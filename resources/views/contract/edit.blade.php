@extends('layouts.project')
@section('title', __('contract.edit_contract'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('contract.edit_contract') }} {{ $contract->name }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ __('general.contract') }} №{{ $contract->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ Storage::url($contract->file) }}" download class="btn btn-block btn-primary btn-xs">
                            {{ __('general.download_contract') }}
                        </a>
                    </div>
                </div>
                <form class="button-delete-inline" action="{{ route('contract.update', $contract->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @if ($contract->type == 'Клиент')
                        <input type="hidden" name="action" value="client">
                    @elseif ($contract->type == 'Поставщик')
                        <input type="hidden" name="action" value="supplier">
                    @endif
                    <div class="card-body">
                        <strong>{{ __('general.counterparty') }}
                            @if ($contract->type == 'Клиент')
                                {{ optional($contract->client)->name }}<br><br>
                            @elseif ($contract->type == 'Поставщик')
                                {{ optional($contract->supplier)->name }}<br><br>
                            @endif
                        </strong>
                        <div class="form-group">
                            <label>{{ __('contract.contract_number') }}</label>
                            <input type="text" class="form-control" name="name"
                                   placeholder="{{ __('contract.contract_number') }}" value="{{ $contract->name }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('contract.date_of_sign') }}</label>
                            <input type="text" class="form-control date_input" name="date_start"
                                   placeholder="{{ __('contract.date_of_sign') }}" value="{{ $contract->date_start->format('d.m.Y') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('contract.valid_before') }}</label>
                            <input type="text" class="form-control date_input" name="date_period"
                                   placeholder="{{ __('contract.valid_before') }}" value="{{ $contract->date_period->format('d.m.Y') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('contract.contract_type') }}</label>
                            <input type="text" class="form-control" name="additional_info"
                                   placeholder="{{ __('contract.contract_type') }}"
                                   value="{{ $contract->additional_info }}">
                        </div>
                        <div class="form-group">
                            <label for="contract">{{ __('contract.upload_new_file') }}</label>
                            <input type="file" class="form-control-file" name="file">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right">{{ __('general.update') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection
