@if ($container->usage_statistic->isNotEmpty())
    <a href="#" class="xedit"
       data-pk="{{ $container->usage_statistic[0]->id }}"
       data-name="return_date"
       data-model="ContainerUsageStatistic">
        {{ $container->usage_statistic[0]->return_date }}
    </a>
@elseif (($container->start_date_for_us !='' || $container->start_date_for_client !='') && $container->problem_id =='')
    @if (canWorkWithProject($group->project_id))
        <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="return_container">
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-sm invoice-confirm-btn"
                        data-action='{"update_table":{"table_id":"containers_group_{{ $group->id }}"}}'>
                    {{ __('container.make_return_table') }}
                </button>
            </div>
        </form>
    @endif
@elseif($container->problem_id !='')
    {{ __('container.problem_found') }}
@else
    {{ __('container.start_date_not_set') }}
@endif
