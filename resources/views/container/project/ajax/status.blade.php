<div class="card-header">
    <h3 class="card-title">{{ __('container.project_status') }}</h3>
</div>
<form action="{{ route('container_project.update', $container_project->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="update_project_status">
    <div class="card-body">
        <div class="form-group">
            <label>{{ __('container.project_current_status') }}</label>
            <select class="form-control select2" name="status"
                    data-placeholder="{{ __('container.project_select_current_status') }}" style="width: 100%;">
                <option value="Добавлен вручную" {{ $container_project->status == 'Добавлен вручную' ? 'selected' : '' }}>{{ __('container.project_added_manually') }}</option>
                <option value="Добавлен автоматически" {{ $container_project->status == 'Добавлен автоматически' ? 'selected' : '' }}>{{ __('container.project_added_auto') }}</option>
                <option value="В работе" {{ $container_project->status == 'В работе' ? 'selected' : '' }}>{{ __('container.project_in_work') }}</option>
                <option value="Ожидается оплата"  {{ $container_project->status == 'Ожидается оплата' ? 'selected' : '' }}>{{ __('container.project_waiting_payment') }}</option>
                <option value="Завершен и оплачен"  {{ $container_project->status == 'Завершен и оплачен' ? 'selected' : '' }}>{{ __('container.project_finished_and_paid') }}</option>
            </select>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-outline-primary"
                data-action='{"update_div":{"div_id":"status"}}'>
            {{ __('general.save') }}
        </button>
    </div>
</form>
