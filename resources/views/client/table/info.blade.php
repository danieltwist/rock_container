@if ($client->additional_info !='')
    {{ $client->additional_info }}
    <br>
@endif
@if ($client->card !='')
    <a href="{{ Storage::url($client->card) }}" download>{{ __('general.counterparty_card') }}</a>
@endif
@if($client->contracts->isNotEmpty())
    <br>{{ __('general.contracts') }}:<br>
    @foreach($client->contracts as $contract)
        <a href="{{ Storage::url($contract->file) }}" download>
            {{ $contract->name }} {{ $contract->additional_info }}
        </a><br>
    @endforeach
@endif
