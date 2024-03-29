<div class="form-group">
    <label for="containers_used">Список контейнеров (определено: {{ count($containers) }})</label>
    <select class="form-control select2" id="containers_used" name="containers[]" multiple
            data-placeholder="Выберите контейнеры" style="width: 100%;">
        <option></option>
        @foreach($containers as $container)
            <option value="{{ $container }}" selected>{{ $container }}</option>
        @endforeach
    </select>
    @if(!is_null($not_found))
        <div class="mt-2">
            <strong>Не найдены в пуле контейнеров: </strong>
            <br>{{ implode(', ', $not_found) }}
        </div>
    @endif
    @if(!is_null($not_correct_format))
        <div class="mt-2">
            <strong>Неправильный формат: </strong>
            <br>{{ implode(', ', $not_correct_format) }}
        </div>
    @endif
    @if(!is_null($already_used))
        <div class="mt-2">
            <strong>Используются в других заявках: <br></strong>
            @foreach($already_used as $key => $value)
                {{ $value['container_name'] }} - <a class="text-dark" href="/application/{{ $value['application_id'] }}" target="_blank">в заявке {{ $value['application_name'] }}</a>
                {!! $key != array_key_last($already_used) ? '<br>' : '' !!}
            @endforeach
        </div>
    @endif
    @if(!is_null($can_reuse))
        <div class="mt-2">
            <strong>Используются, но имеют дату сдачи: <br></strong>
            @foreach($can_reuse as $key => $value)
                {{ $value['container_name'] }} - <a class="text-dark" href="/application/{{ $value['application_id'] }}" target="_blank">в заявке {{ $value['application_name'] }}</a>
                / <a class="text-dark cursor-pointer reuse_container"
                     data-application_id="{{ $value['application_id'] }}"
                     data-container_id="{{ $value['container_id'] }}">
                    Перенести в архив и использовать в данной заявке
                </a>
                {!! $key != array_key_last($can_reuse) ? '<br>' : '' !!}
            @endforeach
        </div>
    @endif
</div>
