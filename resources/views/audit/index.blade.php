@extends('layouts.project')
@section('title', 'История действий пользователей')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">История действий пользователей</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Все действия</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped audits_table" id="audits_table">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 10%">
                                Дата
                            </th>
                            <th style="width: 10%">
                                Пользователь
                            </th>
                            <th style="width: 15%">
                                Элемент
                            </th>
                            <th style="width: 10%">
                                Действие
                            </th>
                            <th style="width: 30%">
                                Версия до
                            </th>
                            <th style="width: 30%">
                                Версия после
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
