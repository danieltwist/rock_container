<a class="btn btn-app bg-navy" href="{{ route('supplier.show', $supplier->id) }}">
    <i class="fas fa-chart-bar"></i> {{ __('general.summary') }}
</a>
<a class="btn btn-app bg-primary" href="/invoice?supplier={{ $supplier->id }}">
    <i class="fas fa-file-invoice"></i> {{ __('general.invoices') }}
</a>
