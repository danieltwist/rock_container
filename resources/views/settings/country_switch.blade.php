@switch($country)
    @case('Россия')
    {{ __('settings.country_russia') }}
    @break
    @case('Китай')
    {{ __('settings.country_china') }}
    @break
    @case('Индонезия')
    {{ __('settings.country_indonesia') }}
    @break
    @case('Польша')
    {{ __('settings.country_poland') }}
    @break
    @case('Беларусь')
    {{ __('settings.country_belarus') }}
    @break
    @case('Казахстан')
    {{ __('settings.country_kazakhstan') }}
    @break
    @case('Великобритания')
    {{ __('settings.country_uk') }}
    @break
    @case('Германия')
    {{ __('settings.country_germany') }}
    @break
    @case('Индия')
    {{ __('settings.country_india') }}
    @break
@endswitch
