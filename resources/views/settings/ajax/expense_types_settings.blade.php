<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Виды расходов</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>{{ __('general.name') }}</th>
                        <th style="width: 10%">{{ __('general.removing') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($expense_types as $expense_type)
                        @if($expense_type->type == 'category')
                            <tr>
                                <td>{{ $expense_type->name }}</td>
                                <td>
                                    <form class="inline-block" action="{{ route('expense_type.destroy', $expense_type->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm delete-btn"
                                                data-action='{"update_div":{"div_id": "expense_types_settings_div"},"select2_init":{"need_init":"true"}}'>
                                            {{ __('general.remove') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Типы расходов</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>{{ __('general.name') }}</th>
                        <th>Вид</th>
                        <th style="width: 10%">{{ __('general.removing') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($expense_types as $expense_type)
                        @if($expense_type->type == 'type')
                            <tr>
                                <td>{{ $expense_type->name }}</td>
                                <td>{{ $expense_type->category }}</td>
                                <td>
                                    <form class="inline-block" action="{{ route('expense_type.destroy', $expense_type->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm delete-btn"
                                                data-action='{"update_div":{"div_id": "expense_types_settings_div"},"select2_init":{"need_init":"true"}}'>
                                            {{ __('general.remove') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="col-md-6">
        <form action="{{ route('expense_type.store') }}" method="POST">
            <input type="hidden" name="type" value="add_expense_category">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Добавить вид расходов</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Название</label>
                        <input type="text" class="form-control" name="name"
                               placeholder="Название">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"update_div":{"div_id": "expense_types_settings_div"},"select2_init":{"need_init":"true"}}'>
                        {{ __('general.add') }}
                    </button>
                </div>
            </div>
        </form>
        <form action="{{ route('expense_type.store') }}" method="POST">
            <input type="hidden" name="type" value="add_expense_type">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Добавить тип расходов</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Название</label>
                        <input type="text" class="form-control" name="name"
                               placeholder="Название">
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="expense_category">Вид расходов</label>
                        <select class="form-control select2" name="category"
                                data-placeholder="Выберите вид расходов" style="width: 100%;">
                            <option></option>
                            @foreach($expense_types as $expense_type)
                                @if($expense_type->type == 'category')
                                    <option value="{{ $expense_type->name }}">{{ $expense_type->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"update_div":{"div_id": "expense_types_settings_div"},"select2_init":{"need_init":"true"}}'>
                        {{ __('general.add') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
