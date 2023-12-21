@extends('layouts.project')
@section('title', 'Сводка по доходам')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Сводка по доходам</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Скачать сводку по доходам</h3>
                        </div>
                        <form action="{{ route('get_report_incomes_by_types') }}" method="GET">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client">Выберите год для загрузки отчета</label>
                                            <select class="form-control select2" name="report_type" data-placeholder="Выберите год для загрузки отчета" style="width: 100%;" required>
                                                <option></option>
                                                <option value="this_year">Этот год</option>
                                                <option value="last_year">Прошлый год</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success download_file_directly"
                                                    data-action='{"download_file":{"need_download": "true"}}'>
                                                <i class="fas fa-file-excel"></i>
                                                Скачать отчет
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
