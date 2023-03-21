@switch($type)
    @case('Авто')
    {{ __('supplier.auto') }}
    @break
    @case('ТЭО')
    {{ __('supplier.teo') }}
    @break
    @case('Аренда')
    {{ __('supplier.rent') }}
    @break
    @case('Прочее')
    {{ __('supplier.another') }}
    @break
@endswitch
