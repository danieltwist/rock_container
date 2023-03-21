@if(empty($chosen_containers_id))
    <strong>Ни один контейнер не выбран</strong>
@else
    {{ $containers_names }}
@endif
@if($excluded_containers_exist)
    <br> <strong>Часть контейнеров заблокирована для редактирования </strong>
    @foreach($exclude_from_list as $item)
        {{ $item['name'] }} - {{ $item['reason'] }} пользователем {{ $item['user'] }}<br>
    @endforeach
@endif
