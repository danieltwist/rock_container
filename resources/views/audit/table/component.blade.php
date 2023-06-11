@if($audit->auditable_type != 'App\Models\User')
    <a class="text-dark" href="{{ route($audit->component_route, $audit->auditable_id) }}">{{ $audit->component_name }}</a>
@else
    {{ $audit->component_name }}
@endif
