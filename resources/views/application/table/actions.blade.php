<a class="btn btn-app bg-primary" href="{{ route('application.show', $application->id) }}">
    <i class="far fa-eye">
    </i>
    {{ __('general.go') }}
</a>
@can ('edit projects')
    <a class="btn btn-app bg-indigo" href="{{ route('application.edit', $application->id) }}">
        <i class="fas fa-pencil-alt">
        </i>
        {{ __('general.change') }}
    </a>
@endcan
@can ('remove projects')
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
@endcan
