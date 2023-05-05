<a href="{{ route('application.show', $application->id) }}" class="text-dark">{{ $application->name }}</a><br>
<small>{{ $application->type }}<br>
    {{ $application->created_at->format('d.m.Y') }}
</small>
