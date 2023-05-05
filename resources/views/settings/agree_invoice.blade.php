@extends('layouts.project')
@section('title', __('settings.agree_invoices'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('settings.agree_invoices') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <form action="{{ route('update_agree_invoices_settings') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('settings.agree_invoices') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('settings.agree_invoice_users_count') }}</label>
                                    <select class="form-control select2" name="agree_invoice_users_count" id="agree_invoice_users_count"
                                            data-placeholder="{{ __('settings.agree_invoice_users_count') }}" style="width: 100%;">
                                        <option></option>
                                        <option value="1" {{ $agree_invoice_users_count != '1' ?: 'selected' }}>1</option>
                                        <option value="2" {{ $agree_invoice_users_count != '2' ?: 'selected' }}>2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>{{ __('settings.choose_users') }}</label>
                                    <select class="form-control select2" name="agree_invoice_users[]" id="agree_invoice_users"
                                            data-placeholder="{{ __('settings.choose_users') }}" style="width: 100%;" multiple>
                                        <option></option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ !in_array($user->id, $agree_invoice_users) ?: 'selected' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
