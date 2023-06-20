{{ $reason }}
@if(!empty($not_finished_applications))
    <div class="mt-3">
        <ul>
            @foreach($not_finished_applications as $not_finished_application)
                @if(!is_null($not_finished_application['application_id']))
                    <li><a class="text-dark" href="/application/{{ $not_finished_application['application_id'] }}" target="_blank">Заявка {{ $not_finished_application['application_name'] }} типа {{ $not_finished_application['application_type'] }} / контейнер {{ $not_finished_application['container_name'] }}</a></li>
                @else
                    <li>Контейнер {{ $not_finished_application['container_name'] }}</li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
