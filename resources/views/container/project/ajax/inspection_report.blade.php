<i class="fas fa-search bg-warning"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_inspection_report') }} {{ $container_project->inspection_report != ''
            ? __('container.project_uploaded')
            : __('container.project_not_uploaded') }}
    </h3>
    @if($container_project->inspection_report == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="upload_inspection_report">
                <div class="form-group">
                    <input type="file" class="form-control-file" name="inspection_report">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"inspection_report"}}'>
                    {{ __('general.upload') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                <a class="btn btn-outline-primary btn-sm" href="{{ Storage::url($container_project->inspection_report) }}" download>Скачать</a>
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_inspection_report">
                <button type="submit" class="ml-1 btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"inspection_report"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
