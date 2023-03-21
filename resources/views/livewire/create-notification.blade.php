<div>
    <div class="form-group">
        <label for="to_id">{{ __('notification.to_whom_send') }}</label>
        <select class="form-control" name="to_id">
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>{{ __('general.text') }}</label>
        <textarea class="form-control" rows="3" name="text" placeholder="{{ __('general.text') }}"></textarea>
    </div>
</div>
