<div class="modal fade" id="create_work_request_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('work_request.create_work_request') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('work_request.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" id="create_work_request_modal_content">
                    @livewire('create-work-request')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"
                            data-action='{"hide_modal":{"id": "create_work_request_modal"}}'>
                        {{ __('work_request.create_work_request') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
