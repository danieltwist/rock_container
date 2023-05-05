{{ $audit->created_at->format('d.m.Y H:i:s') }}<br>
<small>
    <a class="text-dark" href="{{ route('get_user_statistic', $audit->user_id) }}">{{ $audit->user_name }}</a><br>
    <strong><a class="text-dark" href="{{ route($audit->component_route, $audit->auditable_id) }}">{{ $audit->event }} {{ $audit->component_name }}</a></strong>
</small>
