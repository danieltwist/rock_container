<?php

namespace App\Http\Controllers\Invoice;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Filters\InvoiceFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\ActionRecording;
use App\Models\Application;
use App\Models\Client;
use App\Models\CurrencyRate;
use App\Models\ExpenseType;
use App\Models\Invoice;
use App\Models\IncomeType;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Project;

class InvoiceController extends Controller
{
    use FinanceTrait;

    public function index(InvoiceFilter $request)
    {
        return view('invoice.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $project = \App\Models\Project::find($request->project_id);

        if ($project->active == '0' && $project->paid == 'Оплачен') {
            $losses = true;
        } else {
            $losses = false;
        }

        if ($request->action == 'create_new_expense') {

            $new_invoice = new Invoice();

            $new_invoice->status = 'Ожидается счет от поставщика';
            $new_invoice->direction = 'Расход';
            $new_invoice->block_id = $request->block_id;
            $new_invoice->supplier_id = $request->supplier_id;
            $new_invoice->project_id = $request->project_id;
            $new_invoice->application_id = $request->application_id;
            $new_invoice->currency = $request->currency;
            $new_invoice->rate_out_date = $request->rate_out_date;
            $new_invoice->amount_in_currency = $request->amount_in_currency;
            $new_invoice->amount = $request->amount;
            $new_invoice->deadline = $request->deadline;
            $new_invoice->additional_info = $request->additional_info;
            $new_invoice->expense_category = $request->expense_category;
            $new_invoice->expense_type = $request->expense_type;
            $new_invoice->income_category = $request->income_category;
            $new_invoice->income_type = $request->income_type;
            $new_invoice->user_add = Auth::user()->name;
            $new_invoice->user_id = Auth::user()->id;
            if ($losses) {
                $new_invoice->losses_potential = [
                    'client_decision' => '',
                    'client_payment_deadline' => '',
                    'income_invoice_id' => ''
                ];
            }

            if(isset($request->hide_comment))
                $new_invoice->hide_comment = 1;

            $new_invoice->save();

            if (!is_null($new_invoice->losses_potential)) $this->updateInvoiceLosses($new_invoice, '');

            $this->updateProjectFinance($request->project_id);

            return redirect()->back()->withSuccess(__('invoice.create_new_expense_successfully'));
        }

        if ($request->action == 'create_new_finance') {

            $new_invoice = new Invoice();

            if ($request->direction == 'Клиенту') {
                $new_invoice->client_id = $request->client_id;
                $client = Client::find($request->client_id);
            }

            if ($request->direction == 'Поставщику') {
                $new_invoice->supplier_id = $request->supplier_id;
                $supplier = Supplier::find($request->supplier_id);
            }

            if ($request->type == 'Расход') {

                isset($request->agree_without_invoice) ? $status = 'Счет на согласовании' : $status = 'Ожидается счет от поставщика';

                $message = __('invoice.outcome_added_successfully');

                if ($losses) {
                    $new_invoice->losses_potential = [
                        'client_decision' => '',
                        'client_payment_deadline' => '',
                        'income_invoice_id' => ''
                    ];
                }

                $new_invoice->expense_category = $request->expense_category;
                $new_invoice->expense_type = $request->expense_type;

            }
            else  {
                if ($request->direction == 'Клиенту') {
                    $client->country == 'Россия' ? $status = 'Ожидается загрузка счета' : $status = 'Ожидается создание инвойса';
                } else {
                    $supplier->country == 'Россия' ? $status = 'Ожидается загрузка счета' : $status = 'Ожидается создание инвойса';
                }
                $new_invoice->income_category = $request->income_category;
                $new_invoice->income_type = $request->income_type;

                $message = __('invoice.income_added_successfully');
            }

            $new_invoice->status = $status;
            $new_invoice->direction = $request->type;

            $new_invoice->currency = $request->currency;
            $new_invoice->rate_out_date = $request->rate_out_date;
            $new_invoice->amount_in_currency = $request->amount_in_currency;
            $new_invoice->deadline = $request->deadline;
            $new_invoice->project_id = $request->project_id;
            $new_invoice->application_id = $request->application_id;
            $new_invoice->amount = $request->amount;
            $new_invoice->additional_info = $request->additional_info;
            $new_invoice->user_add = Auth::user()->name;
            $new_invoice->user_id = Auth::user()->id;

            if(isset($request->hide_comment))
                $new_invoice->hide_comment = 1;

            $new_invoice->save();

            if (!is_null($new_invoice->losses_potential)) $this->updateInvoiceLosses($new_invoice, '');

            $this->updateProjectFinance($request->project_id);

            if ($request->type == 'Доход') {
                if ($request->direction == 'Клиенту' && $client->country != 'Россия') {
                    $message = __('invoice.income_added_successfully_create_invoice');
                } elseif ($request->direction == 'Поставщику' && $supplier->country != 'Россия') {
                    $message = __('invoice.income_added_successfully_create_invoice');
                }

            }

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => $message,
                'table_row' => $this->loadTableRow($new_invoice->id)
            ]);

        }

    }

    public function show(Invoice $invoice)
    {
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $client_decision = '';
        $client_payment_deadline = '';
        $income_invoice_id = '';

        if (!is_null($invoice->losses_potential)) {
            $client_decision = $invoice->losses_potential['client_decision'];
            $client_payment_deadline = $invoice->losses_potential['client_payment_deadline'];
            $income_invoice_id = $invoice->losses_potential['income_invoice_id'];
        }

        $difference = $this->getInvoiceExchangeDifference($invoice);
        $exchange_difference = $difference['difference'];
        $average_exchange_rate = $difference['average_exchange_rate'];

        return view('invoice.show', [
            'invoice' => $invoice,
            'invoices' => Invoice::where('direction', 'Доход')->where('project_id', $invoice->project_id)->get(),
            'rates' => $currency_rates,
            'client_decision' => $client_decision,
            'client_payment_deadline' => $client_payment_deadline,
            'income_invoice_id' => $income_invoice_id,
            'agree_invoice_users' => unserialize(Setting::where('name', 'agree_invoice_users')->first()->toArray()['value']),
            'exchange_difference' => $exchange_difference,
            'average_exchange_rate' => $average_exchange_rate
        ]);
    }

    public function edit($id)
    {
        $invoice = Invoice::find($id);

        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        return view('invoice.edit', [
            'invoice' => $invoice,
            'rates' => $currency_rates,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($request->action == 'upload_payment_order_file') {

            if ($request->hasFile('payment_order')) {

                $folder = getFolderUploadInvoice($invoice, 'payment_order');

                $file = renameBeforeUpload($request->payment_order->getClientOriginalName());

                if ($invoice->payment_order_file != '') {
                    $key = array_key_last($invoice->payment_order_file);
                    $key = $key + 1;
                    $filename = $request->payment_order->storeAs($folder, $invoice->id.'-'.$key.'-'.$file);
                } else
                    $filename = $request->payment_order->storeAs($folder, $invoice->id.'-'.$file);

                $payment_order_file = $invoice->payment_order_file;

                $payment_order_file [] = [
                    'filename' => $filename,
                    'amount' => $request->amount,
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'user' => Auth::user()->name
                ];

                $invoice->payment_order_file = $payment_order_file;

                $invoice->save();

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('invoice.payment_order_uploaded_successfully'),
                    'ajax' => view('invoice.ajax.payment_order_file', [
                        'invoice' => $invoice
                    ])->render()
                ]);

            } else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('general.first_choose_file'),
                    'ajax' => view('invoice.ajax.payment_order_file', [
                        'invoice' => $invoice
                    ])->render()
                ]);
            }

        }

        if ($request->action == 'upload_invoice_file') {

            if ($request->hasFile('invoice_file')) {

                $folder = getFolderUploadInvoice($invoice, 'invoice');

                $file = renameBeforeUpload($request->invoice_file->getClientOriginalName());

                if ($invoice->invoice_file != '') {
                    $key = array_key_last($invoice->invoice_file);
                    $key = $key + 1;
                    $filename = $request->invoice_file->storeAs($folder, $invoice->id.'-'.$key.'-'.$file);
                } else
                    $filename = $request->invoice_file->storeAs($folder, $invoice->id.'-'.$file);

                $invoice_file = $invoice->invoice_file;

                $invoice_file [] = [
                    'filename' => $filename,
                    'amount' => $request->amount,
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'user' => Auth::user()->name
                ];

                $invoice->invoice_file = $invoice_file;

                if($invoice->direction == 'Доход'){
                    in_array($invoice->status, ['Счет согласован на оплату', 'Согласована частичная оплата', 'Оплачен', 'Частично оплачен'])
                        ? $status = $invoice->status
                        : $status = 'Ожидается оплата';
                }
                else {
                    in_array($invoice->status, ['Счет согласован на оплату', 'Согласована частичная оплата', 'Оплачен', 'Частично оплачен'])
                        ? $status = $invoice->status
                        : $status = 'Счет на согласовании';
                }

                $invoice->status = $status;

                $invoice->save();

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('invoice.invoice_file_uploaded_successfully'),
                    'ajax' => view('invoice.ajax.invoice_file', [
                        'invoice' => $invoice
                    ])->render()
                ]);

            } else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('general.first_choose_file'),
                    'ajax' => view('invoice.ajax.invoice_file', [
                        'invoice' => $invoice
                    ])->render()
                ]);
            }

        }

        if ($request->action == 'upload_upd_file') {

            if ($request->hasFile('upd_file')) {

                $folder = getFolderUploadInvoice($invoice, 'upd');

                $file = renameBeforeUpload($request->upd_file->getClientOriginalName());

                if ($invoice->upd_file != '') {
                    $key = array_key_last($invoice->upd_file);
                    $key = $key + 1;
                    $filename = $request->upd_file->storeAs($folder, $invoice->id.'-'.$key.'-'.$file);
                } else
                    $filename = $request->upd_file->storeAs($folder, $invoice->id.'-'.$file);

                $upd_file = $invoice->upd_file;

                $upd_file [] = [
                    'filename' => $filename,
                    'amount' => $request->amount,
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'user' => Auth::user()->name
                ];

                $invoice->upd_file = $upd_file;

                $invoice->save();

                $this->notifyAFileUploaded('Загружен УПД к '.$invoice->direction.'у №'.$invoice->id, $invoice->id);

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('invoice.upload_upd_file_uploaded_successfully'),
                    'ajax' => view('invoice.ajax.upd_file', [
                        'invoice' => $invoice
                    ])->render()
                ]);

            } else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('general.first_choose_file'),
                    'ajax' => view('invoice.ajax.upd_file', [
                        'invoice' => $invoice
                    ])->render()
                ]);
            }

        }

        if ($request->action == 'pay_invoice_income') {

            if ($invoice->currency != 'RUB') {
                $invoice->rate_income_date = $request->rate_income_date;
                $invoice->amount_in_currency_income_date = $invoice->amount_in_currency_income_date + $request->amount_in_currency_income_date;
                $paid_amount = $invoice->amount_in_currency_income_date.$invoice->currency.' ('.$request->amount_income_date.'р.)';
            }
            else {
                $paid_amount = $request->amount_income_date.'р.';
            }

            $invoice->amount_income_date = $invoice->amount_income_date + $request->amount_income_date;
            $invoice->status = $request->status;
            $invoice->accountant_comment = $request->accountant_comment;

            $payments_history = $invoice->payments_history;

            $payments_history [] = [
                'date' => Carbon::now()->format('Y-m-d h:i:s'),
                'amount_rub' => $request->amount_income_date,
                'amount_currency' => $request->amount_in_currency_income_date,
                'currency' => $invoice->currency,
                'user' => auth()->user()->name
            ];

            $invoice->payments_history = $payments_history;

            $invoice->save();

            if (!is_null($invoice->losses_used_for_compensation)) $this->updateInvoiceLosses(Invoice::findOrFail($invoice->losses_used_for_compensation), $invoice->id);

            $this->updateProjectFinance($invoice->project_id);

            $this->notifyInvoicePaid($invoice, $paid_amount);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('invoice.pay_invoice_income_successfully')
            ]);
        }

        if ($request->action == 'pay_invoice_outcome') {

            if ($invoice->currency != 'RUB') {
                $invoice->rate_income_date = $request->rate_income_date;
                $invoice->amount_in_currency_income_date = $invoice->amount_in_currency_income_date + $request->amount_in_currency_income_date;
                $paid_amount = $invoice->amount_in_currency_income_date.$invoice->currency.' ('.$request->amount_income_date.'р.)';
            }
            else {
                $paid_amount = $request->amount_income_date.'р.';
            }

            $invoice->amount_actual = $request->amount_actual;
            $invoice->amount_in_currency_actual = $request->amount_in_currency_actual;

            $invoice->amount_income_date = $invoice->amount_income_date + $request->amount_income_date;
            $invoice->status = $request->status;
            $invoice->accountant_comment = $request->accountant_comment;

            $payments_history = $invoice->payments_history;

            $payments_history [] = [
                'date' => Carbon::now()->format('Y-m-d h:i:s'),
                'amount_rub' => $request->amount_income_date,
                'amount_currency' => $request->amount_in_currency_income_date,
                'currency' => $invoice->currency,
                'user' => auth()->user()->name
            ];

            $invoice->payments_history = $payments_history;

            $invoice->save();

            $this->updateProjectFinance($invoice->project_id);

            $this->notifyInvoicePaid($invoice, $paid_amount);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('invoice.pay_invoice_income_successfully')
            ]);

        }

        if ($request->action == 'update_invoice') {

            $invoice_array = $invoice->toArray();
            $user = auth()->user();

            if($request->direction == 'Поставщику'){
                $client_id = null;
                $supplier_id = $request->supplier_id;
            } else {
                $client_id = $request->client_id;
                $supplier_id = null;
            }

            $old_project = $invoice->project_id;
            $new_project = $request->project_id;

            isset($request->hide_comment) ?  $hide_comment = 1 : $hide_comment = 0;
            isset($request->created_at) ?  $created_at = $request->created_at : $created_at = $invoice->created_at;

            $invoice->update([
                'amount' => $request->amount,
                'client_id' => $client_id,
                'supplier_id' => $supplier_id,
                'currency' => $request->currency,
                'project_id' => $new_project,
                'application_id' => $request->application_id,
                'deadline' => $request->deadline,
                'rate_out_date' => $request->rate_out_date,
                'rate_income_date' => $request->rate_income_date,
                'amount_in_currency' => $request->amount_in_currency,
                'amount_actual' => $request->amount_actual,
                'amount_in_currency_actual' => $request->amount_in_currency_actual,
                'amount_income_date' => $request->amount_income_date,
                'amount_in_currency_income_date' => $request->amount_in_currency_income_date,
                'amount_sale_date' => $request->amount_sale_date,
                'rate_sale_date' => $request->rate_sale_date,
                'status' => $request->status,
                'additional_info' => $request->additional_info,
                'expense_category' => $request->expense_category,
                'expense_type' => $request->expense_type,
                'income_category' => $request->income_category,
                'income_type' => $request->income_type,
                'edited' => '1',
                'hide_comment' => $hide_comment,
                'created_at' => $created_at
            ]);

            $this->updateProjectFinance($invoice->project_id);

            $new_record = new ActionRecording();

            $new_record->model = 'invoice';
            $new_record->model_id = $invoice->id;
            $new_record->object = 'Счет №' . $invoice->id;
            $new_record->text = 'Редактирование счета';
            $new_record->before_edit = serialize($invoice_array);
            $new_record->user_id = $user->id;

            $new_record->save();

            if($old_project != $new_project){
                $this->updateProjectFinance($old_project);
                $this->moveInvoiceToAnotherProject($invoice);
            }

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('invoice.update_invoice_successfully')
            ]);

        }

    }

    public function destroy(Invoice $invoice)
    {
        if (checkUploadedFileInvoice($invoice->id)['file']) {
            Storage::delete(unserialize($invoice->file)['filename']);
        } else Storage::delete($invoice->file);

        $invoice->delete();

        $this->updateProjectFinance($invoice->project_id);

        return redirect()->back()->withSuccess(__('invoice.removed_successfully'));
    }

    public function get_invoice_by_id($id)
    {
        $invoice = Invoice::withTrashed()->find($id);
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $client_decision = '';
        $client_payment_deadline = '';
        $income_invoice_id = '';

        $difference = $this->getInvoiceExchangeDifference($invoice);
        $exchange_difference = $difference['difference'];
        $average_exchange_rate = $difference['average_exchange_rate'];

        if (!is_null($invoice->losses_potential)) {
            $client_decision = $invoice->losses_potential['client_decision'];
            $client_payment_deadline = $invoice->losses_potential['client_payment_deadline'];
            $income_invoice_id = $invoice->losses_potential['income_invoice_id'];
        }

        return view('project.layouts.show_invoice_view', [
            'invoice' => $invoice,
            'invoices' => Invoice::where('direction', 'Доход')->where('project_id', $invoice->project_id)->get(),
            'rates' => $currency_rates,
            'client_decision' => $client_decision,
            'client_payment_deadline' => $client_payment_deadline,
            'income_invoice_id' => $income_invoice_id,
            'agree_invoice_users' => unserialize(Setting::where('name', 'agree_invoice_users')->first()->toArray()['value']),
            'exchange_difference' => $exchange_difference,
            'average_exchange_rate' => $average_exchange_rate
        ]);

    }

    public function edit_invoice_by_id($id)
    {
        $invoice = Invoice::withTrashed()->find($id);
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $invoice->client_id != '' ? $company_type = 'client' : $company_type = 'supplier';

        $invoice->currency != 'RUB' ? $class = '' : $class = 'd-none';

        return [
            'info' => view('invoice.modal.edit_invoice_modal_view', [
                'invoice' => $invoice,
                'client_id' => $invoice->client_id,
                'supplier_id' => $invoice->supplier_id,
                'rates' => $currency_rates,
                'company_type' => $company_type,
                'class' => $class,
                'expense_types' => ExpenseType::all(),
                'income_types' => IncomeType::all(),
                'projects' => \App\Models\Project::all(),
                'applications' => Application::all(),
                'clients' => Client::all(),
                'suppliers' => Supplier::all()
            ])->render(),
            'payments_history' => view('invoice.modal.edit_invoice_payments_history', [
                'invoice' => $invoice
            ])->render()
        ];
    }

    public function get_invoice_changes($id)
    {

        $invoice = Invoice::find($id);
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $changes = $invoice->actions;

        foreach ($changes as $change) {

            $invoice = new Invoice();
            $invoice = $invoice->fill(unserialize($change->before_edit));
            $change->invoice = $invoice;

        }

        return view('project.layouts.show_invoice_changes', [
            'changes' => $changes,
            'rates' => $currency_rates
        ]);

    }

    public function getOutInvoices()
    {

        $invoices = Invoice::where('direction', 'Доход')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('invoice.index', [
            'invoices' => $invoices,
            'title' => __('invoice.outcome_invoices')
        ]);

    }

    public function getInInvoices()
    {

        $invoices = Invoice::where('direction', 'Расход')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('invoice.index', [
            'invoices' => $invoices,
            'title' => __('invoice.income_invoices')
        ]);

    }

    public function getAgreedInvoices()
    {

        $invoices = Invoice::where('status', 'Счет согласован на оплату')
            ->orWhere('status', 'Согласована частичная оплата')->paginate(30);

        return view('invoice.index', [
            'invoices' => $invoices,
            'title' => __('invoice.agreed_invoice_title')
        ]);

    }

    public function getInvoicesForApproval()
    {
        $invoices = Invoice::where('status', 'Счет на согласовании')->orderBy('created_at', 'desc')->paginate(30);

        return view('invoice.index', [
            'invoices' => $invoices,
            'title' => __('invoice.on_approval_title')
        ]);
    }

    public function sortByDate(Request $request)
    {
        $range = explode(' - ', $request->data_range);
        $invoices = [];
        switch ($request->type) {
            case 'Ожидается согласование':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('status', 'Счет на согласовании')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'Согласованы на оплату':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('status', 'Счет согласован на оплату')
                    ->orWhere('status', 'Согласована частичная оплата')
                    ->orderBy('created_at', 'desc')->get();
                break;
            case 'Входящие счета':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('direction', 'Расход')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'Исходящие счета':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('direction', 'Доход')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'Мои счета':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('user_add', auth()->user()->name)
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'Оплаченные счета':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->whereIn('status', ['Оплачен', 'Частично оплачен'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'В процессе согласования':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->whereNotIn('status', ['Оплачен', 'Частично оплачен', 'Счет согласован на оплату', 'Ожидается оплата'])
                    ->where(function ($query) {
                        $query->where('agree_1', 'like', '%Счет согласован на оплату%')
                            ->orWhere('agree_2', 'like', '%Счет согласован на оплату%')
                            ->orWhere('agree_1', 'like', '%Согласована частичная оплата%')
                            ->orWhere('agree_2', 'like', '%Согласована частичная оплата%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 'Все счета':
                $invoices = Invoice::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->orderBy('created_at', 'desc')->get();
                break;
        }

        foreach ($invoices as $invoice) {
            $this->giveClass($invoice);
        }

        return view('project.layouts.invoices_table', [
            'invoices' => $invoices
        ]);

    }

    public function removeUpdAjax($id, $key)
    {
        $invoice = Invoice::find($id);

        $upd_file = $invoice->upd_file;
        if ($upd_file[$key]['filename'] != '') {
            Storage::delete($upd_file[$key]['filename']);
        }

        unset($upd_file[$key]);
        if (!empty($upd_file)) {
            $invoice->update([
                'upd_file' => $upd_file
            ]);
        } else {
            $invoice->update([
                'upd_file' => null
            ]);
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('invoice.upd_removed_successfully'),
            'view' => view('invoice.ajax.upd_file', [
                'invoice' => $invoice
            ])->render()
        ]);
    }

    public function removePaymentOrderAjax($id, $key)
    {
        $invoice = Invoice::find($id);

        $payment_order_file = $invoice->payment_order_file;
        if ($payment_order_file[$key]['filename'] != '') {
            Storage::delete($payment_order_file[$key]['filename']);
        }

        unset($payment_order_file[$key]);
        if (!empty($payment_order_file)) {
            $invoice->update([
                'payment_order_file' => $payment_order_file
            ]);
        } else {
            $invoice->update([
                'payment_order_file' => null
            ]);
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('invoice.pp_removed_successfully'),
            'view' => view('invoice.ajax.payment_order_file', [
                'invoice' => $invoice
            ])->render()
        ]);
    }

    public function removeInvoiceAjax($id, $key)
    {
        $invoice = Invoice::find($id);

        $invoice_file = $invoice->invoice_file;
        if ($invoice_file[$key]['filename'] != '') {
            Storage::delete($invoice_file[$key]['filename']);
        }

        if(in_array($invoice->status, ['Счет согласован на оплату', 'Согласована частичная оплата', 'Оплачен', 'Частично оплачен'])){
            $status = $invoice->status;
        }
        else {
            if ($invoice->direction == 'Расход') {
                $status = 'Ожидается счет от поставщика';
            }
            else {
                if (!is_null($invoice->client_id)) {
                    $invoice->client->country == 'Россия' ? $status = 'Ожидается загрузка счета' : $status = 'Ожидается создание инвойса';

                }
                elseif (!is_null($invoice->supplier_id)) {
                    $invoice->supplier->country == 'Россия' ? $status = 'Ожидается загрузка счета' : $status = 'Ожидается создание инвойса';

                }

            }
        }

        unset($invoice_file[$key]);
        if (!empty($invoice_file)) {
            $invoice->update([
                'invoice_file' => $invoice_file,
            ]);
        } else {
            $invoice->update([
                'invoice_file' => null,
                'status' => $status
            ]);
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('invoice.invoice_removed_successfully'),
            'view' => view('invoice.ajax.invoice_file', [
                'invoice' => $invoice
            ])->render()
        ]);
    }

    public function uploadInvoiceAjax($id, Request $request)
    {
        if ($request->hasFile('file')) {

            $invoice = Invoice::find($id);

            $folder = getFolderUploadInvoice($invoice, 'invoice');
            $file = renameBeforeUpload($request->file->getClientOriginalName());

            if ($invoice->invoice_file != '') {
                $key = array_key_last($invoice->invoice_file);
                $key = $key + 1;
                $filename = $request->file->storeAs($folder, $invoice->id.'-'.$key.'-'.$file);
            } else
                $filename = $request->file->storeAs($folder, $invoice->id.'-'.$file);

            $invoice_file = $invoice->invoice_file;

            $invoice_file [] = [
                'filename' => $filename,
                'amount' => 'Не указана',
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'user' => Auth::user()->name
            ];

            $invoice->invoice_file = $invoice_file;

            if($invoice->direction == 'Доход'){
                $status = 'Ожидается оплата';
            }
            else {
                in_array($invoice->status, ['Счет согласован на оплату', 'Согласована частичная оплата', ''])
                    ? $status = $invoice->status
                    : $status = 'Счет на согласовании';
            }

            $invoice->status = $status;

            $invoice->save();

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('invoice.invoice_uploaded_successfully')
            ]);

        }
        else {
            return response()->json([
                'bg-class' => 'bg-danger',
                'from' => 'Система',
                'message' => __('general.first_choose_file')
            ]);
        }
    }

    public function loadTableRow($id)
    {

        $invoice = Invoice::find($id);

        $this->giveClass($invoice);

        $id = $invoice->id;

        $info = view('project.invoices_table.info', [
            'invoice' => $invoice
        ])->render();

        $amount = view('project.invoices_table.amount', [
            'invoice' => $invoice
        ])->render();

        $paid = view('project.invoices_table.paid', [
            'invoice' => $invoice
        ])->render();

        $status = view('project.invoices_table.'.config('app.prefix_view').'status', [
            'invoice' => $invoice
        ])->render();

        $actions = view('project.invoices_table.actions', [
            'invoice' => $invoice
        ])->render();

        return array(
            $id,
            $info,
            $amount,
            $paid,
            $status,
            $actions
        );
    }

    public function loadModalTableRow($id)
    {

        $invoice = Invoice::find($id);

        return response()->json([
            'modal_table' => view('invoice.ajax.invoice_table_modal', [
                'invoice' => $invoice
            ])->render()
        ]);
    }

    public function deleteRow($id)
    {

        $invoice = Invoice::find($id);

        if(!is_null($invoice)){
            if (checkUploadedFileInvoice($invoice->id)['file']) {
                Storage::delete(unserialize($invoice->file)['filename']);
            } else Storage::delete($invoice->file);

            $invoice->delete();

            $this->updateProjectFinance($invoice->project_id);
        }
        else {
            $invoice = Invoice::withTrashed()->find($id);
            $invoice->forceDelete();
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => $invoice->direction . ' №' . $invoice->id . ' '.__('invoice.was_removed_successfully')
        ]);
    }

    public function giveClass(Invoice $invoice)
    {
        switch ($invoice->status) {
            case 'Удален':
            case 'Не оплачен':
                $invoice->class = 'danger';
                break;
            case 'Частично оплачен':
            case 'Оплачен':
                $invoice->class = 'success';
                break;
            case 'Ожидается счет от поставщика':
            case 'Ожидается создание инвойса':
            case 'Создан черновик инвойса':
            case 'Ожидается загрузка счета':
                $invoice->class = 'warning';
                break;
            case 'Согласована частичная оплата':
            case 'Счет согласован на оплату':
                $invoice->class = 'info';
                break;
            case 'Ожидается оплата':
                $invoice->class = 'primary';
                break;
            case 'Счет на согласовании':
                $invoice->class = 'secondary';
                break;
            default:
                $invoice->class = 'secondary';
        }
    }

    public function potentialLossesUpdate(Request $request)
    {

        $invoice = Invoice::find($request->invoice_id);

        if ($invoice->losses_potential['income_invoice_id'] != '' && $request->invoice_id_for_losess_compensation != $invoice->losses_potential['income_invoice_id']) {
            Invoice::findOrFail($invoice->losses_potential['income_invoice_id'])->update([
                'losses_used_for_compensation' => null
            ]);
        }

        $invoice->losses_potential = [
            'client_decision' => $request->client_decision,
            'client_payment_deadline' => $request->client_payment_deadline,
            'income_invoice_id' => $request->invoice_id_for_losess_compensation
        ];
        $invoice->losses_confirmed = $request->confirm_losess;

        $invoice->save();

        $this->updateInvoiceLosses($invoice, $request->invoice_id_for_losess_compensation);

        if ($request->invoice_id_for_losess_compensation != '') {

            Invoice::findOrFail($request->invoice_id_for_losess_compensation)->update([
                'losses_used_for_compensation' => $invoice->id
            ]);
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('invoice.potential_losses_updated_successfully')
        ]);

    }

    public function getAgreedInvoicesAmount(Request $request, InvoiceFilter $filter)
    {
        if($request->data_range != '' && $request->data_range !='Все'){
            $range = explode(' - ', $request->data_range);
            $range_from = $range[0];
            $range_to = $range[1];
        }
        else {
            $range_from = '2000-01-01';
            $range_to = '3000-01-01';
        }

        $invoices = Invoice::filter($filter);

        if($request->direction != ''){
            $invoices->where('direction', $request->direction);
        }

        $invoices = $invoices->whereDate('created_at', '>=', $range_from)
            ->whereDate('created_at', '<=', $range_to)
            ->get();

        $amount_rub = 0;
        $amount_usd = 0;
        $amount_cny = 0;

        foreach ($invoices as $invoice){

            $invoice_amount = $this->getInvoicePaymentBalance($invoice);

            switch ($invoice->currency){
                case 'RUB':
                    $amount_rub += $invoice_amount;
                    break;
                case 'USD':
                    $amount_usd += $invoice_amount;
                    break;
                case 'CNY':
                    $amount_cny += $invoice_amount;
                    break;
            }

        }

        return view('invoice.ajax.agree_invoices_amount', [
            'invoices_count' => $invoices->count(),
            'amount_rub' => $amount_rub,
            'amount_cny' => $amount_cny,
            'amount_usd' => $amount_usd
        ])->render();
    }

    public function notifyAFileUploaded($text, $invoice_id){

        $role = auth()->user()->getRoleNames()->toArray();

        $link = 'invoice/'.$invoice_id;

        if(!in_array('accountant', $role)){

            $accountant_group = User::role('accountant')->pluck('id')->toArray();

            foreach ($accountant_group as $user_id){

                $message = [
                    'from' => 'Система',
                    'to' => $user_id,
                    'text' => $text,
                    'link' => $link,
                    'class' => 'bg-info'
                ];

                event(new NotificationReceived($message));
            }
        }
        else {

            $invoice = Invoice::findOrFail($invoice_id);

            $project = \App\Models\Project::findOrFail($invoice->project_id);

            $to_users = [];

            $project->manager_id == '' ?: $to_users [] = $project->manager_id;
            $project->logist_id == '' ?: $to_users [] = $project->logist_id;

            foreach ($to_users as $user_id){

                $message = [
                    'from' => 'Система',
                    'to' => $user_id,
                    'text' => $text,
                    'link' => $link,
                    'class' => 'bg-info'
                ];

                event(new NotificationReceived($message));
            }


        }

    }

    public function notifyInvoicePaid(Invoice $invoice, $amount){

        $notify_group = User::whereHas('roles', function ($query) {
            $query->where('name', 'director');
        })->get()->pluck('id');

        is_null($invoice->user_id) ?: $notify_group [] = $invoice->user_id;

        $text = $invoice->direction.' №'.$invoice->id.' был оплачен';

        $text .= PHP_EOL .'Проект: '.optional($invoice->project)->name;
        $text .= PHP_EOL .'Контрагент: '.$this->getInvoiceCounterparty($invoice);
        $text .= PHP_EOL .'Сумма: '.$amount;

        foreach ($notify_group as $user_id){
            $notification = [
                'from' => 'Система',
                'to' => $user_id,
                'text' => $text,
                'link' => 'invoice/'.$invoice->id,
                'class' => 'bg-success'
            ];

            $notification['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$notification['link']],
                    ],
                ]
            ];

            $notification['action'] = 'notification';
            $notification_channel = getNotificationChannel($user_id);

            if($notification_channel == 'Система'){
                event(new NotificationReceived($notification));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($notification));
            }
            else {
                event(new NotificationReceived($notification));
                event(new TelegramNotify($notification));
            }
        }

    }

    public function restoreRow($id){

        $invoice = Invoice::withTrashed()->findOrFail($id);
        $invoice->restore();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('Счет ' .$invoice->id. ' был успешно восстановлен')
        ]);
    }

    public function getInvoiceCounterparty(Invoice $invoice){
        if(!is_null($invoice->client_id)){
            $counterparty_name = optional($invoice->client)->name;
        }
        else {
            $counterparty_name = optional($invoice->supplier)->name;
        }

        return $counterparty_name;
    }

    public function moveInvoiceToAnotherProject(Invoice $invoice){

        if(!is_null($invoice->invoice_file)){
            $invoice_file = $invoice->invoice_file;
            foreach ($invoice->invoice_file as $key => $file){
                $filename = last(explode('/', $file['filename']));
                $new_path = getFolderUploadInvoice($invoice, 'invoice').$filename;
                try {
                    Storage::move($file['filename'], $new_path);
                }
                catch (\Exception $e) {
                    continue;
                }

                $invoice_file[$key]['filename'] = $new_path;

                $invoice->update([
                    'invoice_file' => $invoice_file
                ]);
            }
        }

        if(!is_null($invoice->upd_file)){
            $upd_file = $invoice->upd_file;
            foreach ($invoice->upd_file as $key => $file){
                $filename = last(explode('/', $file['filename']));
                $new_path = getFolderUploadInvoice($invoice, 'upd').$filename;
                try {
                    Storage::move($file['filename'], $new_path);
                }
                catch (\Exception $e) {
                    continue;
                }

                $upd_file[$key]['filename'] = $new_path;

                $invoice->update([
                    'upd_file' => $upd_file
                ]);
            }
        }

        if(!is_null($invoice->payment_order_file)){
            $payment_order_file = $invoice->payment_order_file;
            foreach ($payment_order_file as $key => $file){
                $filename = last(explode('/', $file['filename']));
                $new_path = getFolderUploadInvoice($invoice, 'payment_order').$filename;
                try {
                    Storage::move($file['filename'], $new_path);
                }
                catch (\Exception $e) {
                    continue;
                }

                $payment_order_file[$key]['filename'] = $new_path;

                $invoice->update([
                    'payment_order_file' => $payment_order_file
                ]);
            }
        }
    }

}
