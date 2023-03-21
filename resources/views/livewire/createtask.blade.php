<div>
    @if(is_null($task))
        @if(!is_null($project_id))
            <input type="hidden" name="project_id" value="{{ $project_id }}">
        @endif
            <input type="hidden" name="send_to" value="{{ $send_to }}" class="create_task_send_to">
        <div class="form-group">
            <label>{{ __('task.task_name') }}</label>
            <input type="text" class="form-control to_uppercase" name="name"
                   placeholder="{{ __('task.task_name') }}">
        </div>
        <div class="form-group">
            <label>{{ __('task.task_text') }}</label>
            <textarea class="form-control to_uppercase" rows="3" name="text"
                      placeholder="{{ __('task.task_text') }}">@if(!is_null($text)){{ $text }}@endif</textarea>
        </div>
        <div class="form-group">
            <label for="to_id">{{ __('task.send_to') }}</label>
            <select class="form-control create_task_to_users" name="to_users" data-placeholder="{{ __('task.choose_send_to') }}" required>
                <option value="">{{ __('task.choose_send_to') }}</option>
                @foreach($users as $user)
                    <option data-create_task_send_to="Пользователю" value="Пользователю:{{ $user->id }}" {{ $selected_user == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
                @foreach($user_roles as $key=>$value)
                    <option data-create_task_send_to="{{ $key }}" value="{{ $key }}:{{ $value }}" {{ $selected_user == $key ? 'selected' : '' }}>{{ $key }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>{{ __('task.object') }}</label>
            <select wire:model="selectedModel" class="form-control" name="model">
                <option value="free">{{ __('task.object_free') }}</option>
                <option value="project">{{ __('task.object_project') }}</option>
                <option value="invoice">{{ __('task.object_invoice') }}</option>
                <option value="container">{{ __('task.object_container') }}</option>
                <option value="container_group">{{ __('task.object_container_group') }}</option>
                <option value="upd">{{ __('task.object_upd') }}</option>
            </select>
        </div>

        @if(!is_null($projects))
            <div class="form-group">
                <label>{{ __('task.choose_project') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id"
                        data-placeholder="{{ __('task.choose_project') }}" style="width: 100%;" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{$project->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(!is_null($invoices))
            <div class="form-group">
                <label>{{ __('task.choose_invoice') }}</label>
                <select class="form-control select2 select2_livewire invoice_select2_ajax" name="model_id"
                        data-placeholder="{{ __('task.choose_invoice') }}" style="width: 100%;" required>
                </select>
            </div>
        @endif

        @if(!is_null($invoices_project_task))
            <div class="form-group">
                <label>{{ __('task.choose_invoice') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id"
                        data-placeholder="{{ __('task.choose_invoice') }}" style="width: 100%;" required>
                    @foreach($invoices_project_task as $invoice)
                        <option value="{{ $invoice->id }}">{{ $invoice->direction }} №{{$invoice->id}} {{ __('general.from') }} {{$invoice->created_at}} {{ __('general.for') }}
                            @if (!is_null($invoice->supplier_id))
                                {{ optional($invoice->supplier)->name }}
                            @elseif (!is_null($invoice->client_id))
                                {{ optional($invoice->client)->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(!is_null($upd))
            <div class="form-group">
                <label>{{ __('task.choose_invoices') }}</label>
                <select class="form-control select2 select2_livewire invoice_select2_ajax" name="model_id[]"
                        data-placeholder="{{ __('task.choose_invoices') }}" style="width: 100%;" multiple required>
                    @foreach($upd as $invoice)
                        <option value="{{ $invoice->id }}">{{ $invoice->direction }} №{{$invoice->id}} {{ __('general.from') }} {{$invoice->created_at}} {{ __('general.for') }}
                            @if (!is_null($invoice->supplier_id))
                                {{ optional($invoice->supplier)->name }}
                            @elseif (!is_null($invoice->client_id))
                                {{ optional($invoice->client)->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(!is_null($upd_project_task))
            <div class="form-group">
                <label>{{ __('task.choose_invoices') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id[]"
                        data-placeholder="{{ __('task.choose_invoices') }}" style="width: 100%;" multiple required>
                    @foreach($upd_project_task as $invoice)
                        <option value="{{ $invoice->id }}">{{ $invoice->direction }} №{{$invoice->id}} {{ __('general.from') }} {{$invoice->created_at}} {{ __('general.for') }}
                            @if (!is_null($invoice->supplier_id))
                                {{ optional($invoice->supplier)->name }}
                            @elseif (!is_null($invoice->client_id))
                                {{ optional($invoice->client)->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(!is_null($containers))
            <div class="form-group">
                <label>{{ __('task.choose_container') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id"
                        data-placeholder="{{ __('task.choose_container') }}" style="width: 100%;" required>
                    @foreach($containers as $container)
                        <option value="{{ $container->id }}">{{$container->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(!is_null($container_groups))
            <div class="form-group">
                <label>{{ __('task.choose_containers_list') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id"
                        data-placeholder="{{ __('task.choose_containers_list') }}" style="width: 100%;" required>
                    @foreach($container_groups as $group)
                        <option value="{{ $group->id }}">№{{ $group->id }} {{ $group->name }} {{ __('task.project') }} {{ optional($group->project)->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="form-group">
            <label>{{ __('task.co-workers') }}</label>
            <select class="form-control select2 select2_livewire" name="additional_users[]"
                    data-placeholder="{{ __('task.co-workers') }}" multiple>
                <option value="">{{ __('task.choose_co-workers') }}</option>
                @foreach($users as $user)
                    <option value="Пользователю:{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>{{ __('task.finish_before') }}</label>
            <input type="text" class="form-control task_deadline" name="task_deadline" placeholder="{{ __('task.task_deadline') }}">
        </div>
        <div class="form-group">
            <label>{{ __('task.upload_files') }}</label>
            <input type="file" class="form-control-file" name="files[]" multiple>
        </div>
        <div class="form-group clearfix">
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="can_change_deadline" name="can_change_deadline">
                <label for="can_change_deadline">{{ __('task.can_change_deadline') }}</label>
            </div>
        </div>
        <div class="form-group clearfix">
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="check_work" name="check_work">
                <label for="check_work">{{ __('task.check_work') }}</label>
            </div>
        </div>
    @else
        @if(!is_null($project_id))
            <input type="hidden" name="project_id" value="{{ $project_id }}">
        @endif
        <input type="hidden" name="send_to" value="{{ $send_to }}" class="create_task_send_to">
        <div class="form-group">
            <label>{{ __('task.task_name') }}</label>
            <input type="text" class="form-control to_uppercase" name="name"
                   placeholder="{{ __('task.task_name') }}"
                   value="{{ $task->name }}">
        </div>
        <div class="form-group">
            <label>{{ __('task.task_text') }}</label>
            <textarea class="form-control to_uppercase"
                      rows="3"
                      name="text"
                      placeholder="{{ __('task.task_text') }}">{{ $task->text }}</textarea>
        </div>
        <div class="form-group">
            <label for="to_id">{{ __('task.send_to') }}</label>
            <select class="form-control create_task_to_users" name="to_users" data-placeholder="{{ __('task.choose_send_to') }}" required>
                <option value="">{{ __('task.choose_send_to') }}</option>
                @foreach($users as $user)
                    <option data-create_task_send_to="Пользователю" value="Пользователю:{{ $user->id }}" {{ $task->send_to == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
                @foreach($user_roles as $key=>$value)
                    <option data-create_task_send_to="{{ $key }}" value="{{ $key }}:{{ $value }}" {{ $task->send_to == $key ? 'selected' : '' }}>{{ $key }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>{{ __('task.object') }}</label>
            <select wire:model="selectedModel" class="form-control" name="model">
                <option value="free">{{ __('task.object_free') }}</option>
                <option value="project">{{ __('task.object_project') }}</option>
                <option value="invoice">{{ __('task.object_invoice') }}</option>
                <option value="container">{{ __('task.object_container') }}</option>
                <option value="container_group">{{ __('task.object_container_group') }}</option>
                <option value="upd">{{ __('task.object_upd') }}</option>
            </select>
        </div>
        @if(!is_null($projects))
            <div class="form-group">
                <label>{{ __('task.choose_project') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id"
                        data-placeholder="{{ __('task.choose_project') }}" style="width: 100%;" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $task->model_id == $project->id ? 'selected' : '' }}>{{$project->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(!is_null($invoices))
            <div class="form-group">
                <label>{{ __('task.choose_invoice') }}</label>
                <select class="form-control select2 select2_livewire invoice_select2_ajax" name="model_id"
                        data-placeholder="{{ __('task.choose_invoice') }}" style="width: 100%;" required>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}" {{ $task->model_id == $invoice->id ? 'selected' : '' }}>{{ $invoice->direction }} №{{$invoice->id}} {{ __('general.from') }} {{$invoice->created_at}} {{ __('general.for') }}
                            @if (!is_null($invoice->supplier_id))
                                {{ optional($invoice->supplier)->name }}
                            @elseif (!is_null($invoice->client_id))
                                {{ optional($invoice->client)->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(!is_null($upd))
            <div class="form-group">
                <label>{{ __('task.choose_invoices') }}</label>
                <select class="form-control select2 select2_livewire invoice_select2_ajax" name="model_id[]"
                        data-placeholder="{{ __('task.choose_invoices') }}" style="width: 100%;" multiple required>
                    @foreach($upd as $invoice)
                        <option value="{{ $invoice->id }}" {{ in_array($invoice->id, unserialize($task->object_array)) ? 'selected' : '' }}>{{ $invoice->direction }} №{{$invoice->id}} {{ __('general.from') }} {{$invoice->created_at}} {{ __('general.for') }}
                            @if (!is_null($invoice->supplier_id))
                                {{ optional($invoice->supplier)->name }}
                            @elseif (!is_null($invoice->client_id))
                                {{ optional($invoice->client)->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(!is_null($containers))
            <div class="form-group">
                <label>{{ __('task.choose_container') }}</label>
                <select class="form-control select2 select2_livewire" name="model_id"
                        data-placeholder="{{ __('task.choose_container') }}" style="width: 100%;" required>
                    @foreach($containers as $container)
                        <option value="{{ $container->id }} {{ $task->model_id == $container->id ? 'selected' : '' }}">{{$container->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="form-group">
            <label>{{ __('task.co-workers') }}</label>
            <select class="form-control select2 select2_livewire" name="additional_users[]"
                    data-placeholder="{{ __('task.co-workers') }}" multiple>
                <option value="">{{ __('task.choose_co-workers') }}</option>
                @if(!empty($task->additional_users))
                    @foreach($users as $user)
                        <option value="Пользователю:{{ $user->id }}" {{ in_array($user->id, $task->additional_users) ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                @else
                    @foreach($users as $user)
                        <option value="Пользователю:{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="form-group">
            <label>{{ __('task.finish_before') }}</label>
            <input type="text" class="form-control task_deadline" name="task_deadline" value="{{ $task->deadline }}"
                   placeholder="{{ __('task.task_deadline') }}">
        </div>

        <div class="form-group">
            <label>{{ __('task.upload_files') }}</label>
            <input type="file" class="form-control-file" name="files[]" multiple>
        </div>

        <div class="form-group clearfix">
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="can_change_deadline" name="can_change_deadline" {{ !is_null($task->can_change_deadline) ? 'checked' : '' }}>
                <label for="can_change_deadline">{{ __('task.can_change_deadline') }}</label>
            </div>
        </div>

        <div class="form-group clearfix">
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="check_work" name="check_work" {{ !is_null($task->check_work) ? 'checked' : '' }}>
                <label for="check_work">{{ __('task.check_work') }}</label>
            </div>
        </div>
    @endif
</div>
