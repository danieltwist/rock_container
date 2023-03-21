@php $file_exist = false; @endphp
@if(!is_null($task->comment))
    @foreach(unserialize($task->comment) as $comment)
        @if($comment['file']!= '')
            <div class="card-comment mt-2">
                <div class="comment-text">
                    <span class="username">
                        {{ optional(userInfo($comment['user']))->name }}
                      <span
                          class="text-muted float-right">{{ $comment['date'] }}</span>
                    </span><!-- /.username -->
                    @if(!is_null($comment['file']))
                        <br>
                        <ul>
                            @foreach($comment['file'] as $file)
                                <li><a href="{{ Storage::url($file['url']) }}"
                                       download>{{ $file['name'] }}</a></li>
                            @endforeach
                            <ul>
                    @endif
                </div>
            </div>
            @php $file_exist = true; @endphp
        @endif
    @endforeach
@endif
@if($file_exist === false)
    {{ __('work_request.no_files') }}
@endif
