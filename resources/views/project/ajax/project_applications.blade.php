@if ($applications->isNotEmpty())
    <table class="table table-striped mt-2 datatable_without_search">
        <thead>
        <tr>
            <th style="width: 10px">#</th>
            <th style="width: 30%">{{ __('project.client') }}</th>
            <th style="width: 30%">{{ __('project.contract') }}</th>
            <th>{{ __('project.application_file') }}</th>
            @can('remove invoices')
                <th>{{ __('general.removing') }}</th>
            @endcan
        </tr>
        </thead>
        <tbody>
        @foreach($applications as $application)
            <tr>
                <td>{{ $application->id }}</td>
                <td>{{ optional($application->client)->name }}</td>
                <td>
                    {{ optional($application->contract)->name }} - {{ __('project.valid_before') }} {{ optional($application->contract)->date_period }}<br>
                    {{ optional($application->contract)->additional_info }} <br>
                    <a href="{{ Storage::url(optional($application->contract)->file) }}"
                       download>{{ __('project.download_contract') }}</a>
                </td>
                <td><a class="btn btn-primary btn-sm"
                       href="{{ Storage::url($application->file) }}"
                       download>{{ __('project.download_application') }}</a></td>
                @can('remove invoices')
                    <td>
                        <form class="button-delete-inline"
                              action="{{ route('application.destroy', $application->id) }}"
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" value="{{ $application->project_id }}">
                            <button type="submit"
                                    class="btn btn-danger btn-sm delete-btn"
                                    data-action='{"update_div":{"div_id":"project_applications"}}'>
                                {{ __('project.remove_application') }}
                            </button>
                        </form>
                    </td>
                @endcan
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    {{ __('project.applications_not_found') }}
@endif
