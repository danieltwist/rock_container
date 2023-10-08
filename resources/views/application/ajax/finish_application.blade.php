@if($can_finish_application)
    <form class="button-delete-inline float-right" action="{{ route('archive_containers_usage_info') }}" method="POST">
        @csrf
        <input type="hidden" name="application_id" value="{{ $application->id }}">
        @if(in_array($application->type, ['Покупка', 'Клиент', 'Подсыл']))
            <button type="submit" class="btn bg-success mt-2 confirm-btn">
                <i class="fas fa-check"></i> Завершить работу с заявкой
            </button>
        @else

            <button type="submit" class="btn bg-success mt-2" onClick="return confirmSubmit()">
                <i class="fas fa-check"></i> Завершить и переместить контейнеры в архив
            </button>
        @endif
    </form>
@else
    @if($application->status == 'Завершена')
        <div class="mt-3 float-right">
            Дата завершения: {{ $application->finished_at->format('d.m.Y H:i:s') }}
        </div>
    @elseif($application->status == 'Черновик')
        @if(in_array($role, ['director', 'super-admin']))
            <form class="button-delete-inline float-right" action="{{ route('change_status_in_work') }}" method="POST">
                @csrf
                <input type="hidden" name="application_id" value="{{ $application->id }}">
                <button type="submit" class="btn bg-success mt-2">
                    <i class="fas fa-check"></i> Перевести в работу
                </button>
            </form>
        @endif
    @else
        <div class="float-right">
            <a class="btn bg-danger mt-2 cursor-pointer"
                   data-application_id="{{ $application->id }}"
                   data-toggle="modal"
                   data-target="#preview_not_allowed_finish_reason">
                <i class="fas fa-times"></i> Завершение недоступно
            </a>
        </div>
    @endif
@endif
