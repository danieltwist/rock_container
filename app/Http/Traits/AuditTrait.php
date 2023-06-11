<?php

namespace App\Http\Traits;

use App\Models\Application;
use App\Models\Client;
use App\Models\CurrencyRate;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\User;
use OwenIt\Auditing\Models\Audit;

trait AuditTrait {

    private array $columns = [
        'App\Models\Application' => [
            'type' => [
                'name' => 'Тип заявки',
                'type' => 'string'
            ],
            'counterparty_type' => [
                'name' => 'Тип контрагента',
                'type' => 'string'
            ],
            'name' => [
                'name' => 'Название',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус',
                'type' => 'string'
            ],
            'client_name' => [
                'name' => 'Компания клиента',
                'type' => 'string'
            ],
            'supplier_name' => [
                'name' => 'Компания поставщика',
                'type' => 'string'
            ],
            'price_currency' => [
                'name' => 'Валюта',
                'type' => 'string'
            ],
            'price_amount' => [
                'name' => 'Сумма',
                'type' => 'string'
            ],
            'grace_period' => [
                'name' => 'Кол-во суток льготного пользования',
                'type' => 'string'
            ],
            'snp_currency' => [
                'name' => 'Валюта СНП',
                'type' => 'string'
            ],
            'snp_range' => [
                'name' => 'Прогрессивный СНП',
                'type' => 'array',
                'keys' => null,
                'multidimensional' => true,
                'delimiter' => ': '
            ],
            'snp_after_range' => [
                'name' => 'Ставка СНП после диапазонов',
                'type' => 'string'
            ],
            'additional_info' => [
                'name' => 'Дополнительная информация',
                'type' => 'string'
            ],
        ],
        'App\Models\Task' => [
            'name' => [
                'name' => 'Название',
                'type' => 'string'
            ],
            'text' => [
                'name' => 'Текст',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус',
                'type' => 'string'
            ],
            'deadline' => [
                'name' => 'Дедлайн',
                'type' => 'string'
            ],
        ],
        'App\Models\WorkRequest' => [
            'name' => [
                'name' => 'Название',
                'type' => 'string'
            ],
            'text' => [
                'name' => 'Текст',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус',
                'type' => 'string'
            ],
            'deadline' => [
                'name' => 'Дедлайн',
                'type' => 'string'
            ],
        ],
        'App\Models\Invoice' => [
            'amount' => [
                'name' => 'Сумма',
                'type' => 'string'
            ],
            'currency' => [
                'name' => 'Валюта',
                'type' => 'string'
            ],
            'amount_actual' => [
                'name' => 'Фактическая сумма',
                'type' => 'string'
            ],
            'amount_paid' => [
                'name' => 'Оплачено в рублях',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус',
                'type' => 'string'
            ],
            'director_comment' => [
                'name' => 'Примечание руководителя',
                'type' => 'string'
            ],
            'manager_comment' => [
                'name' => 'Примечание менеджера',
                'type' => 'string'
            ],
            'accountant_comment' => [
                'name' => 'Примечание бухгалтера',
                'type' => 'string'
            ],
            'additional_info' => [
                'name' => 'Информация',
                'type' => 'string'
            ],
            'deadline' => [
                'name' => 'Дедлайн',
                'type' => 'string'
            ],
            'amount_in_currency_actual' => [
                'name' => 'Фактическая сумма в валюте',
                'type' => 'string'
            ],
            'amount_in_currency' => [
                'name' => 'Сумма в валюте',
                'type' => 'string'
            ],
            'amount_income_date' => [
                'name' => 'Сумма в рублях на дату оплаты',
                'type' => 'string'
            ],
            'amount_in_currency_income_date' => [
                'name' => 'Сумма в валюте на дату оплаты в рублях',
                'type' => 'string'
            ],
            'rate_out_date' => [
                'name' => 'Курс на день выставления',
                'type' => 'string'
            ],
            'rate_income_date' => [
                'name' => 'Курс на день оплаты',
                'type' => 'string'
            ],
            'rate_sale_date' => [
                'name' => 'Курс на день продажи валюты',
                'type' => 'string'
            ],
            'amount_sale_date' => [
                'name' => 'Сумма продажи валюты в рублях',
                'type' => 'string'
            ],
            'expense_category' => [
                'name' => 'Вид расходов',
                'type' => 'string'
            ],
            'expense_type' => [
                'name' => 'Тип расходов',
                'type' => 'string'
            ],
            'agree_1' => [
                'name' => 'Согласование',
                'type' => 'special'
            ],
            'agree_2' => [
                'name' => 'Согласование',
                'type' => 'special'
            ],
        ],
        'App\Models\Project' => [
            'name' => [
                'name' => 'Название',
                'type' => 'string'
            ],
            'status' => [
                'name' => 'Статус',
                'type' => 'string'
            ],
            'paid' => [
                'name' => 'Статус оплаты',
                'type' => 'string'
            ],
            'planned_payment_date' => [
                'name' => 'Планируемая дата оплаты',
                'type' => 'string',
                'date' => true
            ],
            'additional_info' => [
                'name' => 'Дополнительная информация',
                'type' => 'string'
            ],
        ],
        'App\Models\User' => [
            'name' => [
                'name' => 'Имя',
                'type' => 'string'
            ],
        ],
    ];

    public function prepareForTable(Audit $audit){
        $user = User::where('id', $audit->user_id)->first();
        if(!is_null($user)){
            $user_name = $user->name;
        }
        else $user_name = 'Удален, ID '.$audit->user_id;
        $audit->user_name = $user_name;

        switch ($audit->auditable_type){
            case 'App\Models\Application':
                $component_name = 'Заявка';
                $component_route = 'application.show';
                try{
                    $name = Application::withTrashed()->find($audit->auditable_id)->name;
                }
                catch (\Exception $e){
                    $name = '№'.$audit->auditable_id;
                }
                break;
            case 'App\Models\Invoice':
                $component_name = 'Счет';
                $component_route = 'invoice.show';
                $name = '№'.$audit->auditable_id;
                break;
            case 'App\Models\Project':
                $component_name = 'Проект';
                $component_route = 'project.show';
                try{
                    $name = Project::withTrashed()->find($audit->auditable_id)->name;
                }
                catch (\Exception $e){
                    $name = '№'.$audit->auditable_id;
                }
                break;
            case 'App\Models\Task':
                $component_name = 'Задача';
                $component_route = 'task.show';
                $name = '№'.$audit->auditable_id;
                break;
            case 'App\Models\WorkRequest':
                $component_name = 'Запрос';
                $component_route = 'work_request.show';
                $name = '№'.$audit->auditable_id;
                break;
            case 'App\Models\Client':
                $component_name = 'Клиент';
                $component_route = 'client.show';
                try{
                    $name = Client::withTrashed()->find($audit->auditable_id)->name;
                }
                catch (\Exception $e){
                    $name = '№'.$audit->auditable_id;
                }
                break;
            case 'App\Models\Supplier':
                $component_name = 'Поставщик';
                $component_route = 'supplier.show';
                try{
                    $name = Supplier::withTrashed()->find($audit->auditable_id)->name;
                }
                catch (\Exception $e){
                    $name = '№'.$audit->auditable_id;
                }
                break;
            case 'App\Models\User':
                $component_name = 'Пользователь';
                $component_route = 'user.show';
                try{
                    $name = User::withTrashed()->find($audit->auditable_id)->name;
                }
                catch (\Exception $e){
                    $name = '№'.$audit->auditable_id;
                }
                break;
        }

        $component_name .= ' '.$name;

        $audit->component_name = $component_name;
        $audit->component_route = $component_route;

        switch ($audit->event){
            case 'updated':
                $audit->event = 'Обновление';
                break;
            case 'created':
                $audit->event = 'Создание';
                break;
            case 'deleted':
                $audit->event = 'Удаление';
                break;
            case 'restored':
                $audit->event = 'Восстановление';
                break;
        }

        $columns = $this->columns;

        if($audit->event == 'Обновление'){
            if($this->checkAuditForColumn($audit)){
                $before_edit = [];
                $after_edit = [];

                $audit->before_edit = null;
                $audit->after_edit = null;
                $audit->skip = false;
                foreach ($audit->getModified() as $col_name => $modified) {
                    if (isset($columns[$audit->auditable_type][$col_name])) {
                        if ($columns[$audit->auditable_type][$col_name]['type'] == 'string') {
                            if(isset($columns[$audit->auditable_type][$col_name]['datetime'])){
                                !is_null($modified['old']) ? $old_text = \Carbon\Carbon::parse($modified['old'])->format('d.m.Y H:i:s') : $old_text = '';
                                !is_null($modified['new']) ? $new_text = \Carbon\Carbon::parse($modified['new'])->format('d.m.Y H:i:s') : $new_text = '';
                            }
                            elseif(isset($columns[$audit->auditable_type][$col_name]['date'])){
                                !is_null($modified['old']) ? $old_text = \Carbon\Carbon::parse($modified['old'])->format('d.m.Y') : $old_text = '';
                                !is_null($modified['new']) ? $new_text = \Carbon\Carbon::parse($modified['new'])->format('d.m.Y') : $new_text = '';
                            }
                            else {
                                $old_text = $modified['old'];
                                $new_text = $modified['new'];
                            }
                            $before_edit[] = [
                                'column' => $columns[$audit->auditable_type][$col_name]['name'],
                                'text' => $old_text
                            ];
                            $after_edit[] = [
                                'column' => $columns[$audit->auditable_type][$col_name]['name'],
                                'text' => $new_text
                            ];
                        }
                        if ($columns[$audit->auditable_type][$col_name]['type'] == 'array') {
                            $old_text = [];
                            if (!is_null($modified['old'])) {
                                if(!is_null($audit->auditable_type::withTrashed()->find($audit->auditable_id)->deleted_at))
                                    $modified['old'] = json_decode($modified['old'], true);
                                if (isset($columns[$audit->auditable_type][$col_name]['multidimensional'])) {
                                    foreach ($modified['old'] as $value) {
                                        if (!is_null($columns[$audit->auditable_type][$col_name]['keys'])) {
                                            foreach ($value as $m_key => $m_val) {
                                                $old_text[] = $columns[$audit->auditable_type][$col_name]['keys'][$m_key] . ': ' . $m_val;
                                            }
                                        } else {
                                            isset($columns[$audit->auditable_type][$col_name]['delimiter']) ? $delimiter = $columns[$audit->auditable_type][$col_name]['delimiter'] : $delimiter = ', ';
                                            $old_text[] = implode($delimiter, $value);
                                        }
                                    }
                                } else {
                                    if (!is_null($columns[$audit->auditable_type][$col_name]['keys'])) {
                                        foreach ($modified['old'] as $key => $value) {
                                            $old_text[] = $columns[$audit->auditable_type][$col_name]['keys'][$key] . ': ' . $value;
                                        }
                                    } else {
                                        isset($columns[$audit->auditable_type][$col_name]['delimiter']) ? $delimiter = $columns[$audit->auditable_type][$col_name]['delimiter'] : $delimiter = ', ';
                                        $old_text[] = implode($delimiter, $modified['old']);
                                    }
                                }
                            }
                            isset($columns[$audit->auditable_type][$col_name]['delimiter']) ? $delimiter = $columns[$audit->auditable_type][$col_name]['delimiter'] : $delimiter = ' / ';
                            $before_edit[] = [
                                'column' => $columns[$audit->auditable_type][$col_name]['name'],
                                'text' => implode($delimiter, $old_text)
                            ];

                            $new_text = [];
                            if (!is_null($modified['new'])) {
                                if(!is_null($audit->auditable_type::withTrashed()->find($audit->auditable_id)->deleted_at))
                                    $modified['new'] = json_decode($modified['new'], true);
                                if (isset($columns[$audit->auditable_type][$col_name]['multidimensional'])) {
                                    foreach ($modified['new'] as $value) {
                                        if (!is_null($columns[$audit->auditable_type][$col_name]['keys'])) {
                                            foreach ($value as $m_key => $m_val) {
                                                $new_text[] = $columns[$audit->auditable_type][$col_name]['keys'][$m_key] . ': ' . $m_val;
                                            }
                                        } else {
                                            isset($columns[$audit->auditable_type][$col_name]['delimiter']) ? $delimiter = $columns[$audit->auditable_type][$col_name]['delimiter'] : $delimiter = ', ';
                                            $new_text[] = implode($delimiter, $value);
                                        }
                                    }
                                } else {
                                    if (!is_null($columns[$audit->auditable_type][$col_name]['keys'])) {
                                        foreach ($modified['new'] as $key => $value) {
                                            $new_text[] = $columns[$audit->auditable_type][$col_name]['keys'][$key] . ': ' . $value;
                                        }
                                    } else {
                                        isset($columns[$audit->auditable_type][$col_name]['delimiter']) ? $delimiter = $columns[$audit->auditable_type][$col_name]['delimiter'] : $delimiter = ', ';
                                        $new_text[] = implode($delimiter, $modified['new']);
                                    }
                                }
                            }
                            isset($columns[$audit->auditable_type][$col_name]['delimiter']) ? $delimiter = $columns[$audit->auditable_type][$col_name]['delimiter'] : $delimiter = ' / ';
                            $after_edit[] = [
                                'column' => $columns[$audit->auditable_type][$col_name]['name'],
                                'text' => implode($delimiter, $new_text)
                            ];
                        }
                        if ($columns[$audit->auditable_type][$col_name]['type'] == 'special') {
                            if(in_array($col_name, ['agree_1', 'agree_2'])){
                                $old_text = [];
                                if(!is_null($modified['old'])){
                                    $old = unserialize($modified['old']);
                                    $old_text = [
                                        'Дата' => $old['date']->format('d.m.Y H:i:s'),
                                        'Статус' => $old['status']
                                    ];
                                }

                                $new_text = [];
                                if(!is_null($modified['new'])){
                                    $new = unserialize($modified['new']);
                                    $new_text = [
                                        'Дата' => $new['date']->format('d.m.Y H:i:s'),
                                        'Статус' => $new['status']
                                    ];
                                }

                                $before_edit[] = [
                                    'column' => $columns[$audit->auditable_type][$col_name]['name'],
                                    'text' => implode(' ', $old_text)
                                ];

                                $after_edit[] = [
                                    'column' => $columns[$audit->auditable_type][$col_name]['name'],
                                    'text' => implode(' ', $new_text)
                                ];
                            }
                        }
                    }
                }

                $audit->before_edit = $before_edit;
                $audit->after_edit = $after_edit;
            }
        }

        return $audit;
    }

    public function checkAuditForColumn(Audit $audit){
        $skip = true;
        if($audit->event == 'updated'){
            foreach ($audit->getModified() as $key => $updated){
               if (isset($this->columns[$audit->auditable_type][$key])){
                   $skip = false;
                   break;
               }
            }
        }
        return $skip;
    }
}
