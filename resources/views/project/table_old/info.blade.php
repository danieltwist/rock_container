@if(!is_null($project->client))
    <a class="text-dark" href="{{ route('client.show', $project->client->id) }}">{{ __('general.client') }}: {{ $project->client->name }}</a><br>
@else
    {{ __('general.client_was_deleted') }}<br><br>
@endif
@if($project->additional_clients != '')
    <button class="btn btn-primary btn-sm mt-2" type="button"
            data-toggle="collapse"
            data-target="#collapseAdditionalClients{{$project->id}}"
            aria-expanded="false"
            aria-controls="collapseExample">
        <i class="fa fa-angle-down"></i>
        {{ __('project.additional_clients') }}
    </button>
    <div class="collapse mt-2" id="collapseAdditionalClients{{$project->id}}">
        <div class="card card-body">
            <div class="text-muted">
                <ul>
                    @foreach($project->additional_client() as $client)
                        <li>{{ $client }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
<br><small>{{ __('project.direction') }}: {{ $project->from }} - {{ $project->to }}<br>
    {{ __('project.goods_info') }}: {{ $project->freight_info }} {{ $project->freight_amount }}
    @if ($project->additional_info!='')
        @if(mb_strlen($project->additional_info)>100)
            <div id="collapse_task_text_compact_{{ $project->id }}">
                <br>{{ __('general.additional_info') }}: {{ \Illuminate\Support\Str::limit($project->additional_info, 100, $end='...') }}
                <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_full_{{ $project->id }}"><i class="fa fa-angle-down"></i> {{ __('general.expand') }}</a>
            </div>
            <div id="collapse_task_text_full_{{ $project->id }}" class="d-none">
                <br>{{ __('general.additional_info') }}: {{ $project->additional_info }}
                <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_compact_{{ $project->id }}"><i class="fa fa-angle-up"></i> {{ __('general.collapse') }}</a>
            </div>
        @else
            <br>{{ __('general.additional_info') }}: {{ $project->additional_info }}
        @endif
    @endif
</small>
