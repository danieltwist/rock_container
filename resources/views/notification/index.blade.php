@extends('layouts.project')

@section('title', __('notification.notifications'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('notification.notifications') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('notification.latest_notifications') }}</h3>
                            @if($to_notifications->count() != 0)
                                <div class="card-tools">
                                    <form action="{{ route('add_all_read_to_archive_notifications') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="in">
                                        <button type="submit" class="btn btn-block btn-default btn-xs">
                                            <i class="fas fa-archive"></i>
                                            {{ __('notification.move_read_in_archive') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="card-body card-comments">
                            @if($to_notifications->count() != 0)
                                @foreach($to_notifications as $notification)
                                <div class="card-comment">
                                    @if ($notification->from == 'Система')
                                        <img class="img-circle img-sm" src="/storage/avatars/system.jpg">
                                    @elseif(is_null($notification->from_user))
                                        <img class="img-circle img-sm" src="/storage/avatars/default.jpg">
                                    @else
                                        <img class="img-circle img-sm" src="{{ Storage::url(optional($notification->from_user)->avatar) }}">
                                    @endif

                                    <div class="comment-text">
                                    <span class="username">
                                      {{$notification->from}}
                                      <span class="text-muted float-right">{{ $notification->created_at->diffForHumans() }}</span>
                                    </span>
                                        {!! $notification->text !!}
                                        @if(is_null($notification->received))
                                            <small>
                                                <span class="float-right">
                                                    <a class="notification-make-read cursor-pointer" data-notification_id="{{ $notification->id }}">
                                                        {{ __('notification.mark_as_read') }}
                                                    </a>
                                                </span>
                                            </small>
                                        @endif
                                        @if(!is_null($notification->link))
                                            <div class="mt-3">
                                                <a class="btn btn-default btn-sm" href="{{ \Illuminate\Support\Facades\URL::to($notification->link) }}">
                                                    {{ __('general.go') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @else
                                {{ __('notification.no_income_notifications') }}
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $to_notifications->links() }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('notification.outcome_notifications') }}</h3>
                            @if($from_notifications->count() != 0)
                                <div class="card-tools">
                                    <form action="{{ route('add_all_read_to_archive_notifications') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="out">
                                        <button type="submit" class="btn btn-block btn-default btn-xs">
                                            <i class="fas fa-archive"></i>
                                            {{ __('notification.move_read_in_archive') }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="card-body card-comments">
                            @if($from_notifications->count() != 0)
                                @foreach($from_notifications as $notification)
                            <div class="card-comment">
                                <img class="img-circle img-sm" src="{{ \Illuminate\Support\Facades\Storage::url(optional($notification->to)->avatar) }}">
                                <div class="comment-text">
                                    <span class="username">
                                      {{optional($notification->to)->name}}
                                      <span class="text-muted float-right">{{ $notification->created_at->diffForHumans() }}</span>
                                    </span>
                                    {!! $notification->text !!}
                                    @if(is_null($notification->received))
                                        <small>
                                                <span class="float-right">{{ __('notification.not_read_yet') }}</span>
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                            @else
                                {{ __('notification.no_outcome_notifications') }}
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $from_notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
