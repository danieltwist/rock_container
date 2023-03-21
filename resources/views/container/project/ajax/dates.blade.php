<form action="{{ route('container_project.update', $container_project->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="update_dates">
    <div class="card-header">
        <h3 class="card-title">Даты</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>{{ __('container.project_date_departure') }}</label>
            <input class="form-control date_input invoice_deadline"
                   type="text"
                   name="date_departure"
                   placeholder="{{ __('container.project_date_departure') }}"
                   value="{{ $container_project->date_departure }}">
        </div>
        <div class="form-group">
            <label>{{ __('container.project_date_of_arrival') }}</label>
            <input class="form-control date_input invoice_deadline"
                   type="text"
                   name="date_of_arrival"
                   placeholder="{{ __('container.project_date_of_arrival') }}"
                   value="{{ $container_project->date_of_arrival }}">
        </div>
        <div class="form-group">
            <label>{{ __('container.svv') }}</label>
            <input class="form-control date_input invoice_deadline"
                   type="text"
                   name="svv"
                   placeholder="{{ __('container.svv') }}"
                   value="{{ $container_project->svv }}">
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-outline-primary"
                data-action='{"update_div":{"div_id":"dates"},"datetimepicker_init":{"need_init":"true"}}'>
            {{ __('general.save') }}
        </button>
    </div>
</form>
