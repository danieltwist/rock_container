@foreach($changes as $change)
    {{ __('invoice.changed_by_user') }} {{ $change->user->name }} {{ $change->created_at }}<br><br>
    <b>{{ __('invoice.before_change') }}:</b><br>
    @php
        $invoice = $change->invoice;
    @endphp
    @include('project.layouts.one_invoice_table')
@endforeach
