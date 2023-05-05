@if(!is_null($audit->after_edit))
    @foreach($audit->after_edit as $key => $value)
        <strong>{{ $value['column'] }}:</strong> {!! $value['text'] !!}<br>
    @endforeach
@endif
