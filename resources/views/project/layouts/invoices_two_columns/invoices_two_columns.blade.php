<div class="row">
    <div class="col-md-6">
        @foreach($invoices as $invoice)
            @if($invoice->direction == 'Доход')
                @include('project.layouts.invoices_two_columns.card')
            @endif
        @endforeach
    </div>
    <div class="col-md-6">
        @foreach($invoices as $invoice)
            @if($invoice->direction == 'Расход')
                @include('project.layouts.invoices_two_columns.card')
            @endif
        @endforeach
    </div>
</div>
