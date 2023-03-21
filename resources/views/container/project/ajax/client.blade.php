<i class="fas fa-id-card bg-green"></i>
<div class="timeline-item">
    <h3 class="timeline-header no-border">{{ __('general.client') }} {{ $container_project->client_id != '' ? optional(optional($container_project->client))->name : 'не выбран'}}</h3>
    @if($container_project->client_id == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="change_client_id">
                <div class="form-group">
                    <label>{{ __('container.project_choose_client') }}</label>
                    <select class="form-control select2" name="client_id"
                            data-placeholder="{{ __('container.project_choose_client') }}" style="width: 100%;" required>
                        <option></option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"client"}}'>
                    {{ __('general.choose') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_client_id">
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"client"},"select2_init":{"need_init":"true"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
