<div class="card direct-chat direct-chat-primary card-outline card-primary">
    <div class="card-header ui-sortable-handle" style="cursor: move;">
        <h3 class="card-title">{{ __('project.all_comments') }}</h3>
    </div>
    <div class="card-body">
        <div class="direct-chat-messages">
            @if($comments->isNotEmpty())
                @foreach($comments as $comment)
                    @php
                        $filename = explode('/', $comment->file);
                        $filename = end($filename);
                    @endphp
                    <div class="direct-chat-msg">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left">{{ $comment->user->name }}</span>
                            <span
                                class="direct-chat-timestamp float-right">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <img class="direct-chat-img"
                             src="{{ \Illuminate\Support\Facades\Storage::url($comment->user->avatar) }}">
                        <div class="direct-chat-text">
                            @php
                                $pos = strripos($comment->comment, auth()->user()->name);
                                if ($pos !== false) {
                                    $comment_text = str_replace(auth()->user()->name,'<i>'.auth()->user()->name.'</i>', $comment->comment);
                                }
                                else $comment_text = $comment->comment;
                            @endphp
                            @if(auth()->user()->id == $comment->user_id)
                                <div class="d-none" id="comment_editable_{{ $comment->id }}">
                                    <a class="xedit_edit_comment editable editable-click cursor-pointer" data-type='textarea' data-title='Изменить комментарий' data-pk="{{ $comment->id }}" id="editable_comment_link_{{ $comment->id }}" data-name="comment" data-model="ProjectComment">
                                        @nl2br($comment_text)
                                    </a>
                                </div>
                                <div id="comment_default_{{ $comment->id }}" data-editable="true">
                                    @nl2br($comment_text)
                                </div>
                            @else
                                @nl2br($comment_text)
                            @endif
                            @if($comment->answer_to != '')
                                <div class="direct-chat-text-light">
                                    <small>
                                        @if(!empty($comment->answered_comment->comment))
                                            {{ optional(userInfo($comment->answered_comment->user_id))->name }}: "{{ $comment->answered_comment->comment }}"
                                        @elseif(!empty($comment->answered_comment->file))
                                            @php
                                                $answered_comment_filename = explode('/', $comment->answered_comment->file);
                                                $answered_comment_filename = end($answered_comment_filename);
                                            @endphp
                                            @if($comment->answered_comment->comment != '')
                                                <br>
                                            @endif
                                            <small>
                                                <small>
                                                    <a href="{{ Storage::url($comment->answered_comment->file) }}" class="link-black text-sm" download>
                                                        <i class="fas fa-link mr-1"></i> {{ $answered_comment_filename }}</a>
                                                </small>
                                            </small>
                                        @else
                                            {{ __('project.comment_was_deleted') }}
                                        @endif
                                    </small>
                                </div>
                            @endif
                            @if (!is_null($comment->file))
                                <div class="mt-2">
                                    <small>
                                        <a href="{{ Storage::url($comment->file) }}" class="link-black text-sm" download>
                                            <i class="fas fa-link mr-1"></i> {{ $filename }}</a>
                                    </small>
                                </div>
                            @endif
                            <div class="mt-2">
                                <small>
                                    @if(auth()->user()->id != $comment->user_id)
                                        <a class="text-dark project_handler_comments cursor-pointer"
                                           data-action="answer"
                                           data-answer_to="{{ $comment->id }}"
                                           data-notify_user="{{ $comment->user_id }}"
                                           data-name="{{ optional(userInfo($comment->user_id))->name }}">
                                            {{ __('general.answer') }}
                                        </a>
                                    @endif
                                    @if(auth()->user()->id == $comment->user_id)
                                        <a class="text-dark project_handler_comments cursor-pointer"
                                           data-action="change_comment"
                                           data-comment_id="{{ $comment->id }}">
                                            {{ __('general.change') }}
                                        </a>
                                    @endif
                                    @if(auth()->user()->id == $comment->user_id)
                                        <a class="text-red project_handler_comments cursor-pointer"
                                           data-action="delete_chat_record"
                                           data-comment_id="{{ $comment->id }}">
                                            {{ __('general.remove') }}
                                        </a>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="ml-2">
                    {{ __('project.no_comments_for_this_project') }}
                </div>
            @endif
        </div>
    </div>
</div>
