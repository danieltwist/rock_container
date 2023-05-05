@extends('layouts.project')

@section('title', __('invoice.show_invoice'))

@section('content')
    @if(!is_null($invoice))
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0">{{ __('invoice.show_invoice') }} №{{ $invoice->id }}
                            <a href="{{ route('invoice.index') }}" class="btn btn-default">
                                {{ __('invoice.all_invoices') }}
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
                    <div class="card-header {{ is_null($invoice->deleted_at) ?: 'bg-danger' }}">
                        <h3 class="card-title">{{ __('invoice.invoice_info') }}</h3>
                    </div>
                    <div class="card-body">
                        @include('project.layouts.show_invoice_view')
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
