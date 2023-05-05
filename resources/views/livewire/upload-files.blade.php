<div>
    @if (!is_null($project))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('project.upload_files') }}</h3>
            </div>
            <form action="{{ route('project.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <input type="hidden" name="action" value="upload_files">
                    <div class="form-group">
                        <label for="folder">{{ __('project.folder') }}</label>
                        <select wire:model="selectedRootFolder" class="form-control" name="folder" required>
                            <option value="">{{ __('project.choose_folder') }}</option>
                            <option value="Заявки с поставщиками">{{ __('project.applications_with_clients') }}</option>
                            <option value="Отгрузочные документы">{{ __('project.shipping_documents') }}</option>
                            <option value="Сопроводительные документы">{{ __('project.accompanying_documents') }}</option>
                            <option value="Реестры">{{ __('project.registries') }}</option>
                            <option value="ЖДН и акты Китай">{{ __('project.railway_tax_and_acts_china') }}</option>
                            <option value="Корневая папка проекта">{{ __('project.root_folder') }}</option>
                        </select>
                    </div>
                    @if($need_subfolder)
                        @if(!empty($subfolders))
                            <div class="form-group">
                                <label for="subfolder">{{ __('project.select_subfolder') }}</label>
                                <select class="form-control" name="subfolder">
                                    @foreach($subfolders as $folder)
                                        <option value="{{ $folder }}">{{ $folder }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="subfolder_new">{{ __('project.or_create_new_folder') }}</label>
                                <input type="text" class="form-control" name="subfolder_new" placeholder="{{ __('project.name_for_new_subfolder') }}">
                            </div>
                        @else
                            <div class="form-group">
                                <label for="subfolder_new">{{ __('project.create_new_subfolder') }}</label>
                                <input type="text" class="form-control" name="subfolder_new" placeholder="{{ __('project.name_for_new_subfolder') }}">
                            </div>
                        @endif
                    @endif
                    <div class="form-group mt-4">
                        <label for="application">{{ __('general.choose_files') }}</label>
                        <input type="file" class="form-control-file" name="files[]" multiple required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"
                            data-action='{"update_div":{"div_id":"project_files"},"reset_form":{"need_reset": "true"}}'>{{ __('general.upload') }}</button>
                </div>
            </form>
        </div>
    @endif
</div>
