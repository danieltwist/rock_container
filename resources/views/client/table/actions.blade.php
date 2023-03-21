<a class="btn btn-app bg-indigo" href="{{ route('client.edit', $client->id) }}" >
    <i class="fas fa-pencil-alt">
    </i>
    {{ __('general.change') }}
</a>
<form class="button-delete-inline"
      action="{{ route('client.destroy', $client->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-app bg-danger delete-btn">
        <i class="fas fa-trash">
        </i>
        {{ __('general.remove') }}
    </button>
</form>
