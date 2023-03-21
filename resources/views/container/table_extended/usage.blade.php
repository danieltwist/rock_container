@if (!is_null($container->project))
    {{ __('container.in_project') }}: <a class="text-dark" href="{{ route('project.show', $container->project->id) }}">{{ $container->project->name }}</a><br>
@endif
@if($container->start_date_for_us != '')
    {{ __('container.start_for_us') }}:
    <a href="#" class="xedit"
       data-pk="{{$container->id}}"
       data-name="start_date_for_us"
       data-model="Container">
        {{ $container->start_date_for_us }}
    </a>
@else
    <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" value="start_use_container_for_us">
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-sm btn-block"
                    data-action='{"update_table":{"table_id":"containers_ajax_table","id":"{{$container->id}}","type":"ajax"}}'>
                {{ __('container.set_start_for_us') }}
            </button>
        </div>
    </form>
@endif
@if($container->start_date_for_client != '')
    <br>
    {{ __('container.start_for_client') }}:
    <a href="#" class="xedit"
       data-pk="{{$container->id}}"
       data-name="start_date_for_client"
       data-model="Container">
        {{ $container->start_date_for_client }}
    </a>
@else
    <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" value="start_use_container_for_client">
        <div class="form-group">
            <button type="submit" class="btn btn-info btn-sm btn-block"
                    data-action='{"update_table":{"table_id":"containers_ajax_table","id":"{{$container->id}}","type":"ajax"}}'>
                {{ __('container.set_start_for_client') }}
            </button>
        </div>
    </form>
@endif

@if($container->problem_id !='')
    <br><b>{{ __('container.problem') }}: {{ optional($container->problem)->problem }}, {{ optional($container->problem)->problem_date }} </b>
@endif
