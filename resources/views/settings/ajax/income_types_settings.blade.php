<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('settings.income_categories') }}</h3>
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
                    @foreach($income_types as $income_type)
                        @if($income_type->type == 'category')
                            <tr>
                                <td>
                                    <a href="#" class="xedit"
                                       data-pk="{{ $income_type->id }}"
                                       data-name="name"
                                       data-model="incomeType">
                                        {{ $income_type->name }}
                                    </a>
                                </td>
                                <td>
                                    <form class="inline-block" action="{{ route('income_type.destroy', $income_type->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                data-action='{"update_div":{"div_id": "income_types_settings_div"},"select2_init":{"need_init":"true"},"xedit_init":{"need_init":"true"}}'>
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
                <h3 class="card-title">{{ __('settings.income_types') }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>{{ __('general.name') }}</th>
                        <th>{{ __('settings.type') }}</th>
                        <th style="width: 10%">{{ __('general.removing') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($income_types as $income_type)
                        @if($income_type->type == 'type')
                            <tr>
                                <td>
                                    <a href="#" class="xedit"
                                       data-pk="{{ $income_type->id }}"
                                       data-name="name"
                                       data-model="incomeType">
                                        {{ $income_type->name }}
                                    </a>
                                </td>
                                <td>{{ $income_type->category }}</td>
                                <td>
                                    <form class="inline-block" action="{{ route('income_type.destroy', $income_type->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                data-action='{"update_div":{"div_id": "income_types_settings_div"},"select2_init":{"need_init":"true"},"xedit_init":{"need_init":"true"}}'>
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
        <form action="{{ route('income_type.store') }}" method="POST">
            <input type="hidden" name="type" value="add_income_category">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('settings.add_income_category') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('general.name') }}</label>
                        <input type="text" class="form-control" name="name"
                               placeholder="{{ __('general.name') }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"update_div":{"div_id": "income_types_settings_div"},"select2_init":{"need_init":"true"}}'>
                        {{ __('general.add') }}
                    </button>
                </div>
            </div>
        </form>
        <form action="{{ route('income_type.store') }}" method="POST">
            <input type="hidden" name="type" value="add_income_type">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('settings.add_income_type') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('general.name') }}</label>
                        <input type="text" class="form-control" name="name"
                               placeholder="{{ __('general.name') }}">
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="income_category">{{ __('invoice.income_category') }}</label>
                        <select class="form-control select2" name="category"
                                data-placeholder="{{ __('invoice.income_category') }}" style="width: 100%;">
                            <option></option>
                            @foreach($income_types as $income_type)
                                @if($income_type->type == 'category')
                                    <option value="{{ $income_type->name }}">{{ $income_type->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"update_div":{"div_id": "income_types_settings_div"},"select2_init":{"need_init":"true"}}'>
                        {{ __('general.add') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
