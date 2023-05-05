<a class="text-dark" href="{{ route('supplier.show', $supplier->id) }}">{{ $supplier->name }}</a><br>
<small>
    @include('settings.country_switch', ['country' => $supplier->country]) / @include('supplier.type_switch', ['type' => $supplier->type])
    <br>
    {{ __('general.added') }} {{ $supplier->created_at->format('d.m.Y') }}
</small>
