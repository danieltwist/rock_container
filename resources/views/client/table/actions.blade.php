<a class="btn btn-app bg-indigo" href="{{ route('client.edit', $client->id) }}" >
    <i class="fas fa-pencil-alt">
    </i>
    {{ __('general.change') }}
</a>
@if(!is_null($client->deleted_at))
    <button
        class="btn btn-app bg-warning ajax-restore-row"
        data-action="restore_row"
        data-object="client"
        data-type="ajax"
        data-object-id="{{ $client->id }}">
        <i class="fas fa-trash-restore"></i>
        Восстановить
    </button>
@else
    <button
        class="btn btn-app bg-danger ajax-delete-row"
        data-action="delete_row"
        data-object="client"
        data-type="ajax"
        data-object-id="{{ $client->id }}">
        <i class="fas fa-trash">
        </i>
        {{ __('general.remove') }}
    </button>
@endif
