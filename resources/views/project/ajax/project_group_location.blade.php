@if ($group->container_group_locations_list->isEmpty())
    {{ __('project.group_locations_list_is_empty') }}
@else
    @foreach($group->container_group_locations_list as $location)
        {{ $location->date }}: {{ $location->country }}, {{ $location->city }}
        {{ $location->additional_info}}<br>
    @endforeach
@endif
