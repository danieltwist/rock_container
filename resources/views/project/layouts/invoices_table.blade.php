<table class="table table-striped" id="invoices_table_project_static">
    <thead>
    <tr>
        <th style="width: 1%">#</th>
        <th style="width: 35%">{{ __('general.info') }}</th>
        <th style="width: 17%">{{ __('general.amount') }}</th>
        <th style="width: 17%">{{ __('general.paid') }}</th>
        <th>{{ __('general.status') }}</th>
        <th style="width: 30%">{{ __('general.actions') }}</th>
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
                @include('project.invoices_table.info')
            </td>
            <td>
                @include('project.invoices_table.amount')
            </td>
            <td>
                @include('project.invoices_table.paid')
            </td>
            <td class="project-state">
                @include('project.invoices_table.'.config('app.prefix_view').'status')
            </td>
            <td>
                @include('project.invoices_table.actions')
            </td>
        </tr>
    @endforeach


    </tbody>
</table>
