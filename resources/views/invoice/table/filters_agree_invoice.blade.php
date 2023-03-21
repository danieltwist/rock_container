<div class="row mt-2">
    <div class="col-md-12">
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_agree_filters" data-filter="Все">
                {{ __('general.all') }}
            </button>
        </div>
        <div class="btn-group mt-2">
            <button type="button" class="btn btn-default btn-sm invoices_agree_filters"
                    data-filter="Без дополнительного статуса">{{ __('invoice.sub_status_without') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_agree_filters"
                    data-filter="Срочно">{{ __('invoice.sub_status_urgent') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_agree_filters"
                    data-filter="Взаимозачет">{{ __('invoice.sub_status_compensation') }}
            </button>
            <button type="button" class="btn btn-default btn-sm invoices_agree_filters"
                    data-filter="Отложен">{{ __('invoice.sub_status_postponed') }}
            </button>
        </div>
    </div>
</div>
