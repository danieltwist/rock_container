<a class="btn btn-app bg-primary" href="{{ route('project.show', $project['id']) }}">
    <i class="far fa-eye">
    </i>
    {{ __('general.go') }}
</a>
<a class="btn btn-app bg-success" href="{{ route('export_project', $project['id']) }}">
    <i class="fas fa-file-excel"></i>
    </i>
    {{ __('general.export') }}
</a>
@can ('edit projects')
    @if(can_edit_this_project($project->id))
        <a class="btn btn-app bg-indigo" href="{{ route('project.edit', $project['id']) }}">
            <i class="fas fa-pencil-alt">
            </i>
            {{ __('general.change') }}
        </a>
    @endif
@endcan
@can ('remove projects')
    @if(!is_null($project->deleted_at))
        <button
            class="btn btn-app bg-warning ajax-restore-row"
            data-action="restore_row"
            data-object="project"
            data-type="ajax"
            data-object-id="{{ $project->id }}">
            <i class="fas fa-trash-restore"></i>
            Восстановить
        </button>
    @else
        <button
            class="btn btn-app bg-danger ajax-delete-row"
            data-action="delete_row"
            data-object="project"
            data-type="ajax"
            data-object-id="{{ $project->id }}">
            <i class="fas fa-trash">
            </i>
            {{ __('general.remove') }}
        </button>
    @endif
@endcan
