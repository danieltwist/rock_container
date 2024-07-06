@extends('layouts.project')
@section('title', 'Шаблоны инвойсов')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Шаблоны инвойсов</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Текущие шаблоны</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Название</th>
                            <th>Информация</th>
                            <th>Дата добавления</th>
                            <th>Файл</th>
                            <th style="width: 10%">{{ __('general.removing') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoice_templates as $template)
                            <tr>
                                <td>{{ $template->name }}</td>
                                <td>{{ $template->info }}</td>
                                <td>{{ $template->created_at }}</td>
                                <td>
                                    <a class="btn-primary btn btn-sm" href="{{ Storage::url($template->file) }}" download>
                                       Скачать
                                    </a>
                                </td>
                                <td>
                                    <form class="inline-block" action="{{ route('invoice_templates.destroy', $template->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                        @if(count($invoice_templates) == 1)
                                            disabled
                                        @endif>
                                            {{ __('general.remove') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <form action="{{ route('invoice_templates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Добавить новый шаблон</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="from">Название</label>
                            <input type="text" class="form-control to_uppercase" name="name"
                                   placeholder="Название" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('general.additional_info') }}</label>
                            <textarea class="form-control to_uppercase" rows="3" name="additional_info"
                                      placeholder="{{ __('general.additional_info') }}" required></textarea>
                        </div>
                        <div class="form-group mt-2">
                            <label for="file">{{ __('general.file') }}</label>
                            <input type="file" class="form-control-file" name="invoice_template" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
