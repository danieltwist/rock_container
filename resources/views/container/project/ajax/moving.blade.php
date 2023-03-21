<i class="fas fa-arrows-alt bg-blue"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_moving') }} {{ $container_project->moving == '' ? __('container.project_not_set') : $container_project->moving }}
    </h3>
    @if($container_project->moving == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="update_moving">
                <div class="row">
                    <div class="col-md-12">
                        <label>{{ __('container.project_moving') }}</label>
                        <div class="form-group">
                            <select class="form-control select2" name="moving"
                                    data-placeholder="{{ __('container.project_moving') }}"
                                    style="width: 100%;">
                                <option value="не требуется">{{ __('container.project_no_need') }}</option>
                                <option value="требуется">{{ __('container.project_need') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"moving"}}'>
                    {{ __('general.save') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_moving">
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"moving"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
