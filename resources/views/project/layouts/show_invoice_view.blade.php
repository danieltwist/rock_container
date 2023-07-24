@php
    switch($invoice->status){
        case 'Удален': case 'Не оплачен':
            $class = 'danger';
            break;
        case 'Частично оплачен': case 'Оплачен': case 'Взаимозачет':
            $class = 'success';
            break;
        case 'Ожидается счет от поставщика': case 'Ожидается создание инвойса': case 'Создан черновик инвойса': case 'Ожидается загрузка счета':
            $class = 'warning';
            break;
        case 'Согласована частичная оплата': case 'Счет согласован на оплату':
            $class = 'info';
            break;
        case 'Ожидается оплата':
            $class = 'primary';
            break;
        case 'Счет на согласовании':
            $class = 'secondary';
            break;
        default:
            $class = 'secondary';
    }
@endphp
<div class="row invoice-info">
    <div class="col-sm-6 invoice-col">
        <b>{{ __('invoice.number_in_system') }}:</b> {{ $invoice->id }}
        @if(!is_null($invoice->deleted_at))
            (удален)
        @endif<br>
        <b>{{ __('invoice.added') }}:</b> {{ $invoice->created_at->format('d.m.Y H:i:s') }} {{ $invoice->user_add }}<br>
        <b>{{ __('general.type') }}: </b>
        @if(!is_null($invoice->losses_potential) && (!is_null($invoice->losses_amount)) && (is_null($invoice->losses_confirmed)))
            {{ __('invoice.losses_potential') }} {{ $invoice->losses_amount }}р.
        @elseif(!is_null($invoice->losses_confirmed))
            {{ __('invoice.losses') }} {{ $invoice->losses_amount }}р.
        @else
            @switch($invoice->direction)
                @case('Доход')
                {{ __('general.income') }}
                @break
                @case('Расход')
                {{ __('general.outcome') }}
                @break
            @endswitch
        @endif
        @if ($invoice['client_id']=='' && $invoice->direction =='Доход')
            / {{ __('invoice.outcome_to_supplier') }}<br>
        @elseif ($invoice['supplier_id'] == '' && $invoice->direction =='Доход')
            / {{ __('invoice.outcome_to_client') }}<br>
        @elseif ($invoice['client_id'] == '' && $invoice->direction =='Расход')
            / {{ __('invoice.income_from_supplier') }}<br>
        @elseif ($invoice['supplier_id'] == '' && $invoice->direction =='Расход')
            / {{ __('invoice.income_from_client') }}<br>
        @endif
        <b>{{ __('general.project') }}:</b>
        @if($invoice->project)
            <a href="{{ route('project.show', optional($invoice->project)->id) }}">{{ optional($invoice->project)->name }}</a>
            {{ $invoice->block_id!='' ? ' / '.$invoice->block->name : '' }}
            <br>
        @else
            {{ __('general.deleted') }}<br>
        @endif
        @if ($invoice->direction=='Расход')
            @if(!is_null($invoice->block))
                <b>{{ __('general.stage') }}:</b> {{ $invoice->block->name }}<br>
            @endif
        @endif
        @if(!is_null($invoice->project))
            <b>{{ __('project.project_of_user') }}:</b> {{ $invoice->project->user->name }}<br>
        @endif
        <b>{{ __('invoice.waiting_amount') }}:</b> {{ $invoice->amount }}р.<br>
        @if ($invoice->manager_comment!='')
            <b>{{ __('invoice.manager_comment') }}:</b> {{ $invoice->manager_comment }}<br>
        @endif
        {!! agreeInfo($invoice->id) !!}
        @if ($invoice->accountant_comment!='')
            <br><b>{{ __('invoice.accountant_comment') }}:</b> {{ $invoice->accountant_comment }}
        @endif
        @if ($invoice->director_comment!='')
            <br><b>{{ __('invoice.director_comment') }}:</b> {{ $invoice->director_comment }}
        @endif
    </div>
    <div class="col-sm-6 invoice-col">
        @if ($invoice['supplier_id']=='')
            <b>{{ __('general.client') }}:</b>
            @if($invoice->client)
                <a href="{{ route('client.show', $invoice->client_id) }}">{{ optional($invoice->client)->name }}</a>
            @else
                {{ __('general.client_was_deleted') }}
            @endif
            <br>
            @if (!empty(optional($invoice->client)->requisites))
                <button class="btn btn-primary btn-sm mt-2" type="button"
                        data-toggle="collapse"
                        data-target="#collapseSNPconditions{{ $invoice->id }}"
                        aria-expanded="false"
                        aria-controls="collapseExample">
                    <i class="fa fa-angle-down"></i>
                    {{ __('general.shows_requisites') }}
                </button>
                <div class="collapse mt-2" id="collapseSNPconditions{{ $invoice->id }}">
                    <div class="card card-body">
                        <b>{{ __('general.requisites') }}: </b>
                        @nl2br(optional($invoice->client)->requisites)
                        <br>
                        @if (!empty(optional($invoice->client)->email))
                            <b>E-mail: </b>
                            @nl2br(optional($invoice->client)->email)
                        @endif
                    </div>
                </div>
            @endif
        @endif
        @if ($invoice['client_id']=='')
            <b>{{ __('general.supplier') }}: </b>
            @if($invoice->supplier)
                <a href="{{ route('supplier.show', $invoice->supplier_id) }}">{{ optional($invoice->supplier)->name }}</a>
            @else
                {{ __('general.supplier_was_deleted') }}
            @endif
            <br>
            @if (!empty(optional($invoice->supplier)->requisites))
                <button class="btn btn-primary btn-sm mt-2" type="button"
                        data-toggle="collapse"
                        data-target="#collapseSNPconditions{{ $invoice->id }}"
                        aria-expanded="false"
                        aria-controls="collapseExample">
                    <i class="fa fa-angle-down"></i>
                    {{ __('general.shows_requisites') }}
                </button>
                <div class="collapse mt-2" id="collapseSNPconditions{{ $invoice->id }}">
                    <div class="card card-body">
                        <b>{{ __('general.requisites') }}: </b>
                        @nl2br(optional($invoice->supplier)->requisites)
                        <br>
                        @if (!empty(optional($invoice->supplier)->email))
                            <b>E-mail: </b>
                            @nl2br(optional($invoice->supplier)->email)
                        @endif
                    </div>
                </div>
            @endif
        @endif
        @if (!is_null($invoice->payments_history))
            <div class="mt-2">
                <b>{{ __('invoice.info_about_payment') }}: </b>
                <br>
                @foreach($invoice->payments_history as $payment)
                    {{ \Carbon\Carbon::parse($payment['date'])->format('d.m.Y H:i:s') }} - {{ $payment['user'] }} <br>
                    @if($payment['currency'] == 'RUB')
                        {{ __('invoice.payment_amount') }}: {{ $payment['amount_rub'] }}р.
                    @else
                        {{ __('invoice.payment_amount') }}: {{ $payment['amount_currency'] }}{{ $payment['currency'] }}
                        ({{ $payment['amount_rub'] }}р.)
                    @endif
                    <br>
                @endforeach
            </div>
        @endif
    </div>
    <div class="col-md-12 mt-4">
        @if($_SERVER['REQUEST_URI'] !== '/invoice/'.$invoice->id)
            <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-default btn-sm">
                <i class="fas fa-link"></i> {{ __('invoice.open_on_external_link') }}</a>
        @endif
        <a class="btn btn-default btn-sm copy-to-clipboard cursor-pointer" data-link="{{ route('invoice.show', $invoice->id) }}">
            <i class="fas fa-copy"></i> {{ __('invoice.copy_link') }}
        </a>
        <a class="btn btn-default btn-sm cursor-pointer"
           data-toggle="modal"
           data-type="ajax"
           data-target="#edit_invoice_modal"
           data-invoice-id="{{ $invoice->id }}">
            <i class="fas fa-pencil-alt"></i> {{ __('general.change') }}
        </a>
        <br><br>
        @if ($invoice->additional_info!='')
            <b>{{ __('general.additional_info') }}:</b> {{ $invoice->additional_info }}
        @endif
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card card-outline card-info collapsed-card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">
                            {{ __('invoice.upd') }}
                            {{ $invoice->upd == '' && $invoice->upd_file == '' ? __('invoice.not_uploaded') : __('invoice.uploaded') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="upd_file_{{$invoice->id}}">
                        @include('invoice.ajax.upd_file')
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-warning collapsed-card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">
                            {{ __('invoice.payment_order') }}
                            {{ $invoice->payment_order == '' && $invoice->payment_order_file == '' ? __('invoice.not_uploaded_f') : __('invoice.uploaded_f') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="payment_orders">
                        @include('invoice.ajax.payment_order_file')
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-success collapsed-card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">
                            {{ __('invoice.invoice') }}
                            {{ $invoice->file == '' && $invoice->invoice_file == '' ?  __('invoice.not_uploaded') : __('invoice.uploaded') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="invoice_file_{{ $invoice->id }}">
                        @include('invoice.ajax.invoice_file')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-4">
        <div id="invoice_table_modal_ajax">
            @include('invoice.ajax.invoice_table_modal')
        </div>
    </div>
    @if(!is_null($invoice->losses_potential))
        <div class="col-md-12 mt-4">
            <h5>{{ __('invoice.losses_potential') }}</h5>
            <form action="{{ route('potential_losess_update') }}" method="POST">
                @csrf
                <div class="row">
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('invoice.losses_client_decision') }}</label>
                            <input type="text" class="form-control" name="client_decision"
                                   value="{{ $client_decision }}"
                                   placeholder="{{ __('invoice.losses_client_decision') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('invoice.losses_client_payment_deadline') }}</label>
                            <input type="text" class="form-control invoice_deadline" name="client_payment_deadline"
                                   value="{{ $client_payment_deadline }}"
                                   placeholder="{{ __('invoice.losses_client_payment_deadline') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('invoice.losses_invoice_id_for_losess_compensation') }}</label>
                            <select class="form-control" name="invoice_id_for_losess_compensation"
                                    id="invoice_id_for_losess_compensation"
                                    data-placeholder="{{ __('invoice.losses_invoice_id_for_losess_compensation') }}">
                                <option value="">{{ __('invoice.losses_wont_pay') }}</option>
                                @foreach($invoices as $income_invoice)
                                    <option
                                        value="{{ $income_invoice->id }}" {{ $income_invoice_id == $income_invoice->id ? 'selected' : '' }}>{{ $income_invoice->direction }}
                                        №{{$income_invoice->id}} {{ __('general.from') }} {{$income_invoice->created_at}} {{ __('general.for') }}
                                        @if (!is_null($income_invoice->supplier_id))
                                            {{ optional($income_invoice->supplier)->name }}
                                        @elseif (!is_null($income_invoice->client_id))
                                            {{ optional($income_invoice->client)->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" id="confirm_losses_div">
                        <div class="form-group clearfix">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="confirm_losess"
                                       name="confirm_losess" {{ !is_null($invoice->losses_confirmed) ? 'checked' : '' }}>
                                <label for="confirm_losess">{{ __('invoice.confirm_losess') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary float-right submit_from_invoice_modal"
                        data-action='{"hide_modal":{"id": "view_invoice"}}'>
                    {{ __('invoice.confirm_losess') }}
                </button>
            </form>
        </div>
    @endif
    @if(in_array($current_user_id, $agree_invoice_users))
        @if ($invoice->status!='Оплачен' && $invoice->direction == 'Расход')
            <div class="col-md-12 mt-4">
                <h5>{{ __('invoice.agree_invoice') }}</h5>
                <form action="{{ route('agree_invoice_rc') }}" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <div class="form-group">
                        <label>{{ __('invoice.comment_for_accountant') }}</label>
                        <textarea class="form-control" rows="3" name="director_comment"
                                  placeholder="{{ __('invoice.comment_for_accountant') }}">{{ $invoice->director_comment }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('invoice.set_invoice_status') }}</label>
                        <select class="form-control" name="status">
                            <option value="Счет согласован на оплату">{{ __('invoice.status_agreed') }}</option>
                            <option value="Согласована частичная оплата">{{ __('invoice.status_part_agreed') }}</option>
                            <option value="Счет на согласовании">{{ __('invoice.status_agree') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('invoice.sub_status') }}</label>
                        <select class="form-control" name="sub_status">
                            @foreach(['Без дополнительного статуса', 'Срочно', 'Взаимозачет', 'Отложен'] as $sub_status)
                                <option value="{{ $sub_status }}" {{ $sub_status == $invoice->sub_status ? 'selected' : '' }}>
                                    @include('invoice.status_switch', ['status' => $sub_status])
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary float-right submit_from_invoice_modal"
                            data-action='{"hide_modal":{"id": "view_invoice"}}'>
                        {{ __('invoice.agree_invoice') }}
                    </button>
                </form>
            </div>
        @endif
    @endif
    @can ('pay invoices')
        @if($invoice->direction == 'Расход')
            @if (in_array($invoice->status, ['Счет согласован на оплату', 'Согласована частичная оплата', 'Частично оплачен', 'Ожидается оплата', 'Взаимозачет']))
                <div class="col-md-12 mt-4">
                    <h5>{{ __('invoice.pay_invoice_outcome') }}</h5>
                    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="pay_invoice_outcome">
                        @php
                            $currency = $invoice->currency;
                            if ($currency != 'RUB'){
                                if($currency == 'USD'){
                                    $rate_minus_one = $rates->usd_divided;
                                }
                                else {
                                    $rate_minus_one = $rates->cny_divided;
                                }
                                $total_price_in_rubles = round($invoice->amount_in_currency * $rate_minus_one, 2);
                            }
                            else{
                                $total_price_in_rubles = round($invoice->amount, 2);
                            }
                        @endphp
                        @if ($currency != 'RUB')
                            <div class="form-group">
                                <label>{{ __('general.currency') }}</label>
                                <select class="form-control" name="currency" id="invoice_currency"
                                        data-placeholder="{{ __('general.currency') }}" style="width: 100%;" required>
                                    <option value="RUB" data-currency-rate="1"
                                        {{ $invoice->currency == 'RUB' ? 'selected' : '' }}>{{ __('general.ruble') }}
                                    </option>
                                    <option value="USD"
                                            data-currency-rate="{{ $rates->USD }}"
                                            data-divided="{{ $rates->usd_divided }}"
                                            data-ratio="{{ $rates->usd_ratio }}"
                                        {{ $invoice->currency == 'USD' ? 'selected' : '' }}>
                                        {{ __('general.usd') }}, {{ __('general.cb_rate') }} {{ $rates->USD }}
                                    </option>
                                    <option value="CNY"
                                            data-currency-rate="{{ $rates->CNY }}"
                                            data-divided="{{ $rates->cny_divided }}"
                                            data-ratio="{{ $rates->cny_ratio }}"
                                        {{ $invoice->currency == 'CNY' ? 'selected' : '' }}>{{ __('general.cny') }}, {{ __('general.cb_rate') }} {{ $rates->CNY }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" id="invoice_rate_div">
                                <label>{{ __('general.cb_rate_corrected') }}</label>
                                <input class="form-control"
                                       name="rate_income_date"
                                       type="text"
                                       id="invoice_rate"
                                       placeholder="{{ __('general.cb_rate_corrected') }}"
                                       value="{{ $rate_minus_one }}">
                            </div>
                            <div class="form-group" id="invoice_amount_in_currency_div">
                                <label>{{ __('general.amount_in_currency_actual') }}</label>
                                <input class="form-control rate_input"
                                       name="amount_in_currency_actual"
                                       type="text"
                                       id="invoice_amount_in_currency"
                                       placeholder="{{ __('general.amount_in_currency_actual') }}"
                                       value="{{ $invoice->amount_in_currency }}">
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="amount_actual">{{ __('invoice.amount_actual_in_rubles') }}</label>
                            <input type="text" class="form-control rate_input"
                                   name="amount_actual"
                                   id="invoice_total_price_in_rub"
                                   placeholder="{{ __('invoice.amount_actual_in_rubles') }}"
                                   inputmode="numeric"
                                   value="{{ number_format($total_price_in_rubles, 2, '.', '') }}" required>
                        </div>
                        @if ($currency != 'RUB')
                            <div class="form-group">
                                <label for="amount_actual">{{ __('invoice.this_payment_amount_in_currency') }}</label>
                                <input type="text" class="form-control rate_input"
                                       id="this_invoice_payment_in_currency"
                                       name="amount_in_currency_income_date"
                                       placeholder="{{ __('invoice.this_payment_amount_in_currency') }}"
                                       value="{{ $invoice->amount_in_currency }}" required>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="amount_actual">{{ __('invoice.this_payment_amount_in_rub') }}</label>
                            <input type="text" class="form-control rate_input"
                                   id="this_invoice_payment_in_rubles"
                                   name="amount_income_date"
                                   placeholder="{{ __('invoice.this_payment_amount_in_rub') }}"
                                   value="{{ number_format($total_price_in_rubles, 2, '.', '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('invoice.status') }}</label>
                            <select class="form-control" name="status" required>
                                <option value="Оплачен">{{ __('invoice.status_paid') }}</option>
                                <option value="Частично оплачен">{{ __('invoice.status_part_paid') }}</option>
                                <option value="Взаимозачет">{{ __('invoice.sub_status_compensation') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('general.note') }}</label>
                            <textarea class="form-control" rows="3" name="accountant_comment"
                                      placeholder="{{ __('invoice.note_about_this_payment') }}">{{ $invoice->accountant_comment }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary float-right submit_from_invoice_modal"
                                data-action='{"hide_modal":{"id": "view_invoice"}}'>
                            {{ __('invoice.pay_invoice_outcome') }}
                        </button>
                    </form>
                </div>
            @endif
        @endif
        @if($invoice->direction == 'Доход')
            @if (in_array($invoice->status, ['Счет согласован на оплату', 'Согласована частичная оплата', 'Частично оплачен', 'Ожидается оплата', 'Взаимозачет']))
                <div class="col-md-12 mt-4">
                    <h5>{{ __('invoice.pay_invoice_outcome') }}</h5>
                    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="pay_invoice_income">
                        @php
                            $currency = $invoice->currency;
                            if ($currency != 'RUB'){
                                if($currency == 'USD'){
                                    $rate_minus_one = $rates->usd_divided;
                                }
                                else {
                                    $rate_minus_one = $rates->cny_divided;
                                }
                                $total_price_in_rubles = round($invoice->amount_in_currency * $rate_minus_one, 2);
                            }
                            else{
                                $total_price_in_rubles = round($invoice->amount, 2);
                            }
                        @endphp
                        @if ($currency != 'RUB')
                            <div class="form-group">
                                <label>{{ __('general.currency') }}</label>
                                <select class="form-control" name="currency" id="invoice_currency"
                                        data-placeholder="{{ __('general.currency') }}" style="width: 100%;" required>
                                    <option value="RUB" data-currency-rate="1"
                                        {{ $invoice->currency == 'RUB' ? 'selected' : '' }}>{{ __('general.ruble') }}
                                    </option>
                                    <option value="USD"
                                            data-currency-rate="{{ $rates->USD }}"
                                            data-divided="{{ $rates->usd_divided }}"
                                            data-ratio="{{ $rates->usd_ratio }}"
                                        {{ $invoice->currency == 'USD' ? 'selected' : '' }}>
                                        {{ __('general.usd') }}, {{ __('general.cb_rate') }} {{ $rates->USD }}
                                    </option>
                                    <option value="CNY"
                                            data-currency-rate="{{ $rates->CNY }}"
                                            data-divided="{{ $rates->cny_divided }}"
                                            data-ratio="{{ $rates->cny_ratio }}"
                                        {{ $invoice->currency == 'CNY' ? 'selected' : '' }}>
                                        {{ __('general.cny') }}, {{ __('general.cb_rate') }} {{ $rates->CNY }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" id="invoice_rate_div">
                                <label>{{ __('general.cb_rate_corrected') }}</label>
                                <input class="form-control"
                                       name="rate_income_date"
                                       type="text"
                                       id="invoice_rate"
                                       placeholder="{{ __('general.cb_rate_corrected') }}"
                                       value="{{ $rate_minus_one }}">
                            </div>
                            <div class="form-group" id="invoice_amount_in_currency_div">
                                <label>{{ __('invoice.paid_by_client_in_currency') }}</label>
                                <input class="form-control rate_input"
                                       name="amount_in_currency_income_date"
                                       type="text"
                                       id="invoice_amount_in_currency"
                                       placeholder="{{ __('invoice.paid_by_client_in_currency') }}"
                                       value="{{ $invoice->amount_in_currency }}">
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="amount_actual">{{ __('invoice.this_payment_amount_in_rub') }}</label>
                            <input type="text" class="form-control rate_input"
                                   name="amount_income_date"
                                   id="invoice_total_price_in_rub"
                                   placeholder="{{ __('invoice.this_payment_amount_in_rub') }}"
                                   value="{{ number_format($total_price_in_rubles, 2, '.', '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Статус счета</label>
                            <select class="form-control" name="status" required>
                                <option value="Оплачен">{{ __('invoice.status_paid') }}</option>
                                <option value="Частично оплачен">{{ __('invoice.status_part_paid') }}</option>
                                <option value="Взаимозачет">{{ __('invoice.sub_status_compensation') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ __('general.note') }}</label>
                            <textarea class="form-control" rows="3" name="accountant_comment"
                                      placeholder="{{ __('invoice.note_about_this_payment') }}">{{ $invoice->accountant_comment }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary float-right submit_from_invoice_modal"
                                data-action='{"hide_modal":{"id": "view_invoice"}}'>
                            {{ __('invoice.pay_invoice_outcome') }}
                        </button>
                    </form>
                </div>
@endif
@endif
@endcan
