@extends('layouts.project')
@section('title', __('work_request.view_work_request'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('work_request.view_work_request') }} №{{ $task->id }}
                        <a href="{{ route('work_request.index') }}" class="btn btn-default">
                            {{ __('work_request.all_work_requests') }}
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $task->text }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped invoices_table">
                        <thead>
                        <tr>
                            <th style="width: 1%">#</th>
                            <th style="width: 17%">{{ __('general.type') }}</th>
                            <th style="width: 15%">{{ __('general.counterparty') }}</th>
                            <th style="width: 17%">{{ __('general.amount') }}</th>
                            <th style="width: 17%">{{ __('general.paid') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th style="width: 30%">{{ __('task.upload_upd') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            @php
                                switch($invoice->status){
                                    case 'Удален': case 'Не оплачен':
                                        $class = 'danger';
                                        break;
                                    case 'Частично оплачен': case 'Оплачен':
                                        $class = 'success';
                                        break;
                                    case 'Ожидается счет от поставщика': case 'Ожидается создание инвойса': case 'Создан черновик инвойса': case 'Ожидается загрузка счета':
                                        $class = 'warning';
                                        break;
                                    case 'Согласована частичная оплата': case 'Счет согласован на оплату':
                                        $class = 'info';
                                        break;
                                    case 'Ожидается оплата':
                                        $class = 'primary';
                                        break;
                                    case 'Счет на согласовании':
                                        $class = 'secondary';
                                        break;
                                    default:
                                        $class = 'secondary';
                                }
                            @endphp
                            @if ($invoice->status == 'Оплачен')
                                <tr class="table-success">
                            @else
                                <tr>
                                    @endif
                                    <td>{{$invoice['id']}}</td>
                                    <td>
                                        @include('invoice.table.info')
                                    </td>
                                    <td>
                                        @include('invoice.table.kontragent')
                                    </td>
                                    <td>
                                        @include('invoice.table.amount')
                                    </td>
                                    <td>
                                        @include('invoice.table.paid')
                                    </td>
                                    <td class="project-state">
                                        @include('invoice.table.'.config('app.prefix_view').'status')
                                    </td>
                                    <td>
                                        <div class="card-body" id="upd_file_{{$invoice->id}}">
                                            @include('invoice.ajax.upd_file')
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
