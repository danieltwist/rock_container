<a class="text-dark" href="{{ route('client.show', $client->id) }}">{{ $client->name }}</a><br>
<small>
    @include('settings.country_switch', ['country' => $client->country])
    <br>
    {{ __('general.added') }} {{ $client->created_at->format('d.m.Y') }}
</small>
