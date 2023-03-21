@if ($current_user_notifications->count()==0)
    <div class="dropdown-item text-sm-center">{{ __('notification.dont_have_unread_notifications') }}</div>
@else
    <a class="dropdown-item dropdown-header notification-make-all-read cursor-pointer">
        {{ __('notification.make_all_as_read') }}
    </a>
    @foreach($current_user_notifications as $notification)
        <div class="dropdown-item">
            <div class="media">
                @if ($notification->from == 'Система')
                    <img
                        src="/storage/avatars/system.jpg"
                        alt="User Avatar"
                        class="notifications-avatar mr-3">
                @elseif(is_null($notification->from_user))
                    <img
                        src="/storage/avatars/default.jpg"
                        alt="User Avatar"
                        class="notifications-avatar mr-3">
                @else
                    <img
                        src="{{ Storage::url($notification->from_user->avatar) }}"
                        alt="User Avatar"
                        class="notifications-avatar mr-3">
                @endif

                <div class="media-body">
                    <h3 class="dropdown-item-title">
                        {{$notification->from}}
                    </h3>
                    <p class="text-sm"> {!! $notification->text !!}</p>
                    <p class="text-sm text-muted"><i
                            class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                        <span class="float-right">
                            <a class="notification-make-read cursor-pointer" data-notification_id="{{ $notification->id }}">
                                {{ __('notification.mark_as_read') }}
                            </a>
                        </span>
                    </p>
                    @if(!is_null($notification->link))
                        <div class="mt-3">
                            <a class="btn btn-default btn-sm notification-make-read" data-notification_id="{{ $notification->id }}" href="{{ \Illuminate\Support\Facades\URL::to($notification->link) }}">{{ __('general.go') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="dropdown-divider"></div>
    @endforeach
    <a href="{{ route('notification.index') }}" class="dropdown-item dropdown-footer">
        {{ __('notification.show_all_notifications') }}
    </a>
@endif
