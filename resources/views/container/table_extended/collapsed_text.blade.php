<div id="collapse_task_text_compact_{{ $id }}">
    {{ \Illuminate\Support\Str::limit($text, 25, $end='...') }}
    <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_full_{{ $id }}">
        <i class="fa fa-angle-down"></i>
    </a>
</div>
<div id="collapse_task_text_full_{{ $id }}" class="d-none">
    {{ $text }}
    <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_compact_{{ $id }}">
        <i class="fa fa-angle-up"></i>
    </a>
</div>
