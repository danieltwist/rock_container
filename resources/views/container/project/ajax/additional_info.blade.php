<div class="card-header">
    <h3 class="card-title">{{ __('container.project_additional_info') }}</h3>
</div>
<form action="{{ route('container_project.update', $container_project->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="change_additional_info">
    <div class="card-body">
        <div class="form-group">
            <label>{{ __('general.additional_info') }}</label>
            <textarea class="form-control" rows="3" name="additional_info"
                      placeholder="{{ __('general.additional_info') }}">{{ $container_project->additional_info }}</textarea>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-outline-primary"
                data-action='{"update_div":{"div_id":"additional_info"}}'>
            {{ __('general.save') }}
        </button>
    </div>
</form>
