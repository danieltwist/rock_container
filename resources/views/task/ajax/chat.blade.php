@if(!is_null($task->comment))
    @foreach(unserialize($task->comment) as $key=>$comment)
        <div class="card-comment mt-2">
            <img class="img-circle img-sm"
                 src="{{ Storage::url(optional(userInfo($comment['user']))->avatar) }}"
                 alt="User Image">
            <div class="comment-text">
                <span class="username">
                    {{ optional(userInfo($comment['user']))->name }}
                  <span class="text-muted float-right">{{ \Carbon\Carbon::parse($comment['date'])->format('d.m.Y H:i:s') }}</span>
                </span>
                @php
                    $pos = strripos($comment['text'], auth()->user()->name);
                    if ($pos !== false) {
                        $comment_text = str_replace(auth()->user()->name,'<i>'.auth()->user()->name.'</i>', $comment['text']);
                    }
                    else $comment_text = $comment['text'];
                @endphp
                @if(auth()->user()->id == $comment['user'])
                    <div class="d-none" id="comment_editable_{{ $key }}">
                        <a class="xedit_edit_comment editable editable-click cursor-pointer"
                           data-type='textarea'
                           data-title='{{ __('task.change_comment') }}'
                           data-pk="{{ $key }}"
                           data-task_id="{{ $task->id }}"
                           id="editable_comment_link_{{ $key }}"
                           data-name="comment"
                           data-model="TaskComment">
                            @nl2br($comment_text)
                        </a>
                    </div>
                    <div id="comment_default_{{ $key }}" data-editable="true">
                        @nl2br($comment_text)
                    </div>
                @else
                    @nl2br($comment_text)
                @endif
                @if($comment['answer_to'] != '')
                    <div class="direct-chat-text-light">
                        <small>
                            @if(!empty(unserialize($task->comment)[$comment['answer_to']]['text']))
                                {{ optional(userInfo(unserialize($task->comment)[$comment['answer_to']]['user']))->name }}
                                :
                                "{{ unserialize($task->comment)[$comment['answer_to']]['text'] }}
                                "
                            @elseif(!empty(unserialize($task->comment)[$comment['answer_to']]['file']))
                                @if(unserialize($task->comment)[$comment['answer_to']]['text'] != '')
                                    <br>
                                @endif
                                <small>{{ __('task.added_files') }}:
                                    @foreach(unserialize($task->comment)[$comment['answer_to']]['file'] as $file)
                                        <a href="{{ Storage::url($file['url']) }}"
                                           download>{{ $file['name'] }}</a>
                                    @endforeach
                                </small>
                            @else
                                {{ __('task.comment_was_deleted') }}
                            @endif
                        </small>
                    </div>
                @endif
                @if($comment['file'] != '')
                    <small>
                        @foreach($comment['file'] as $file)
                            <br><a href="{{ Storage::url($file['url']) }}" class="link-black text-sm" download>
                                <i class="fas fa-link mr-1"></i> {{ $file['name'] }}
                            </a>
                        @endforeach
                    </small>
                @endif
                <div class="mt-2">
                    <small>
                        @if(auth()->user()->id != $comment['user'])
                            <a class="text-dark task_handler_answer cursor-pointer"
                               data-answer_to="{{ $key }}"
                               data-notify_user="{{ $comment['user'] }}"
                               data-name="{{ optional(userInfo($comment['user']))->name }}">{{ __('task.answer') }}
                            </a>
                        @endif
                        @if(auth()->user()->id == $comment['user'])
                            @if($comment['text'] != '')
                                <a class="text-dark project_handler_comments cursor-pointer"
                                   data-action="change_comment"
                                   data-comment_id="{{ $key }}">{{ __('general.change') }}
                                </a>
                            @endif
                        @endif
                        @if(auth()->user()->id == $comment['user'])
                            <a class="text-red task_handler_comments cursor-pointer delete-btn"
                               data-task_id="{{ $task->id }}"
                               data-action="delete_chat_record"
                               data-message_id="{{ $key }}">
                                {{ __('general.remove') }}
                            </a>
                        @endif
                    </small>
                </div>
            </div>
        </div>
    @endforeach
@else
    {{ __('task.no_comments') }}
@endif
