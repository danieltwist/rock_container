<i class="fas fa-file-contract bg-navy"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_client_application') }} {{ $container_project->application_from_client == ''
            ? __('container.project_not_uploaded_f')
            : __('container.project_uploaded_f') }}
    </h3>
    @if($container_project->application_from_client == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="upload_application_from_client">
                <div class="form-group">
                    <input type="file" class="form-control-file" name="application_from_client">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"client_application"}}'>
                    {{ __('general.upload') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                <a class="btn btn-outline-primary btn-sm" href="{{ Storage::url($container_project->application_from_client) }}" download>Скачать</a>
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_application_from_client">
                <button type="submit" class="ml-1 btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"client_application"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
