@extends('layouts.project')
@section('title', 'Баланс счетов из 1С')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Баланс счетов из 1С</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Все записи</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped bank_account_balances" id="bank_account_balances">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 10%">
                                Дата
                            </th>
                            <th style="width: 90%">
                                Информация
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
