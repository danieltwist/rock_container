<div class="modal fade" id="preview_application_invoices">
    <div class="modal-dialog modal-xl">
        <form action="{{ route('application_add_invoices') }}" method="POST">
            <input type="hidden" name="application_id" value="{{ $application->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Предпросмотр сгенерированных расходов / доходов</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="project_id">Выберите проект</label>
                        <select class="form-control select2" name="project_id"
                                data-placeholder="Выберите проект" style="width: 100%;" required>
                            <option></option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="invoices_for_application_div"></div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"hide_modal":{"id":"preview_application_invoices"},"update_table":{"table_id":"invoices_ajax_table_content_application", "type":"ajax"}}'>
                        Добавить выбранные
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
