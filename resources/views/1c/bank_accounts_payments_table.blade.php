@extends('layouts.project')
@section('title', 'История платежей из 1С')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">История платежей из 1С</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Все платежи</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped bank_account_payments" id="bank_account_payments">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 10%">
                                Дата
                            </th>
                            <th style="width: 10%">
                                Тип
                            </th>
                            <th style="width: 15%">
                                Компания
                            </th>
                            <th style="width: 30%">
                                Контрагент
                            </th>
                            <th style="width: 10%">
                                Сумма
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
