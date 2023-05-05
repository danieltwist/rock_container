@if(!is_null($audit->before_edit))
    @foreach($audit->before_edit as $key => $value)
        <strong>{{ $value['column'] }}:</strong> {!! $value['text'] !!}<br>
    @endforeach
@endif
