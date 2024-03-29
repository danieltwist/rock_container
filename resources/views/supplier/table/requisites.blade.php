<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseRequisites{{$supplier->id}}" aria-expanded="false" aria-controls="collapseExample">
    {{ __('general.shows_requisites') }}
</button>

<div class="collapse mt-2" id="collapseRequisites{{$supplier->id}}">
    <div class="card card-body">
        {{ __('general.inn_license_number') }}: {{ $supplier->inn }}
        <br>
        @nl2br($supplier->requisites)
        <br>
        E-mail: {{ $supplier->email }}
    </div>
</div>
