Стоимость: <strong>{{ $application->price_amount }}{{ $application->price_currency }}</strong><br>
@if(!is_null($application->grace_period))
    Льготный период: <strong>{{ $application->grace_period }} дней</strong><br>
@endif
@if(!is_null($application->snp_after_range))
    СНП: <strong>{{ $application->snp_after_range }}{{ $application->snp_currency }}</strong>
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
                <strong>
                    @foreach($application->snp_range as $range)
                        {{ $range['range'] }} день - {{ $range['price'] }}{{ $application->snp_currency }}<br>
                    @endforeach
                </strong>
            </div>
        </div>
    @endif
@endif
