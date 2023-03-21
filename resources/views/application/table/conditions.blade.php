Стоимость: {{ $application->price_amount }}{{ $application->price_currency }}<br>
Льготный период: {{ $application->grace_period }} дней<br>
СНП: {{ $application->snp_after_range }}{{ $application->snp_currency }}
@if(!is_null($application->snp_range))
    <br>
    <button class="btn btn-primary btn-sm mt-2" type="button"
            data-toggle="collapse"
            data-target="#collapseSNPconditions{{ $application->id }}"
            aria-expanded="false"
            aria-controls="collapseExample">
        <i class="fa fa-angle-down"></i>
        Прогрессивный СНП
    </button>
    <div class="collapse mt-2" id="collapseSNPconditions{{ $application->id }}">
        <div class="card card-body">
            @foreach($application->snp_range as $range)
                {{ $range['range'] }} день - {{ $range['price'] }}{{ $application->snp_currency }}<br>
            @endforeach
        </div>
    </div>
@endif
