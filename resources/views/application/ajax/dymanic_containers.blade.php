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
</div>
