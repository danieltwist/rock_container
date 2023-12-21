<div class="form-group">
    <label for="income_type">Тип доходов</label>
    <select class="form-control select2" name="income_type"
            data-placeholder="Выберите тип доходов" style="width: 100%;">
        <option></option>
        @foreach($income_types as $income_type)
            @if($income_type->type == 'type')
                <option value="{{ $income_type->name }}">{{ $income_type->name }}</option>
            @endif
        @endforeach
    </select>
</div>
