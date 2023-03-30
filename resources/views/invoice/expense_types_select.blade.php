<div class="form-group">
    <label for="expense_type">Тип расходов</label>
    <select class="form-control select2" name="expense_type"
            data-placeholder="Выберите тип расходов" style="width: 100%;">
        <option></option>
        @foreach($expense_types as $expense_type)
            @if($expense_type->type == 'type')
                <option value="{{ $expense_type->name }}">{{ $expense_type->name }}</option>
            @endif
        @endforeach
    </select>
</div>
