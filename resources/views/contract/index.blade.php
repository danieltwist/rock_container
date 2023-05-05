@extends('layouts.project')

@section('title', __('contract.all_contracts'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">
                        {{ $title ?? __('contract.all_contracts') }}
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
                    <h3 class="card-title">{{ __('contract.all_contracts_list') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped datatable_with_paging">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 20%">
                                {{ __('contract.contract_number') }}
                            </th>
                            <th style="width: 30%">
                                {{ __('general.counterparty') }}
                            </th>
                            <th>
                                {{ __('general.files') }}
                            </th>
                            <th style="width: 15%">
                                {{ __('contract.valid_before') }}
                            </th>
                            <th style="width: 15%">
                                {{ __('general.actions') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td>
                                    {{ $contract->id }}
                                </td>
                                <td>
                                    {{ $contract->name }} {{ __('general.from') }} {{ $contract->date_start->format('d.m.Y') }}<br><small>{{ $contract->additional_info }}</small>
                                    <br>
                                    <small>
                                        {{ __('general.added') }} {{ $contract['created_at']->format('d.m.Y') }}
                                        @if($contract['created_at'] != $contract['updated_at'])
                                            / {{ __('general.updated') }} {{ $contract['updated_at']->format('d.m.Y') }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @switch($contract->type)
                                        @case('Поставщик')
                                            {{ __('general.supplier') }}
                                        @break
                                        @case('Клиент')
                                            {{ __('general.client') }}
                                        @break
                                    @endswitch
                                    <br>
                                    @if($contract->type == 'Поставщик')
                                        @if(!is_null(optional($contract->supplier)->id))
                                            <a class="text-dark" href="{{ route('supplier.show', optional($contract->supplier)->id) }}">
                                                {{ optional($contract->supplier)->name }}
                                            </a>
                                        @endif
                                    @elseif ($contract->type == 'Клиент')
                                        @if(!is_null(optional($contract->client)->id))
                                            <a class="text-dark" href="{{ route('client.show', optional($contract->client)->id) }}">
                                                {{ optional($contract->client)->name }}
                                            </a>
                                        @endif
                                    @endif

                                </td>
                                <td>
                                    @if($contract->type == 'Поставщик')
                                        @if (!is_null(optional($contract->supplier)->card))
                                            <a href="{{ Storage::url($contract->supplier->card) }}" download>{{ __('general.counterparty_card') }}</a><br>
                                        @endif
                                        @if($contract->file != '')
                                            <a href="{{ Storage::url($contract->file) }}" download>{{ __('general.download_contract') }}</a>
                                        @endif
                                    @elseif ($contract->type == 'Клиент')
                                        @if (optional($contract->client)->card !='')
                                            <a href="{{ Storage::url($contract->client->card) }}" download>{{ __('general.counterparty_card') }}</a><br>
                                        @endif
                                        @if($contract->file != '')
                                            <a href="{{ Storage::url($contract->file) }}" download>{{ __('general.download_contract') }}</a>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ $contract->date_period->format('d.m.Y') }}
                                    @if ($contract->need_prolong == 1)
                                        <br><b>{{ __('contract.need_prolong') }}</b>
                                    @endif
                                </td>
                                <td class="project-actions">
                                    @can ('edit projects')
                                        <a class="btn btn-app bg-indigo" href="{{ route('contract.edit', $contract->id) }}">
                                            <i class="fas fa-pencil-alt">
                                            </i>
                                            {{ __('general.change') }}
                                        </a>
                                    @endcan
                                    @can ('remove projects')
                                        <form class="button-delete-inline" action="{{ route('contract.destroy', $contract->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-app bg-danger delete-btn">
                                                <i class="fas fa-trash">
                                                </i>
                                                {{ __('general.remove') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
