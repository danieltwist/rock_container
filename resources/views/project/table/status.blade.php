@php
    switch($project->status){
        case 'В работе':
            $class = 'primary';
            break;
        case 'Черновик':
            $class = 'info';
            break;
        case 'Завершен':
            $class = 'success';
            break;
        default:
            $class = 'secondary';
    }
@endphp
<span class="badge badge-{{ $class }}">
    @include('project.status_switch', ['status' => $project->status])
</span>
