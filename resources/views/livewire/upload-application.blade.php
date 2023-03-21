<div>
    @if (!is_null($project))
        <div class="card">
            <form action="{{ route('project.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h3 class="card-title">{{ __('project.upload_application_from_client') }}</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" name="action" value="upload_application_client">
                    <div class="form-group">
                        <label>{{ __('project.client') }}</label>
                        <select wire:model="selectedClientId" class="form-control" name="client_id" required>
                            <option value="">{{ __('project.select_client') }}</option>
                            @if (!is_null($clients))
                                @foreach($clients as $client)
                                    <option value="{{ $client['id'] }}">{{ $client['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('project.contract') }}</label>
                        <select wire:model="selectedContractId" class="form-control" name="contract">
                            <option value="">{{ __('project.select_contract') }}</option>
                            @if (!is_null($contracts))
                                @foreach($contracts as $contract)
                                    <option value="{{ $contract->id }}">{{ $contract->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group mt-4">
                        <label for="application">{{ __('project.select_application_file') }}</label>
                        <input type="file" class="form-control-file"
                               name="application" required>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    @if (!is_null($contracts))
                    <button type="submit"
                            class="btn btn-primary float-right"
                            data-action='{"update_div":{"div_id":"project_applications"},"reset_form":{"need_reset": "true"}}'
                        {{ is_null($selectedContractId) ? 'disabled' : '' }}>
                        {{ __('project.upload_application_file') }}
                    </button>
                    @else
                        @if(is_null($selectedClientId))
                            <button class="btn btn-primary float-right" disabled>
                                {{ __('project.select_client') }}
                            </button>
                        @else
                            <a href="{{ route('client.edit', $selectedClientId) }}"
                                    class="btn btn-primary float-right">
                                {{ __('project.edit_client') }}
                            </a>
                        @endif
                    @endif
                </div>
            </form>
        </div>
    @endif
</div>
