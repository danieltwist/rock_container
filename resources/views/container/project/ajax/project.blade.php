<i class="fas fa-coins bg-info"></i>
<div class="timeline-item">
    <h3 class="timeline-header no-border">{{ __('project.project') }} {{ $container_project->project_id != ''
        ? optional(optional($container_project->project))->name
        : __('container.project_not_selected') }}</h3>
    @if($container_project->project_id == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="change_project_id">
                <div class="form-group">
                    <label>{{ __('container.project_select_project') }}</label>
                    <select class="form-control select2" name="project_id"
                            data-placeholder="{{ __('container.project_select_project') }}" style="width: 100%;" required>
                        <option></option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"main_project"}}'>
                    {{ __('general.choose') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_project_id">
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"main_project"},"select2_init":{"need_init":"true"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
