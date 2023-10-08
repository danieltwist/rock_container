@if(!is_null($from))
    @if(mb_strlen($from)>25)
        <div id="collapse_task_text_compact_from_{{ $application->id }}">
            {{ \Illuminate\Support\Str::limit($from, 25, $end='...') }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_full_from_{{ $application->id }}">
                <i class="fa fa-angle-down"></i>
            </a>
        </div>
        <div id="collapse_task_text_full_from_{{ $application->id }}" class="d-none">
            {{ $from }}<br>
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_compact_from_{{ $application->id }}">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    @else
        {{ $from }}<br>
    @endif
@endif
@if(!is_null($to))
    @if(mb_strlen($to)>25)
        <div id="collapse_task_text_compact_to_{{ $application->id }}">
            {{ \Illuminate\Support\Str::limit($to, 25, $end='...') }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_full_to_{{ $application->id }}">
                <i class="fa fa-angle-down"></i>
            </a>
        </div>
        <div id="collapse_task_text_full_to_{{ $application->id }}" class="d-none">
            {{ $to }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_compact_to_{{ $application->id }}">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    @else
        {{ $to }}
    @endif
@endif
@if(!is_null($place_of_delivery))
    @if(mb_strlen($place_of_delivery)>25)
        <div id="collapse_task_text_compact_place_of_delivery_{{ $application->id }}">
            {{ \Illuminate\Support\Str::limit($place_of_delivery, 25, $end='...') }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_full_place_of_delivery_{{ $application->id }}">
                <i class="fa fa-angle-down"></i>
            </a>
        </div>
        <div id="collapse_task_text_full_place_of_delivery_{{ $application->id }}" class="d-none">
            {{ $place_of_delivery }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_compact_place_of_delivery_{{ $application->id }}">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    @else
        <br>{{ $place_of_delivery }}
    @endif
@endif
