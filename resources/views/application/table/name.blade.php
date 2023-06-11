<a href="{{ route('application.show', $application->id) }}" class="text-dark">{{ $application->name }}</a><br>
<small>
    @switch($application->type)
        @case('Поставщик')
            Взять в аренду
            @break
        @case('Клиент')
            Выдать в аренду
            @break
        @default
            {{ $application->type }}
    @endswitch
    {{ !is_null($application->surcharge) ? ' / Доплатная' : '' }}
    <br>
    {{ $application->created_at->format('d.m.Y') }}
</small>
