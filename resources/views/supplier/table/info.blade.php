@if ($supplier->additional_info !='')
    {{ $supplier->additional_info }}
    <br>
@endif
@if ($supplier->card !='')
    <a href="{{ Storage::url($supplier->card) }}" download>{{ __('general.counterparty_card') }}</a>
@endif
@if($supplier->contracts->isNotEmpty())
    <br>Договоры:<br>
    @foreach($supplier->contracts as $contract)
        <a href="{{ Storage::url($contract->file) }}" download>{{ $contract->name }} {{ $contract->additional_info }}</a><br>
    @endforeach
@endif
