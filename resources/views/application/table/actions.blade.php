<a class="btn btn-app bg-primary" href="{{ route('application.show', $application->id) }}">
    <i class="far fa-eye">
    </i>
    {{ __('general.go') }}
</a>
@can ('edit projects')
    <a class="btn btn-app bg-indigo" href="
    @if(in_array($application->type, ['Покупка', 'Продажа']))
        {{ route('buy_sell_edit', $application->id) }}
    @else
        {{ route('application.edit', $application->id) }}
    @endif">
        <i class="fas fa-pencil-alt">
        </i>
        {{ __('general.change') }}
    </a>
@endcan
@can ('remove projects')
    @if(!is_null($application->deleted_at))
        <button
            class="btn btn-app bg-warning ajax-restore-row"
            data-action="restore_row"
            data-object="application"
            data-type="ajax"
            data-object-id="{{ $application->id }}">
            <i class="fas fa-trash-restore"></i>
            Восстановить
        </button>
    @else
        <button
            class="btn btn-app bg-danger ajax-delete-row"
            data-action="delete_row"
            data-object="application"
            data-type="ajax"
            data-object-id="{{ $application->id }}">
            <i class="fas fa-trash">
            </i>
            {{ __('general.remove') }}
        </button>
    @endif
@endcan
