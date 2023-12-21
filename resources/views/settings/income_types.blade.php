@extends('layouts.project')
@section('title', __('interface.income_types'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('interface.income_types') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div id="income_types_settings_div">
                @include('settings.ajax.income_types_settings')
            </div>
        </div>
    </section>
@endsection


