@if ($container->usage_statistic->isNotEmpty())
    {{ __('container.start_for_us') }}: {{ $container->usage_statistic[0]->start_date_for_us }}
    <br>
    {{ __('container.start_for_client') }}: {{ $container->usage_statistic[0]->start_date_for_client }}
    <br>
    {{ __('container.border') }}: {{ $container->usage_statistic[0]->border_date }}
@else
    @if($container->start_date_for_us != '' )
        {{ __('container.start_for_us') }}:
        <a href="#" class="xedit"
           data-pk="{{$container->id}}"
           data-name="start_date_for_us"
           data-model="Container">
            {{ $container->start_date_for_us }}
        </a>
    @elseif($container->project_id != '')
        <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="start_use_container_for_us">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_table":{"table_id":"containers_group_{{ $group->id }}"}}'>
                    {{ __('container.set_start_for_us') }}
                </button>
            </div>
        </form>
    @endif
    @if($container->start_date_for_client != '')
        <br>
        {{ __('container.start_for_client') }}::
        <a href="#" class="xedit"
           data-pk="{{$container->id}}"
           data-name="start_date_for_client"
           data-model="Container">
            {{ $container->start_date_for_client }}
        </a>
    @elseif($container->project_id != '')
        <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="start_use_container_for_client">
            <div class="form-group">
                <button type="submit" class="btn btn-info btn-sm"
                        data-action='{"update_table":{"table_id":"containers_group_{{ $group->id }}"}}'>
                    {{ __('container.set_start_for_client') }}
                </button>
            </div>
        </form>
    @endif
    @if ($container->border_date != '')
        <br>
        {{ __('container.border') }}: {{ $container->border_date }}
    @endif
    @if($container->problem_id !='')
        <br>
        <b>{{ __('container.problem') }}: {{ optional($container->problem)->problem }}, {{ optional($container->problem)->problem_date }}</b>
    @endif
@endif
