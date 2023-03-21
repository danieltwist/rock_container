<div class="card-header">
    <h3 class="card-title">{{ __('container.project_places') }}</h3>
</div>
<form action="{{ route('container_project.update', $container_project->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="change_places">
    <div class="card-body">
        <div class="form-group">
            <label>{{ __('container.project_start_place') }}</label>
            <input class="form-control to_uppercase"
                   type="text"
                   name="start_place"
                   placeholder="{{ __('container.project_start_place') }}"
                   value="{{ $container_project->start_place }}">
        </div>
        <div class="form-group">
            <label>{{ __('container.project_place_of_arrival') }}</label>
            <input class="form-control to_uppercase"
                   type="text"
                   name="place_of_arrival"
                   placeholder="{{ __('container.project_place_of_arrival') }}"
                   value="{{ $container_project->place_of_arrival }}">
        </div>
        <div class="form-group">
            <label>{{ __('container.project_drop_off_location') }}</label>
            <input class="form-control to_uppercase"
                   type="text"
                   name="drop_off_location"
                   placeholder="{{ __('container.project_drop_off_location') }}"
                   value="{{ $container_project->drop_off_location }}">
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-outline-primary"
                data-action='{"update_div":{"div_id":"places"}}'>
            {{ __('general.save') }}
        </button>
    </div>
</form>
