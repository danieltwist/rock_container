<div class="form-group">
    <label for="contract_id">Договор</label>
    <select class="form-control select2" name="contract_id" required
            data-placeholder="Выберите договор" style="width: 100%;">
        <option></option>
        @foreach($contracts as $contract)
            <option value="{{ $contract->id }}">{{$contract->name}} от {{ $contract->date_start->format('d.m.Y') }}, действует до {{ $contract->date_period->format('d.m.Y') }}</option>
        @endforeach
    </select>
</div>
