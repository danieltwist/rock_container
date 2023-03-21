<div class="modal fade" id="add_file">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('project.project_comments') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="dropdown">
                    <div class="dropdown-menu" id="users_send_to">
                        @foreach($users as $user)
                            <a class="dropdown-item cursor-pointer chat-notify-user"
                               data-notify_user="{{ $user->id }}">{{ $user->name }}</a>
                        @endforeach
                    </div>
                </div>
                <div id="project_additional_info">
                    @include('project.ajax.project_additional_info')
                </div>
            </div>
            <div class="card-footer">
                <form action="{{ route('project_add_comment') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="answer_to" id="project_comment_answer_to" value="">
                    <div class="form-group">
                        <label>{{ __('project.add_comment') }}</label>
                        <textarea class="form-control" rows="3" name="comment" id="comment" placeholder="{{ __('project.write_text') }}"></textarea>
                    </div>
                    <a class="cursor-pointer"
                       data-toggle="collapse"
                       data-target="#collapse_notify_users"
                       aria-expanded="false"
                       aria-controls="collapseExample">
                        <i class="fa fa-angle-down"></i>
                        {{ __('project.send_notifications_to_users') }}
                    </a>
                    <div class="collapse mt-2" id="collapse_notify_users">
                        <div class="form-group">
                            <select class="select2" multiple="multiple" data-placeholder="{{ __('general.choose_user') }}" id="comment_notify_users" name="notify_users[]" style="width: 100%;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ !in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ?: 'selected'}}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="file">{{ __('general.file') }}</label>
                        <input type="file" class="form-control-file" name="file">
                    </div>
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"update_div":{"div_id":"project_additional_info"},"reset_form":{"need_reset": "true"}}'>
                        {{ __('general.add') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
