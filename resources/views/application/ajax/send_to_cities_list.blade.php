<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="send_to_city">Город / Станция</label>
            <select class="form-control select2"
                    name="send_to_city[]"
                    id="send_to_city"
                    required
                    multiple
                    data-placeholder="Выберите города" style="width: 100%;" >
                <option></option>
                @if(!is_null($cities[0]))
                    @foreach($cities[0] as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Добавить в список</label>
            <div class="input-group">
                <input type="text" class="form-control to_uppercase" id="send_to_city_add_city" placeholder="Добавить в список" aria-label="Добавить в список">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary add_city_to_country" data-country_type="send_to" type="button">Добавить</button>
                </div>
            </div>
        </div>
    </div>
</div>
