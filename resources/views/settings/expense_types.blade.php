@extends('layouts.project')
@section('title', __('settings.remove_projects_from_stat'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Классификатор расходов</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div id="expense_types_settings_div">
                @include('settings.ajax.expense_types_settings')
            </div>
        </div>
    </section>
@endsection


