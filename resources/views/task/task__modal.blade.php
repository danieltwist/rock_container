<div class="modal fade" id="create_task_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('task.create_task') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" id="create_task_modal_content">
                @livewire('createtask')
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"
                        data-action='{"hide_modal":{"id": "create_task_modal"},"redirect_url":{"need_redirect": "true"}}'>
                    {{ __('task.create_task') }}
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
