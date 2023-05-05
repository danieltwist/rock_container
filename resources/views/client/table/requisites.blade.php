<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseRequisites{{$client->id}}" aria-expanded="false" aria-controls="collapseExample">
    {{ __('general.shows_requisites') }}
</button>
<div class="collapse mt-2" id="collapseRequisites{{$client->id}}">
    <div class="card card-body">
        {{ __('general.inn_license_number') }}: {{ $client['inn'] }}
        <br>
        @nl2br($client->requisites)
        <br>
        E-mail: {{ $client->email }}
    </div>
</div>
