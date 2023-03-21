@extends('layouts.project')
@section('title', __('block.work_with_block'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('project.project') }} {{optional(optional($block->project))->name}}
                        - {{ $block->name }}
                        <a href="{{ route('project.show', optional($block->project)->id) }}" class="btn btn-default">
                            {{ __('block.back_to_project_page') }}
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            @if (canWorkWithProject(optional($block->project)->id))
                @can ('work with projects')
                    <div class="row">
                        <div class="col-md-8">
                            <div class="timeline">
                                <div>
                                    <i class="fas fa-cog bg-blue"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">{{ __('block.choose_supplier_for_this_block') }}</h3>
                                        <div class="timeline-body">
                                            @if ($block->supplier_id == '')
                                                <form action="{{ route('block.update', $block->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="choose_supplier">
                                                    <div class="form-group">
                                                        <label>{{ __('general.suppliers') }}</label>
                                                        <select class="form-control select2" name="supplier_id"
                                                                data-placeholder="{{ __('project.select_supplier') }}"
                                                                style="width: 100%;">
                                                            <option></option>
                                                            @foreach($suppliers as $supplier)
                                                                <option
                                                                    value="{{ $supplier->id }}">{{$supplier->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @else
                                                        {{ __('block.chosen_supplier') }}
                                                        - {{ optional($block->supplier)->name }}
                                            @endif
                                        </div>
                                        @if ($block->supplier_id == '')
                                            <div class="timeline-footer">
                                                <button type="submit"
                                                        class="btn btn-primary btn-sm">{{ __('general.save') }}</button>
                                            </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-file-signature bg-info"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">{{ __('block.choose_contract_with_supplier') }}</h3>
                                        <div class="timeline-body">
                                            <form action="{{ route('block.update', $block->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="choose_supplier_contract">
                                                <div class="mt-2">
                                                    @if ($block->supplier_id != '')
                                                        @if ($block->contract_id=='')
                                                            @if (optional($block->supplier)->contracts->isNotEmpty())
                                                                @foreach($block->supplier->contracts as $contract)
                                                                    <div class="form-check">
                                                                        <input type="radio" id="{{ $contract->id  }}"
                                                                               name="contract" class="form-check-input"
                                                                               value="{{ $contract->id }}" {{ $block->contract_id == $contract->id ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                               for="{{ $contract->id }}">
                                                                            №{{ $contract->name }}
                                                                            - {{ __('project.valid_before') }} {{ $contract->date_period }} {{ $contract->additional_info }}
                                                                            <a href="{{ Storage::url($contract->file) }}"
                                                                               download>{{ __('general.show') }}</a>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                {{ __('block.no_contracts_for_this_supplier') }}<br>
                                                                <a href="{{ route('supplier.edit', $block->supplier_id) }}"
                                                                   class="btn btn-primary btn-sm mt-2">
                                                                    {{ __('block.edit_supplier') }}
                                                                </a>
                                                            @endif
                                                        @else
                                                            {{ __('block.chosen_contract_with_supplier') }}
                                                            №{{ optional($block->contract)->name }} {{ optional($block->contract)->additional_info }}
                                                            <br>
                                                            {{ __('project.valid_before') }} {{ optional($block->contract)->date_period }}
                                                            <br>
                                                            <a class="btn btn-primary btn-sm mt-3"
                                                               href="{{ Storage::url(optional($block->contract)->file) }}"
                                                               download>{{ __('general.download_contract') }}</a>
                                                        @endif
                                                    @else
                                                        {{ __('block.first_choose_contract_with_supplier') }}
                                                    @endif
                                                </div>
                                        </div>
                                        <div class="timeline-footer">
                                            @if ($block->supplier_id != '')
                                                @if (($block->contract_id == '') && (optional($block->supplier)->contracts->isNotEmpty()))
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        {{ __('general.save') }}
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <div>
                                    <i class="fas fa-upload bg-green"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header no-border">{{ __('project.upload_application_file') }}</h3>
                                        <div class="timeline-body">
                                            @if ($block->supplier_id == '' || $block->contract_id == '')
                                                {{ __('block.first_choose_contract_and_supplier') }}
                                            @else
                                                @if ($applications->isNotEmpty())
                                                    {{ __('block.uploaded_applications') }}:
                                                    <table class="table table-striped mt-2 datatable_without_search">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 10px">#</th>
                                                            <th>{{ __('general.supplier') }}</th>
                                                            <th>{{ __('general.contract') }}</th>
                                                            <th>{{ __('project.application_file') }}</th>
                                                            @can('remove invoices')
                                                                <th>{{ __('general.removing') }}</th>
                                                            @endcan
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($applications as $application)
                                                            <tr>
                                                                <td>{{ $application->id }}</td>
                                                                <td>{{ optional($application->supplier)->name }}</td>
                                                                <td>
                                                                    {{ optional($application->contract)->name }}
                                                                    - {{ __('project.valid_before') }} {{ optional($application->contract)->date_period }}
                                                                    <br>
                                                                    {{ optional($application->contract)->additional_info }}
                                                                    <br>
                                                                    <a href="{{ Storage::url(optional($application->contract)->file) }}"
                                                                       download>
                                                                        {{ __('general.download_contract') }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-primary btn-sm"
                                                                       href="{{ Storage::url($application->file) }}"
                                                                       download>
                                                                        {{ __('general.download_application') }}
                                                                    </a>
                                                                </td>
                                                                @can('remove invoices')
                                                                    <td>
                                                                        <form class="button-delete-inline"
                                                                              action="{{ route('application.destroy', $application->id) }}"
                                                                              method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                    class="btn btn-danger btn-sm delete-btn">
                                                                                {{ __('project.remove_application') }}
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                @endcan
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                    <br>
                                                @endif
                                                <form action="{{ route('block.update', $block->id) }}" method="post"
                                                      enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="upload_application">
                                                    <div class="form-group">
                                                        <label
                                                            for="application">{{ __('project.select_application_file') }}</label>
                                                        <input type="file" class="form-control-file" name="application"
                                                               required>
                                                    </div>

                                                    @endif
                                                    <div class="timeline-footer">
                                                        @if ($block->supplier_id == '' || $block->contract_id == '')

                                                        @else
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                {{ __('general.upload') }}
                                                            </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-file-invoice-dollar bg-yellow"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">{{ __('block.create_outcome') }}</h3>
                                    <div class="timeline-body">
                                        @if ($block->supplier_id == '')
                                            {{ __('block.first_choose_contract_with_supplier') }}
                                        @else
                                            <form action="{{ route('invoice.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="supplier_id"
                                                       value="{{ $block->supplier_id }}">
                                                <input type="hidden" name="direction" value="Расход">
                                                <input type="hidden" name="project_id"
                                                       value="{{ optional($block->project)->id }}">
                                                <input type="hidden" name="block_id" value="{{ $block->id }}">
                                                <input type="hidden" name="action" value="create_new_expense">
                                                <div class="form-group">
                                                    <select class="form-control" name="currency" id="invoice_currency"
                                                            data-placeholder="Выбери валюту" style="width: 100%;"
                                                            required>
                                                        <option value="RUB"
                                                                data-currency-rate="1">
                                                            Рубль
                                                        </option>
                                                        <option value="USD"
                                                                data-currency-rate="{{ $rates->USD }}"
                                                                data-divided="{{ $rates->usd_divided }}"
                                                                data-ratio="{{ $rates->usd_ratio }}">
                                                            Доллары США, курс ЦБ {{ $rates->USD }}
                                                        </option>
                                                        <option value="CNY"
                                                                data-currency-rate="{{ $rates->CNY }}"
                                                                data-divided="{{ $rates->cny_divided }}"
                                                                data-ratio="{{ $rates->cny_ratio }}">
                                                            Юань, курс ЦБ {{ $rates->CNY }}
                                                        </option>
                                                    </select>
                                                    <label>{{ __('general.currency') }}</label>
                                                </div>
                                                <div class="form-group d-none" id="invoice_rate_div">
                                                    <label>{{ __('general.cb_rate_minus') }} <span
                                                            id="ratio"></span></label>
                                                    <input class="form-control" type="text" name="rate_out_date"
                                                           id="invoice_rate"
                                                           placeholder="{{ __('general.cb_rate_corrected') }}"
                                                           value="1">
                                                </div>
                                                <div class="form-group d-none" id="invoice_amount_in_currency_div">
                                                    <label>{{ __('general.price_in_currency') }}</label>
                                                    <input class="form-control rate_input" type="text"
                                                           id="invoice_amount_in_currency" name="amount_in_currency"
                                                           placeholder="{{ __('general.price_in_currency') }}"
                                                           value="0">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('general.price_in_rubles') }}</label>
                                                    <input class="form-control rate_input"
                                                           id="invoice_total_price_in_rub" type="text" name="amount"
                                                           placeholder="{{ __('general.price_in_rubles') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('project.payment_deadline') }}</label>
                                                    <input type="text" class="form-control date_input" name="deadline"
                                                           placeholder="{{ __('project.payment_deadline') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('general.additional_info') }}</label>
                                                    <textarea class="form-control" rows="3" name="additional_info"
                                                              placeholder="{{ __('general.additional_info') }}"></textarea>
                                                </div>
                                                <div class="timeline-footer">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        {{ __('block.add_outcome') }}
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- END timeline item -->
                            <!-- timeline item -->
                            <div>
                                <i class="fa fa-receipt bg-purple"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">{{ __('block.add_invoice_for_outcome') }}</h3>
                                    <div class="timeline-body">
                                        @if (!is_null($invoices))
                                            <table class="table table-striped datatable_without_search">
                                                <thead>
                                                <tr>
                                                    <th style="width: 10px">#</th>
                                                    <th>{{ __('general.supplier') }}</th>
                                                    <th>{{ __('general.amount') }} (р.)</th>
                                                    <th>{{ __('general.invoice') }}</th>
                                                    @can('remove invoices')
                                                        <th>{{ __('general.removing') }}</th>
                                                    @endcan
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($invoices as $invoice)
                                                    @if ($invoice->status=='Удален')
                                                        <tr class="table-danger">
                                                    @else
                                                        <tr>
                                                            @endif
                                                            <td>{{ $invoice['id'] }}</td>
                                                            <td> {{ optional($invoice->supplier)->name }}
                                                                <br><small>{{ $invoice['created_at'] }}<br>
                                                                    @if (!is_null($invoice['additional_info']))
                                                                        {{ $invoice['additional_info'] }}
                                                                    @endif
                                                                </small>
                                                            </td>
                                                            <td>
                                                                @if($invoice->status != 'Оплачен')
                                                                    <a href="#" class="xedit"
                                                                       data-pk="{{ $invoice->id }}"
                                                                       data-name="amount"
                                                                       data-model="Invoice">
                                                                        {{ $invoice->amount }}</a>
                                                                @else
                                                                    {{ $invoice->amount }}р.
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div id="invoice_file_{{ $invoice->id }}">
                                                                    @include('invoice.ajax.invoice_file')
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @can('remove invoices')
                                                                    <form class="button-delete-inline"
                                                                          action="{{ route('invoice.destroy', $invoice->id) }}"
                                                                          method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="btn btn-app bg-warning delete-btn">
                                                                            <i class="fas fa-trash">
                                                                            </i>
                                                                            {{ __('general.remove') }}
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                </tbody>
                                            </table>
                                        @else

                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-upload bg-maroon"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">{{ __('block.finish_block') }}</h3>
                                    <div class="timeline-body">
                                        @if ($block->status == 'Завершен')
                                            {{ __('block.this_block_was_finished') }}
                                        @else
                                            {{ __('block.click_finish_block_after_actual_finish') }}
                                        @endif
                                    </div>
                                    <div class="timeline-footer">
                                        @if ($block->status != 'Завершен')
                                            <form action="{{ route('block.update', $block->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="done_this_block">
                                                <button type="submit" class="btn btn-sm bg-success">
                                                    {{ __('block.finish_block') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-check bg-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('block.additional_info_for_block') }}</h3>
                            </div>
                            <form action="{{ route('block.update', $block->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="change_additional_info">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>{{ __('general.additional_info') }}</label>
                                        <textarea class="form-control" rows="3" name="additional_info"
                                                  placeholder="{{ __('block.additional_info_for_block') }}">{{ $block->additional_info }}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit"
                                            class="btn btn-outline-primary">{{ __('general.add') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('block.block_status') }}</h3>
                            </div>
                            <form action="{{ route('block.update', $block->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="change_status">
                                <div class="card-body">
                                    <div class="form-group">
                                        <select class="form-control select2" name="status"
                                                data-placeholder="{{ __('block.choose_block_status') }}"
                                                style="width: 100%;">
                                            <option></option>
                                            @foreach($statuses as $status)
                                                <option
                                                    value="{{ $status }}" {{ $block->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                            <option
                                                value="Завершен" {{ $block->status == 'Завершен' ? 'selected' : '' }}>
                                                {{ __('block.finished') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit"
                                            class="btn btn-outline-primary">{{ __('block.change_block_status') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('block.change_block_supplier') }}</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('block.update', $block->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="change_supplier">
                                    <div class="form-group">
                                        <label>{{ __('general.suppliers') }}</label>
                                        <select class="form-control select2" name="supplier_id"
                                                data-placeholder="{{ __('project.select_supplier') }}"
                                                style="width: 100%;">
                                            <option></option>
                                            @foreach($suppliers as $supplier)
                                                <option
                                                    value="{{ $supplier->id }}" {{ $block->supplier_id == $supplier->id ? 'selected' : '' }} >{{$supplier->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit"
                                        class="btn btn-outline-primary">{{ __('block.change_block_supplier') }}</button>
                            </div>
                            </form>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('block.change_contract_with_supplier') }}</h3>
                            </div>
                            <form action="{{ route('block.update', $block->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="choose_supplier_contract">
                                <div class="card-body">
                                    @if ($block->contract_id != '')
                                        @if (!is_null(optional($block->supplier)->contracts))
                                            @foreach($block->supplier->contracts as $contract)
                                                <div class="form-check">
                                                    <input type="radio" id="{{ $contract->id  }}" name="contract"
                                                           class="form-check-input"
                                                           value="{{ $contract->id }}" {{ $block->contract_id == $contract->id ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $contract->id }}">
                                                        №{{ $contract->name }}
                                                        - {{ __('project.valid_before') }} {{ $contract->date_period }} {{ $contract->additional_info }}
                                                        <br>
                                                        <a href="{{ Storage::url($contract->file) }}" download>
                                                            {{ __('general.download_contract') }} {{ $contract->name }}
                                                        </a>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            {{ __('block.no_contracts_for_this_supplier') }}
                                        @endif
                                    @else
                                        {{ __('block.contract_with_supplier_not_chosen') }}
                                    @endif
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit"
                                            class="btn btn-outline-primary">{{ __('block.change_contract_with_supplier') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
        </div>
        </div>
        @endcan
        @else
            @include('error.dont_have_access')
        @endif
    </section>
@endsection

