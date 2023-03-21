<table class="table table-striped data_tables">
    <thead>
    <tr>
        <th style="width: 1%">
            #
        </th>
        <th style="width: 15%">
            {{ __('general.container') }}
        </th>
        <th style="width: 20%">
            {{ __('general.client') }}
        </th>
        <th>
            {{ __('container.project_dates') }}
        </th>
        <th style="width: 20%">
            {{ __('container.project_finances') }}
        </th>
        <th style="width: 10%">
            {{ __('container.project_status_table') }}
        </th>
        <th style="width: 15%">
            {{ __('general.actions') }}
        </th>
    </tr>
    </thead>
    <tbody>

    @foreach($container_projects as $project)
        <tr>
            <td>{{ $project->id }}</td>
            <td>
                @if(!is_null(optional(optional($project->container))->name))
                    <a href="{{ route('container.show', $project->container_id) }}">
                    {{ optional(optional($project->container))->name }}
                    </a>
                    <br>
                @endif
                @if($project->project_id != '')
                    {{ __('project.project') }}: <a href="{{ route('project.show', $project->project_id) }}">
                        {{ optional(optional($project->project))->name }}
                    </a>
                @endif
            </td>
            <td>{{ optional(optional($project->client))->name }}<br>
                {{ $project->start_place }} - {{ $project->place_of_arrival }}
            </td>
            <td>{{ __('container.project_date_departure') }}: {{ $project->date_departure != '' ? $project->date_departure : '-'}}<br>
                {{ __('container.project_date_of_arrival') }}: {{ $project->date_of_arrival != '' ? $project->date_of_arrival : '-'}}<br>
                {{ __('container.project_days_on_the_way') }}: {{ $project->info['on_the_way'] }}
            </td>
            <td>
                {{ __('container.project_rate_for_client_') }}: {{ $project->rate_for_client_rub }}р. <br>
                {{ __('container.snp') }}: {{ $project->info['snp_days'] != 0 ? $project->info['snp_days'].' ' . __('container.days') . ' ('.$project->info['snp_days'] * $project->snp_rub.'р.)' : '-' }} <br>
                {{ __('container.project_paid_by_client') }}: {{ $project->paid_rub !='' ? $project->paid_rub.'р.' : '-'}}
            </td>
            <td><span class="badge badge-{{ $project->class }}">{{ $project->status }}</span></td>
            <td>
                <a class="btn btn-app bg-primary" href="{{ route('container_project.show', $project->id) }}">
                    <i class="far fa-eye">
                    </i>
                    {{ __('general.go') }}
                </a>
                @can ('remove projects')
                    <form class="button-delete-inline" action="{{ route('container_project.destroy', $project->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="action" value="remove_container_project">
                        <button type="submit" class="btn btn-app bg-danger delete-btn">
                            <i class="fas fa-trash">
                            </i>
                            {{ __('general.remove') }}
                        </button>
                    </form>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
