<strong>
    <a class="text-dark" href="{{ route('container.show', $container->id) }}">{{ $container->name }}</a>
</strong>
<small>
    @switch($container->size)
        @case('40 футов')
        40 {{ __('container.foots') }}
        @break
        @case('20 футов')
        20 {{ __('container.foots') }}
        @break
    @endswitch
    @if(!is_null($container->archive))
        <i class="fas fa-archive"></i>
    @endif
    @if(is_null($container->archive) && is_null($container->project_id))
        <i class="fas fa-pause"></i>
    @endif
    <br>
    @switch($container->type)
        @case('Аренда')
        {{ __('container.rent') }}
        @break
        @case('В собственности')
        {{ __('container.own') }}
        @break
    @endswitch - {{ optional($container->supplier)->name }}<br>
    {{ __('container.svv') }}:
    <a href="#"
       class="xedit"
       data-pk="{{$container->id}}"
       data-name="svv"
       data-model="Container">
        {{ $container->svv == '' ? __('container.not_set') : $container->svv}}
    </a>
</small>
@if ($container->problem!='')
    <br><br><a class="btn btn-sm btn-default" href="{{ route('container_problem.show', $container->problem_id) }}">{{ __('container.show_problem') }}</a>
@endif
