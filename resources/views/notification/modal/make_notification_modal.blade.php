<div class="modal fade" id="make_notification_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('notification.send_notification') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('notification.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @livewire('create-notification')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right">{{ __('general.send') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
