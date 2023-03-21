<b><a class="text-dark" href="{{ route('project.show', $project['id']) }}">
        {{ $project->name }}
    </a></b>
@if ($project->status == 'Завершен')
    @if($project->paid != 'Не оплачен')
        <i class="fas fa-check-circle"></i>
    @else
        - {{ $project->paid }}
    @endif
@endif
<br>
<small>
    @if(!is_null(optional($project->user)->name))
        {{ __('project.created') }}: <a class="text-dark"
                                        href="{{ route('get_user_statistic', optional($project->user)->id) }}">{{ optional($project->user)->name}}</a> {{ $project->created_at }}
    @endif
    @if(!is_null(optional($project->manager)->name) || !is_null(optional($project->logist)->name))
        <br>
    @endif
    @if($project->manager_id != '')
        @if(!is_null(optional($project->manager)->name))
            {{ __('project.manager') }}: <a class="text-dark"
                                              href="{{ route('get_user_statistic', optional($project->manager)->id) }}">{{ optional($project->manager)->name}}</a>
        @endif
    @endif
    @if($project->logist_id != '')
        @if(!is_null(optional($project->logist)->name))
            / {{ __('project.logist') }}: <a class="text-dark"
                                             href="{{ route('get_user_statistic', optional($project->logist)->id) }}">{{ optional($project->logist)->name}}</a>
        @endif
    @endif
</small>
<br>
<br>
<small>{{ __('project.project_finished_percent') }} {{ $project->complete_level }}%</small>
<div class="progress">
    <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
         aria-valuenow="{{ $project->complete_level }}" aria-valuemin="0" aria-valuemax="100"
         style="width: {{ $project->complete_level }}%">
        <span class="sr-only">{{ $project->complete_level }}% {{ __('project.project_finished_percent_simple') }}</span>
    </div>
</div>
<div class="mt-2">
    @if($project->active == '1')
        @if($project->status == 'Черновик')
            @if(can_edit_this_project_price($project->id))
                <form class="button-delete-inline" action="{{ route('set_status_in_work', $project['id']) }}"
                      method="POST">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <button type="submit" class="btn btn-sm bg-primary">
                        <i class="fas fa-check"></i>
                        {{ __('project.set_status_in_work') }}
                    </button>
                </form>
            @endif
        @else
            @if ($project->active_block != '')
                <small>
                    {{ __('project.current_stage') }}:
                    <b>
                        @if ($project->active_block != '')
                            {{ $project->active_block->name }} {{ $project->active_block->status }}
                        @else
                            {{ __('general.not_set') }}
                        @endif
                    </b>
                </small>
            @endif
        @endif
    @else
        {{ __('project.finish_date') }}:
        @if (canWorkWithProject($project->id))
            <a href="#" class="xedit" data-pk="{{$project->id}}" data-name="finished_at" data-model="Project">
                {{ $project->finished_at }}
            </a>
        @else
            {{ $project->finished_at }}
        @endif
    @endif
</div>
