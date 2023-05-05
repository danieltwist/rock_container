Количество по заявке: {{ $application->containers_amount }}
@if(!is_null($application->containers))
    <br>
    <button class="btn btn-primary btn-sm mt-2" type="button"
            data-toggle="collapse"
            data-target="#collapseContainers{{ $application->id }}"
            aria-expanded="false"
            aria-controls="collapseExample">
        <i class="fa fa-angle-down"></i>
        Показать список ({{ count($application->containers) }})
    </button>
    <div class="collapse mt-2" id="collapseContainers{{ $application->id }}">
        <div class="card card-body">
            {{ implode(', ', $application->containers) }}
        </div>
    </div>
@endif
