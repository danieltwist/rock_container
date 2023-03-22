@if(empty($chosen_containers_id))
    <strong>Ни один контейнер не выбран</strong>
@else
    <div class="form-group">
        <label for="containers_list">Выбраны для редактирования</label>
        <select class="form-control select2-containers_list"
                name="containers_list[]"
                id="containers_list"
                required
                multiple
                data-placeholder="Выберите контейнеры для редактирования" style="width: 100%;" >
            <option></option>
            @foreach($chosen_containers_id as $container_id)
                <option value="{{ $container_id }}" selected>{{ \App\Models\Container::find($container_id)->name }}</option>
            @endforeach
        </select>
    </div>
@endif
@if($excluded_containers_exist)
    <br> <strong>Часть контейнеров заблокирована для редактирования </strong>
    @foreach($exclude_from_list as $item)
        {{ $item['name'] }} - {{ $item['reason'] }} пользователем {{ $item['user'] }}<br>
    @endforeach
@endif
