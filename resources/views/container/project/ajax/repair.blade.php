<i class="fas fa-tools bg-orange"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_repair') }} {{ $container_project->need_repair == ''
            ? __('container.project_not_set')
            : $container_project->need_repair }}
    </h3>
    @if($container_project->need_repair == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="update_repair">
                <div class="row">
                    <div class="col-md-12">
                        <label>{{ __('container.project_repair') }}</label>
                        <div class="form-group">
                            <select class="form-control select2" name="need_repair"
                                    data-placeholder="{{ __('container.project_repair') }}"
                                    style="width: 100%;"
                                    id="need_repair">
                                <option value="не требуется">{{ __('container.project_no_need') }}</option>
                                <option value="требуется">{{ __('container.project_need') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"repair"}}'>{{ __('general.save') }}</button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_repair">
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"repair"}}'>{{ __('general.remove') }}</button>
            </form>
        </div>
    @endif
</div>
