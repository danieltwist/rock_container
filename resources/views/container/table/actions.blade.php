<a class="btn btn-app bg-success" href="{{ route('container.show', $container->id) }}">
    <i class="far fa-eye">
    </i>
    {{ __('general.go') }}
</a>
@if ($container->problem_id =='')
    <a class="btn btn-app bg-warning" href="{{ route('container_problem.create').'?container_id='.$container->id }}">
        <i class="fas fa-exclamation-triangle"></i>
        </i>
        {{ __('container.problem') }}
    </a>
@else
    <a class="btn btn-app bg-warning" href="{{ route('container_problem.show', $container->problem_id) }}">
        <i class="fas fa-exclamation-triangle"></i>
        </i>
        {{ __('container.problem') }}
    </a>
@endif
@can ('edit projects')
    <a class="btn btn-app bg-indigo" href="{{ route('container.edit', $container->id) }}">
        <i class="fas fa-pencil-alt">
        </i>
        {{ __('general.change') }}
    </a>
@endcan
@can ('remove containers')
    <button
        class="btn btn-app bg-danger ajax-delete-row"
        data-action="delete_row"
        data-object="container"
        data-type="ajax"
        data-object-id="{{ $container->id }}">
        <i class="fas fa-trash">
        </i>
        {{ __('general.remove') }}
    </button>
@endcan
