<div id="collapse_task_text_compact_{{ $id }}">
    <a href="{{ $route }}">{{ \Illuminate\Support\Str::limit($text, 30, $end='...') }}</a>
    <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_full_{{ $id }}">
        <i class="fa fa-angle-down"></i>
    </a>
</div>
<div id="collapse_task_text_full_{{ $id }}" class="d-none">
    <a href="{{ $route }}">{{ $text }}</a>
    <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_compact_{{ $id }}">
        <i class="fa fa-angle-up"></i>
    </a>
</div>
