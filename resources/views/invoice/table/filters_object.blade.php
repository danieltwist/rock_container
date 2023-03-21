<div class="row mt-2">
    <div class="col-md-12">
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_object_filters" data-filter="">
                {{ __('general.all') }}
            </button>
        </div>
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
                    data-filter="Счет на согласовании" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_agree') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
                    data-filter="Создан черновик инвойса" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_draft_invoice') }}
            </button>
        </div>
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
                    data-filter="Ожидается счет от поставщика" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_waiting_for_invoice') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
            data-filter="Ожидается создание инвойса" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_waiting_for_create_invoice') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
                    data-filter="Ожидается оплата" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_waiting_for_payment') }}
            </button>
        </div>
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
                    data-filter="Счет согласован на оплату" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_agreed') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
            data-filter="Согласована частичная оплата" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_part_agreed') }}
            </button>
        </div>
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
                    data-filter="Частично оплачен" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_part_paid') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_object_filters"
            data-filter="Оплачен" data-filter_type="{{ $filter }}">{{ __('invoice.filter_status_paid') }}
            </button>
        </div>
    </div>
</div>
