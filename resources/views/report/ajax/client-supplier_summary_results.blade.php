<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('report.results') }}</h3>
        <div class="card-tools">
            <form action="{{ route('export_report_client_supplier_balance') }}" method="POST">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client_id }}">
                <input type="hidden" name="supplier_id" value="{{ $supplier_id }}">
                <input type="hidden" name="datarange" value="{{ $datarange }}">
                <button type="submit" class="btn btn-block btn-success btn-xs"
                        data-action='{"download_file":{"need_download": "true"}}'>
                    <i class="fas fa-file-excel"></i>
                    {{ __('general.export_to_excel') }}
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('report.whole_results_rubles') }}</h3>
                    </div>
                    <div class="card-body">
                        {{ __('report.all_income_invoices_amount') }}: {{ number_format($results['total_income_rub'], 0, '.', ' ') }}р.<br>
                        {{ __('general.paid') }}: {{ number_format($results['total_income_rub_paid'], 0, '.', ' ') }}руб.<br><br>
                        {{ __('report.all_outcome_invoices_amount') }}: {{ number_format($results['total_outcome_rub'], 0, '.', ' ') }}р.<br>
                        {{ __('general.paid') }}: {{ number_format($results['total_outcome_rub_paid'], 0, '.', ' ') }}руб.<br><br>
                        <strong>{{ __('report.balance') }}: {{ number_format($results['total_difference_rub'], 0, '.', ' ') }}р.
                            ({{ number_format($results['total_income_rub_paid']-$results['total_outcome_rub_paid'], 0, '.', ' ')  }}
                            р.)</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('report.whole_results_usd') }}</h3>
                    </div>
                    <div class="card-body">
                        {{ __('report.all_income_invoices_amount') }}: {{ number_format($results['total_income_usd'], 0, '.', ' ') }}USD<br>
                        {{ __('general.paid') }}: {{ number_format($results['total_income_usd_paid'], 0, '.', ' ') }}USD<br><br>
                        {{ __('report.all_outcome_invoices_amount') }}: {{ number_format($results['total_outcome_usd'], 0, '.', ' ') }}USD<br>
                        {{ __('general.paid') }}: {{ number_format($results['total_outcome_usd_paid'], 0, '.', ' ') }}USD<br><br>
                        <strong>{{ __('report.balance') }}: {{ number_format($results['total_difference_usd'], 0, '.', ' ') }}USD
                            ({{ number_format($results['total_income_usd_paid']-$results['total_outcome_usd_paid'], 0, '.', ' ')  }}USD)</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('report.whole_results_cny') }}</h3>
                    </div>
                    <div class="card-body">
                        {{ __('report.all_income_invoices_amount') }}: {{ number_format($results['total_income_cny'], 0, '.', ' ') }}CNY<br>
                        {{ __('general.paid') }}: {{ number_format($results['total_income_cny_paid'], 0, '.', ' ') }}CNY<br><br>
                        {{ __('report.all_outcome_invoices_amount') }}: {{ number_format($results['total_outcome_cny'], 0, '.', ' ') }}CNY<br>
                        {{ __('general.paid') }}: {{ number_format($results['total_outcome_cny_paid'], 0, '.', ' ') }}CNY<br><br>
                        <strong>{{ __('report.balance') }}: {{ number_format($results['total_difference_cny'], 0, '.', ' ') }}CNY
                            ({{ number_format($results['total_income_cny_paid']-$results['total_outcome_cny_paid'], 0, '.', ' ')  }}CNY)</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            @foreach($projects as $project)
                <strong>{{ $project->name }}</strong>
                @php
                    $invoices = $project->invoices;
                @endphp
                <div class="card card-outline card-warning collapsed-card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('report.invoices_list') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="display: none;">
                        <div id="project_invoices_table">
                            @include('project.layouts.invoices_table')
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('report.rubles') }}</h3>
                            </div>
                            <div class="card-body">
                                {{ __('report.all_income_invoices_amount') }}: {{ number_format($project->income_rub, 0, '.', ' ') }}р.<br>
                                {{ __('general.paid') }}: {{ number_format($project->income_rub_paid, 0, '.', ' ') }}р.<br><br>
                                {{ __('report.all_outcome_invoices_amount') }}: {{ number_format($project->outcome_rub, 0, '.', ' ') }}р.<br>
                                {{ __('general.paid') }}: {{ number_format($project->outcome_rub_paid, 0, '.', ' ') }}руб.<br><br>
                                <strong>{{ __('report.balance') }}: {{ number_format($project->income_rub-$project->outcome_rub, 0, '.', ' ') }}р.
                                    ({{ number_format($project->income_rub_paid-$project->outcome_rub_paid, 0, '.', ' ') }}р.)</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">USD</h3>
                            </div>
                            <div class="card-body">
                                {{ __('report.all_income_invoices_amount') }}: {{ number_format($project->income_usd, 0, '.', ' ') }}USD<br>
                                {{ __('general.paid') }}: {{ number_format($project->income_usd_paid, 0, '.', ' ') }}USD<br><br>
                                {{ __('report.all_outcome_invoices_amount') }}: {{ number_format($project->outcome_usd, 0, '.', ' ') }}USD<br>
                                {{ __('general.paid') }}: {{ number_format($project->outcome_usd_paid, 0, '.', ' ') }}USD<br><br>
                                <strong>{{ __('report.balance') }}: {{ number_format($project->income_usd-$project->outcome_usd, 0, '.', ' ') }}USD
                                    ({{ number_format($project->income_usd_paid-$project->outcome_usd_paid, 0, '.', ' ') }}USD)</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">CNY</h3>
                            </div>
                            <div class="card-body">
                                {{ __('report.all_income_invoices_amount') }}: {{ number_format($project->income_cny, 0, '.', ' ') }}CNY<br>
                                {{ __('general.paid') }}: {{ number_format($project->income_cny_paid, 0, '.', ' ') }}CNY<br><br>
                                {{ __('report.all_outcome_invoices_amount') }}: {{ number_format($project->outcome_cny, 0, '.', ' ') }}CNY<br>
                                {{ __('general.paid') }}: {{ number_format($project->outcome_cny_paid, 0, '.', ' ') }}CNY<br><br>
                                <strong>{{ __('report.balance') }}: {{ number_format($project->income_cny-$project->outcome_cny, 0, '.', ' ') }}CNY
                                    ({{ number_format($project->income_cny_paid-$project->outcome_cny_paid, 0, '.', ' ') }}CNY)</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

