@extends('layouts.project')
@section('title', __('container.work_with_containers'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('container.work_with_containers') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div id="success_ajax"></div>
            @if(!isset($list))
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('containers_preview_actions') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('container.upload_list') }}</h3>
                                    <div class="card-tools">
                                        <a href="{{ Storage::url($file_path) }}" download
                                           class="btn btn-block btn-success btn-xs">{{ __('container.download_last_upload_list') }}</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="file">{{ __('general.choose_file') }}</label>
                                        <input type="file" class="form-control-file" name="containers_list">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary float-right">{{ __('general.upload') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('general.info_about_using') }}</h3>
                                <div class="card-tools">
                                    <a href="/storage/templates/containers_update_template.xlsx" download
                                       class="btn btn-block btn-success btn-xs">{{ __('general.download_template') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>{{ __('container.info_about_using_1') }}</li>
                                    <li>{{ __('container.info_about_using_2') }}</li>
                                    <li>{{ __('container.info_about_using_3') }}</li>
                                    <li>{{ __('container.info_about_using_4') }} <a href="{{ route('supplier.index')}}"
                                                                                    target="_blank">{{ __('general.all_suppliers') }}</a>, {{ __('container.info_about_using_5') }}
                                    </li>
                                    <li>{{ __('container.info_about_using_6') }}</li>
                                    <li>{{ __('container.info_about_using_7') }}</li>
                                    <li>{{ __('container.info_about_using_8') }}</li>
                                    <li>{{ __('container.info_about_using_9') }}</li>
                                    <li>{{ __('container.info_about_using_10') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($list))
                @if($need_update)
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Default box -->
                            <form action="{{ route('containers_save_actions') }}" method="POST">
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('container.preview_actions') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="list" value="{{ serialize($list) }}">
                                        <table class="table table-striped invoices_table">
                                            <thead>
                                            <tr>
                                                <th>{{ __('container.number') }}</th>
                                                <th>{{ __('container.grace_period') }} / {{ __('container.days') }}</th>
                                                <th>{{ __('container.snp') }} / {{ __('container.day') }}</th>
                                                <th>{{ __('container.svv') }}</th>
                                                <th>{{ __('container.using') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($containers as $container)
                                                <tr
                                                    @if($container->add_new)
                                                    class="table-success"
                                                    @endif
                                                    @if($container->make_return && $container->return_date != '')
                                                    class="table-info"
                                                    @endif
                                                >
                                                    <td>
                                                        @if($container->add_new)
                                                            <strong>{{ __('container.add_new') }}</strong><br>
                                                        @endif
                                                        @if($container->make_return && $container->return_date != '')
                                                            <strong>{{ __('container.finish_using') }}</strong><br>
                                                        @endif
                                                        {{ $container->name }} <br>
                                                        <small>
                                                            @if ($container->update_type)
                                                                <p class="bg-warning font-weight-bold">
                                                                    {{ $container->type }}
                                                                </p>
                                                            @else
                                                                {{ $container->type }}
                                                            @endif
                                                            @if ($container->update_supplier_id)
                                                                <p class="bg-warning font-weight-bold">
                                                                    {{ $container->supplier }}
                                                                </p>
                                                            @else
                                                                {{ $container->supplier }}
                                                            @endif
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if ($container->update_grace_period_for_client)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.for_client') }}
                                                                - {{ $container->grace_period_for_client == '' ? 'Удалить' : $container->grace_period_for_client }}
                                                            </p>
                                                        @else
                                                            {{ __('container.for_client') }}
                                                            - {{ $container->grace_period_for_client }}<br>
                                                        @endif
                                                        @if ($container->update_grace_period_for_us)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.for_us') }}
                                                                - {{ $container->grace_period_for_us == '' ? 'Удалить' : $container->grace_period_for_us }}
                                                            </p>
                                                        @else
                                                            {{ __('container.for_us') }}
                                                            -  {{ $container->grace_period_for_us }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($container->update_snp_amount_for_client || $container->update_snp_currency || $container->update_snp_range_for_client)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.for_client') }}
                                                                : {{ $container->range_client_string == '' ? 'Удалить' : $container->range_client_string }}
                                                                ,
                                                                {{ __('container.later') }} {{ $container->snp_amount_for_client == '' ? 'Удалить' : $container->snp_amount_for_client }}
                                                                {{ $container->snp_currency }}
                                                            </p>
                                                        @else
                                                            {{ __('container.for_client') }}
                                                            : {{ $container->range_client_string }},
                                                            {{ __('container.later') }} {{ $container->snp_amount_for_client }}{{ $container->snp_currency }}
                                                            <br>
                                                        @endif
                                                        @if ($container->update_snp_amount_for_us || $container->update_snp_currency || $container->update_snp_range_for_us)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.for_us') }}
                                                                : {{ $container->range_us_string == '' ? 'Удалить' : $container->range_us_string }}
                                                                ,
                                                                {{ __('container.later') }} {{ $container->snp_amount_for_us == '' ? 'Удалить' : $container->snp_amount_for_us }}
                                                                {{ $container->snp_currency }}
                                                            </p>
                                                        @else
                                                            {{ __('container.for_us') }}
                                                            : {{ $container->range_us_string }},
                                                            {{ __('container.later') }} {{ $container->snp_amount_for_us }}{{ $container->snp_currency }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($container->update_svv)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ $container->svv == '' ? 'Удалить' : $container->svv}}
                                                            </p>
                                                        @else
                                                            {{ $container->svv }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($container->update_start_date_for_us)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.start_for_us') }}
                                                                - {{ $container->start_date_for_us == '' ? 'Удалить' : $container->start_date_for_us }}
                                                            </p>
                                                        @else
                                                            {{ __('container.start_for_us') }}
                                                            - {{ $container->start_date_for_us }}
                                                        @endif
                                                        <br>
                                                        @if ($container->update_start_date_for_client)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.start_for_client') }}
                                                                - {{ $container->start_date_for_client == '' ? 'Удалить' : $container->start_date_for_client }}
                                                            </p>
                                                        @else
                                                            {{ __('container.start_for_client') }}
                                                            - {{ $container->start_date_for_client }}
                                                        @endif
                                                        <br>
                                                        @if ($container->make_return)
                                                            <p class="bg-warning font-weight-bold">
                                                                {{ __('container.return') }}
                                                                - {{ $container->return_date }}
                                                            </p>
                                                        @else
                                                            {{ __('container.return') }} - {{ $container->return_date }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit"
                                                class="btn btn-primary float-right">{{ __('general.save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('container.preview_actions') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        {{ __('container.changes_not_found') }}
                                    </div>
                                    <div class="card-footer">
                                        <a class="btn btn-primary float-right"
                                           href="{{ route('containers_processing') }}">
                                            {{ __('container.back_to_upload_file') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
    </section>
@endsection


