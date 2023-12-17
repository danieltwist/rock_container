<?php

namespace App\Http\Traits;
use App\Models\Application;
use App\Models\Container;
use App\Models\ContainerHistory;
use App\Models\ContainerProject;
use App\Models\ContainerUsageStatistic;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait ContainerTrait {

    private array $columns = [
        '0' => [
            'id' => 'id',
            'name' => 'ID',
            'search_type' => null,
            'width' => [
                'all' => '5px',
                'free' => '5px',
                'main_info' => '5%',
                'manual' => '1%',
                'repair' => '5px',
                'smgs' => '5px',
                'svv' => '1%',
                'kp' => '1%',
            ],
        ],
        '1' => [
            'id' => 'name',
            'name' => 'Префикс',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => '10%',
                'manual' => '5%',
                'repair' => '150px',
                'smgs' => '150px',
                'svv' => '10%',
                'kp' => '10%',
            ],
        ],
        '2' => [
            'id' => 'status',
            'name' => 'Статус',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => '5%',
            ],
        ],
        '3' => [
            'id' => 'type',
            'name' => 'Тип',
            'search_type' => 'select',
            'width' => [
                'all' => '100px',
                'free' => '150px',
                'main_info' => null,
                'manual' => '5%',
                'repair' => '150px',
                'smgs' => '150px',
                'svv' => '10%',
                'kp' => '5%',
            ],
        ],
        '4' => [
            'id' => 'owner_name',
            'name' => 'Собственник',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => '300px',
                'main_info' => '20%',
                'manual' => '15%',
                'repair' => '300px',
                'smgs' => '300px',
                'svv' => '30%',
                'kp' => '25%',
            ],
        ],
        '5' => [
            'id' => 'size',
            'name' => 'Размер',
            'search_type' => 'select',
            'width' => [
                'all' => '100px',
                'free' => '100px',
                'main_info' => '20%',
                'manual' => '5%',
                'repair' => '100px',
                'smgs' => '100px',
                'svv' => '20%',
                'kp' => '5%',
            ],
        ],
        '6' => [
            'id' => 'supplier_application_name',
            'name' => 'Заявка',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => '10%',
                'manual' => '5%',
                'repair' => '150px',
                'smgs' => '150px',
                'svv' => '10%',
                'kp' => '10%',
            ],
        ],
        '7' => [
            'id' => 'supplier_price_amount',
            'name' => 'Ставка РК',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => '10%',
            ],
        ],
        '8' => [
            'id' => 'supplier_grace_period',
            'name' => 'ЛП РК',
            'search_type' => 'input',
            'width' => [
                'all' => '100px',
                'free' => '100px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '9' => [
            'id' => 'supplier_snp_after_range',
            'name' => 'СНП РК',
            'search_type' => 'input',
            'width' => [
                'all' => '300px',
                'free' => '300px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '10' => [
            'id' => 'supplier_country',
            'name' => 'Страна',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '11' => [
            'id' => 'supplier_city',
            'name' => 'Местонахождение',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '12' => [
            'id' => 'supplier_terminal',
            'name' => 'Терминал',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '13' => [
            'id' => 'supplier_date_get',
            'name' => 'Дата приема',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '14' => [
            'id' => 'supplier_date_start_using',
            'name' => 'Начало пользования',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '15' => [
            'id' => 'supplier_days_using',
            'name' => 'Дней в пользовании',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '16' => [
            'id' => 'supplier_snp_total',
            'name' => 'СНП от собственика',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '17' => [
            'id' => 'supplier_place_of_delivery_country',
            'name' => 'Страна сдачи',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '18' => [
            'id' => 'supplier_place_of_delivery_city',
            'name' => 'Локация сдачи',
            'search_type' => 'select',
            'width' => [
                'all' => '250px',
                'free' => '250px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '19' => [
            'id' => 'svv',
            'name' => 'СВВ',
            'search_type' => 'input',
            'width' => [
                'all' => '100px',
                'free' => '150px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => '19%',
                'kp' => null,
            ],
        ],
        '20' => [
            'id' => 'supplier_terminal_storage_amount',
            'name' => 'Терминальное хранение',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '21' => [
            'id' => 'supplier_payer_tx',
            'name' => 'Плательщик ТХ',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '300px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '22' => [
            'id' => 'supplier_renewal_reexport_costs_amount',
            'name' => 'Расходы за продления/реэкспорт',
            'search_type' => 'input',
            'width' => [
                'all' => '300px',
                'free' => '300px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '23' => [
            'id' => 'supplier_repair_amount',
            'name' => 'Ремонт при получении ктк',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '200px',
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '24' => [
            'id' => 'supplier_repair_status',
            'name' => 'Статус ремонта',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '150px',
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '25' => [
            'id' => 'supplier_repair_confirmation',
            'name' => 'Подтверждение ремонта собствеником',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '300px',
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '26' => [
            'id' => 'relocation_counterparty_name',
            'name' => 'Перевозчик',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '300px',
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '27' => [
            'id' => 'relocation_application_name',
            'name' => 'Заявка',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '28' => [
            'id' => 'relocation_price_amount',
            'name' => 'Расходы за перевозку',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '29' => [
            'id' => 'relocation_date_send',
            'name' => 'Дата отправки ктк',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => '200px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '30' => [
            'id' => 'relocation_date_arrival_to_terminal',
            'name' => 'Дата сдачи на терминал',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => '200px',
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '31' => [
            'id' => 'relocation_place_of_delivery_city',
            'name' => 'Локация сдачи',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => '150px',
                'main_info' => null,
                'manual' => '5%',
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '32' => [
            'id' => 'relocation_place_of_delivery_terminal',
            'name' => 'Терминал',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '33' => [
            'id' => 'relocation_delivery_time_days',
            'name' => 'Нормативный срок доставки',
            'search_type' => 'input',
            'width' => [
                'all' => '250px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '34' => [
            'id' => 'relocation_snp_after_range',
            'name' => 'СНП за сутки',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '35' => [
            'id' => 'relocation_snp_total',
            'name' => 'Сумма СНП за перевозку',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '36' => [
            'id' => 'relocation_repair_amount',
            'name' => 'Ремонт при возврате ктк',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '37' => [
            'id' => 'relocation_repair_status',
            'name' => 'Статус ремонта',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '38' => [
            'id' => 'relocation_repair_confirmation',
            'name' => 'Подтверждение ремонта клиентом',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => '300px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '39' => [
            'id' => 'client_counterparty_name',
            'name' => 'Клиент',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => '30%',
                'manual' => '15%',
                'repair' => '300px',
                'smgs' => '300px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '40' => [
            'id' => 'client_application_name',
            'name' => 'Заявка',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => '10%',
                'manual' => '10%',
                'repair' => '150px',
                'smgs' => '150px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '41' => [
            'id' => 'client_price_amount',
            'name' => 'Ставка выдачи Клиенту',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '42' => [
            'id' => 'client_grace_period',
            'name' => 'ЛП Клиенту',
            'search_type' => 'input',
            'width' => [
                'all' => '100px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '43' => [
            'id' => 'client_snp_after_range',
            'name' => 'СНП',
            'search_type' => 'input',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '44' => [
            'id' => 'client_date_get',
            'name' => 'Дата выдачи Клиенту',
            'search_type' => 'input',
            'width' => [
                'all' => '180px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => '180px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '45' => [
            'id' => 'client_date_return',
            'name' => 'Дата сдачи клиентом',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => '150px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '46' => [
            'id' => 'supplier_date_return',
            'name' => 'Дата сдачи поставщику',
            'search_type' => 'input',
            'width' => [
                'all' => '180px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '47' => [
            'id' => 'client_place_of_delivery_city',
            'name' => 'Локация сдачи',
            'search_type' => 'select',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '48' => [
            'id' => 'client_days_using',
            'name' => 'Дней пользования',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '49' => [
            'id' => 'client_snp_total',
            'name' => 'СНП клиенту',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '50' => [
            'id' => 'client_repair_amount',
            'name' => 'Ремонт при возврате ктк',
            'search_type' => 'select',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '200px',
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '51' => [
            'id' => 'client_repair_status',
            'name' => 'Статус ремонта',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '150px',
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '52' => [
            'id' => 'client_repair_confirmation',
            'name' => 'Подтверждение ремонта клиентом',
            'search_type' => 'select',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => '300px',
                'smgs' => '300px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '53' => [
            'id' => 'client_smgs',
            'name' => 'СМГС',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => '150px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '54' => [
            'id' => 'client_manual',
            'name' => 'Инструкция',
            'search_type' => 'select',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => '5%',
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '55' => [
            'id' => 'client_location_request',
            'name' => 'Локация запроса',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '56' => [
            'id' => 'client_date_manual_request',
            'name' => 'Дата запроса инструкции',
            'search_type' => 'input',
            'width' => [
                'all' => '250px',
                'free' => null,
                'main_info' => null,
                'manual' => '12%',
                'repair' => null,
                'smgs' => '250px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '57' => [
            'id' => 'client_return_act',
            'name' => 'Акт сдачи',
            'search_type' => 'select',
            'width' => [
                'all' => '100px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => '100px',
                'svv' => null,
                'kp' => null,
            ],
        ],
        '58' => [
            'id' => 'own_date_buy',
            'name' => 'Дата покупки',
            'search_type' => 'input',
            'width' => [
                'all' => '100px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => '5%',
            ],
        ],
        '59' => [
            'id' => 'own_date_sell',
            'name' => 'Дата продажи',
            'search_type' => 'input',
            'width' => [
                'all' => '100px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => '5%',
            ],
        ],
        '60' => [
            'id' => 'own_sale_price',
            'name' => 'Сумма продажи',
            'search_type' => 'input',
            'width' => [
                'all' => '150px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => '5%',
            ],
        ],
        '61' => [
            'id' => 'own_buyer',
            'name' => 'Клиент',
            'search_type' => 'input',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => '300px',
                'svv' => null,
                'kp' => '25%',
            ],
        ],
        '62' => [
            'id' => 'processing',
            'name' => 'Редактируется',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => '10%',
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '63' => [
            'id' => 'removed',
            'name' => 'Удален',
            'search_type' => 'input',
            'width' => [
                'all' => '200px',
                'free' => null,
                'main_info' => null,
                'manual' => null,
                'repair' => null,
                'smgs' => null,
                'svv' => null,
                'kp' => null,
            ],
        ],
        '64' => [
            'id' => 'additional_info',
            'name' => 'Примечание',
            'search_type' => 'input',
            'width' => [
                'all' => '300px',
                'free' => null,
                'main_info' => '30%',
                'manual' => '15%',
                'repair' => '300px',
                'smgs' => '500px',
                'svv' => null,
                'kp' => null,
            ],
        ],
    ];

    public function getContainerUsageDates($id){

        $overdue_days = 0;
        $overdue_days_for_us = 0;

        $container = Container::find($id);

        $today = Carbon::now();
        if(!is_null($container->start_date_for_client)){
            $start_date_for_client = new Carbon ($container->start_date_for_client);
            if(!is_null($container->grace_period_for_client)){
                $end_grace_date_for_client = Carbon::parse($start_date_for_client)->addDays($container->grace_period_for_client)->format('Y-m-d');
                $end_grace_date_client = new Carbon($end_grace_date_for_client);
            }
            else {
                $end_grace_date_for_client = '-';
                $end_grace_date_client = '-';
            }
            if($end_grace_date_client != '-' && $today > $end_grace_date_client){
                $overdue_days = $end_grace_date_client->diff($today)->days;
            }
        }
        else {
            $overdue_days = 0;
            $end_grace_date_for_client = '-';
        }

        if(!is_null($container->start_date_for_us)){
            $start_date_for_us = new Carbon ($container->start_date_for_us);
            if(!is_null($container->grace_period_for_us)){
                $end_grace_date_for_us = Carbon::parse($start_date_for_us)->addDays($container->grace_period_for_us)->format('Y-m-d');
                $end_grace_date_us = new Carbon($end_grace_date_for_us);
            }
            else {
                $end_grace_date_for_us = '-';
                $end_grace_date_us = '-';
            }
            if($end_grace_date_us != '-' && $today > $end_grace_date_us){
                $overdue_days_for_us = $end_grace_date_us->diff($today)->days;
            }
        }
        else {
            $overdue_days_for_us = 0;
            $end_grace_date_for_us = '-';
        }

        $svv_date = $container->svv;

        !is_null($container->snp_range_for_client) ? $range_client = true : $range_client = false;
        !is_null($container->snp_range_for_us) ? $range_us = true : $range_us = false;

        $snp_amount_for_client = 0;
        $snp_amount_for_us = 0;

        $range_client_string = [];
        $range_us_string = [];

        if($range_client){

            $range = unserialize($container->snp_range_for_client);

            foreach ($range as $item){

                $range_client_string [] = $item['range'].' дней - '. $item['price'].$container->snp_currency;

                $days = explode('-', $item['range']);

                if($overdue_days >= $days[1]) {
                    $range_days = $days[1] - $days[0] + 1;
                    $snp_amount_for_client += $range_days*$item['price'];
                }
                else {
                    $snp_amount_for_client += $overdue_days*$item['price'];
                }

            }

            $end_array = $range[array_key_last($range)];
            $finish_days = explode('-', $end_array['range']);

            if($overdue_days>= $finish_days[1]){
                $full_price_days = $overdue_days - $finish_days[1];
                $snp_amount_for_client += $full_price_days*$container->snp_amount_for_client;
            }

        }
        else {
            !is_null($container->snp_amount_for_client) ? $snp_amount_for_client = $container->snp_amount_for_client * $overdue_days : $snp_amount_for_client = '-';
        }

        if($range_us){

            $range = unserialize($container->snp_range_for_us);

            foreach ($range as $item){

                $range_us_string [] = $item['range'].' дней - '. $item['price'].$container->snp_currency;

                $days = explode('-', $item['range']);

                if($overdue_days_for_us >= $days[1]) {
                    $range_days = $days[1] - $days[0] + 1;
                    $snp_amount_for_us += $range_days*$item['price'];
                }
                else {
                    $snp_amount_for_us += $overdue_days_for_us*$item['price'];
                }

            }

            $end_array = $range[array_key_last($range)];
            $finish_days = explode('-', $end_array['range']);
            if($overdue_days_for_us>= $finish_days[1]){
                $full_price_days = $overdue_days_for_us - $finish_days[1];
                $snp_amount_for_us += $full_price_days*$container->snp_amount_for_us;
            }
        }
        else {
            !is_null($container->snp_amount_for_us) ? $snp_amount_for_us = $container->snp_amount_for_us * $overdue_days_for_us : $snp_amount_for_us = '-';
        }

        $range_us_string = implode(' / ', $range_us_string);
        $range_client_string = implode(' / ', $range_client_string);

        return [
            'svv_date' => $svv_date,
            'end_grace_date' => $end_grace_date_for_client,
            'end_grace_date_for_us' => $end_grace_date_for_us,
            'overdue_days' => $overdue_days,
            'overdue_days_for_us' => $overdue_days_for_us,
            'snp_amount_for_client' => $snp_amount_for_client,
            'snp_amount_for_us' => $snp_amount_for_us,
            'snp_currency' => $container->snp_currency,
            'range_client' => $range_client_string,
            'range_us' => $range_us_string
        ];

    }

    public function getUpdatedContainerUsageDates($id){

        $overdue_days = 0;
        $overdue_days_for_us = 0;

        $container_stat = ContainerUsageStatistic::find($id);
        $container = Container::find($container_stat->container_id);

        $today = $container_stat->return_date;
        if(!is_null($container_stat->start_date_for_client)){
            $start_date_for_client = new Carbon ($container_stat->start_date_for_client);
            if(!is_null($container->grace_period_for_client)){
                $end_grace_date_for_client = Carbon::parse($start_date_for_client)->addDays($container->grace_period_for_client)->format('Y-m-d');
                $end_grace_date_client = new Carbon($end_grace_date_for_client);
            }
            else {
                $end_grace_date_for_client = '-';
                $end_grace_date_client = '-';
            }
            if($end_grace_date_client != '-' && $today > $end_grace_date_client){
                $overdue_days = $end_grace_date_client->diff($today)->days;
            }
        }
        else {
            $overdue_days = 0;
            $end_grace_date_for_client = '-';
        }

        if(!is_null($container_stat->start_date_for_us)){
            $start_date_for_us = new Carbon ($container_stat->start_date_for_us);
            if(!is_null($container->grace_period_for_us)){
                $end_grace_date_for_us = Carbon::parse($start_date_for_us)->addDays($container->grace_period_for_us)->format('Y-m-d');
                $end_grace_date_us = new Carbon($end_grace_date_for_us);
            }
            else {
                $end_grace_date_for_us = '-';
                $end_grace_date_us = '-';
            }
            if($end_grace_date_us != '-' && $today > $end_grace_date_us){
                $overdue_days_for_us = $end_grace_date_us->diff($today)->days;
            }
        }
        else {
            $overdue_days_for_us = 0;
            $end_grace_date_for_us = '-';
        }

        $svv_date = $container->svv;

        !is_null($container->snp_range_for_client) ? $range_client = true : $range_client = false;
        !is_null($container->snp_range_for_us) ? $range_us = true : $range_us = false;

        $snp_amount_for_client = 0;
        $snp_amount_for_us = 0;

        $range_client_string = [];
        $range_us_string = [];

        if($range_client){

            $range = unserialize($container->snp_range_for_client);

            foreach ($range as $item){

                $range_client_string [] = $item['range'].' дней - '. $item['price'].$container->snp_currency;

                $days = explode('-', $item['range']);

                if($overdue_days >= $days[1]) {
                    $range_days = $days[1] - $days[0] + 1;
                    $snp_amount_for_client += $range_days*$item['price'];
                }
                else {

                    $snp_amount_for_client += $overdue_days*$item['price'];
                }

            }

            $end_array = $range[array_key_last($range)];
            $finish_days = explode('-', $end_array['range']);

            if($overdue_days>= $finish_days[1]){
                $full_price_days = $overdue_days - $finish_days[1];
                $snp_amount_for_client += $full_price_days*$container->snp_amount_for_client;
            }

        }
        else {
            !is_null($container->snp_amount_for_client) ? $snp_amount_for_client = $container->snp_amount_for_client * $overdue_days : $snp_amount_for_client = '-';
        }

        if($range_us){

            $range = unserialize($container->snp_range_for_us);

            foreach ($range as $item){

                $range_us_string [] = $item['range'].' дней - '. $item['price'].$container->snp_currency;

                $days = explode('-', $item['range']);

                if($overdue_days_for_us >= $days[1]) {
                    $range_days = $days[1] - $days[0] + 1;
                    $snp_amount_for_us += $range_days*$item['price'];
                }
                else {
                    $snp_amount_for_us += $overdue_days_for_us*$item['price'];
                }

            }

            $end_array = $range[array_key_last($range)];
            $finish_days = explode('-', $end_array['range']);
            if($overdue_days_for_us>= $finish_days[1]){
                $full_price_days = $overdue_days_for_us - $finish_days[1];
                $snp_amount_for_us += $full_price_days*$container->snp_amount_for_us;
            }
        }
        else {
            !is_null($container->snp_amount_for_us) ? $snp_amount_for_us = $container->snp_amount_for_us * $overdue_days_for_us : $snp_amount_for_us = '-';
        }

        $range_us_string = implode(' / ', $range_us_string);
        $range_client_string = implode(' / ', $range_client_string);

        return [
            'svv_date' => $svv_date,
            'end_grace_date' => $end_grace_date_for_client,
            'end_grace_date_for_us' => $end_grace_date_for_us,
            'overdue_days' => $overdue_days,
            'overdue_days_for_us' => $overdue_days_for_us,
            'snp_amount_for_client' => $snp_amount_for_client,
            'snp_amount_for_us' => $snp_amount_for_us,
            'range_client' => $range_client_string,
            'range_us' => $range_us_string
        ];

    }

    public function getContainerProjectInfo($id){

        $container_project = ContainerProject::find($id);

        if($container_project->date_departure != ''){
            if($container_project->date_of_arrival != ''){
                $created = new Carbon ($container_project->date_departure);
                $now = new Carbon($container_project->date_of_arrival);
                $on_the_way = ($created->diff($now)->days < 1) ? '0' : $created->diff($now)->days;
            }
            else {
                $created = new Carbon ($container_project->date_departure);
                $now = Carbon::now();
                $on_the_way = ($created->diff($now)->days < 1) ? '0' : $created->diff($now)->days;
            }

        }

        else $on_the_way = 0;

        if($container_project->grace_period != ''){
            $on_the_way > $container_project->grace_period ? $snp_days = $on_the_way - $container_project->grace_period : $snp_days = 0;
        }

        else $snp_days = 0;

        $snp_amount = 0;

        if($snp_days != 0) {
            $snp_amount = $container_project->snp_rub * $snp_days;
        }

        $income = 0;

        if($container_project->rate_for_client_rub != ''){
            $income = (int)$container_project->rate_for_client_rub;
        }

        $income_expected = $income + $snp_amount;

        $outcome = 0;

        if(!is_null($container_project->expenses)){
            foreach ($container_project->expenses as $expense){
                $outcome += $expense[$expense['type'].'_total_price_in_rub'];
            }
        }

        $paid = 0;

        if($container_project->paid_rub != ''){

            $paid = $container_project->paid_rub;

        }

        if ($paid != 0){
            $profit = $paid - $outcome;
        }
        elseif ($income_expected !=0) {
            $profit = $income_expected - $outcome;
        }
        else $profit = null;

        return [
            'on_the_way' => $on_the_way,
            'income_expected' => $income_expected,
            'outcome' => $outcome,
            'paid' => $paid,
            'profit' => $profit,
            'snp_days' => $snp_days
        ];

    }

    public function getOwnContainerFinance($id){

        $container = Container::find($id);
        $container_projects = $container->container_projects;

        $income = 0;
        $outcome = 0;
        !is_null($container->own) ? $prime_cost = $container->own->prime_cost : $prime_cost = 0;
        $profit = 0;

        foreach ($container_projects as $project){

            $info = $this->getContainerProjectInfo($project->id);
            $info['paid'] == 0 ? $income += $info['income_expected'] : $income += $info['paid'];
            $outcome += $info['outcome'];
            $profit += $info['profit'];

        }

        $profitability = $profit - $prime_cost;

        return [
            'income' => $income,
            'outcome' => $outcome,
            'profit' => $profit,
            'profitability' => $profitability
        ];

    }

    public function getUsageInfo($id){
        $supplier_overdue_days = 0;
        $relocation_overdue_days = 0;
        $client_overdue_days = 0;
        $supplier_days_using = 0;
        $relocation_days_using = 0;
        $client_days_using = 0;

        $today = date('Y-m-d');

        $container = Container::find($id);

        $supplier_grace_period = $container->supplier_grace_period;
        $relocation_grace_period = $container->relocation_delivery_time_days;
        $client_grace_period = $container->client_grace_period;

        if(!is_null($container->supplier_date_start_using)){
            if(!is_null($container->client_date_return)) $today = $container->client_date_return;
            $supplier_date_start_using = new Carbon ($container->supplier_date_start_using);
            if(!is_null($supplier_grace_period)){
                $end_grace_date_supplier = Carbon::parse($supplier_date_start_using)->addDays($supplier_grace_period)->format('Y-m-d');
                $end_grace_date_supplier = new Carbon($end_grace_date_supplier);
            }
            else {
                $end_grace_date_supplier = null;
            }
            if(!is_null($end_grace_date_supplier) && $today > $end_grace_date_supplier){
                $supplier_overdue_days = $end_grace_date_supplier->subDays(1)->diff($today)->days;
            }
            $supplier_days_using = $supplier_date_start_using->subDays(1)->diff($today)->days;

        }

        if(!is_null($container->relocation_date_send)){
            if(!is_null($container->relocation_date_arrival_to_terminal)) $today = $container->relocation_date_arrival_to_terminal;
            $relocation_date_start_using = new Carbon ($container->relocation_date_send);
            if(!is_null($relocation_grace_period)){
                $end_grace_date_relocation = Carbon::parse($relocation_date_start_using)->addDays($relocation_grace_period)->format('Y-m-d');
                $end_grace_date_relocation = new Carbon($end_grace_date_relocation);
            }
            else {
                $end_grace_date_relocation = null;
            }
            if(!is_null($end_grace_date_relocation) && $today > $end_grace_date_relocation){
                $relocation_overdue_days = $end_grace_date_relocation->subDays(1)->diff($today)->days;
            }
            $relocation_days_using = $relocation_date_start_using->subDays(1)->diff($today)->days;
        }

        if(!is_null($container->client_date_get)){
            if(!is_null($container->client_date_return)) $today = $container->client_date_return;
            $client_date_start_using = new Carbon ($container->client_date_get);
            if(!is_null($client_grace_period)){
                $end_grace_date_client = Carbon::parse($client_date_start_using)->addDays($client_grace_period)->format('Y-m-d');
                $end_grace_date_client = new Carbon($end_grace_date_client);
            }
            else {
                $end_grace_date_client = null;
            }
            if(!is_null($end_grace_date_client) && $today > $end_grace_date_client){
                $client_overdue_days = $end_grace_date_client->subDays(1)->diff($today)->days;
            }
            $client_days_using = $client_date_start_using->subDays(1)->diff($today)->days;
        }

        !is_null($container->supplier_snp_range) ? $range_supplier = true : $range_supplier = false;
        !is_null($container->client_snp_range) ? $range_client = true : $range_client = false;
        !is_null($container->relocation_snp_range) ? $range_relocation = true : $range_relocation = false;

        $snp_amount_supplier = 0;
        $snp_amount_client = 0;
        $snp_amount_relocation = 0;

        if($supplier_overdue_days > 0){
            if($range_supplier){
                $range = $container->supplier_snp_range;

                foreach ($range as $item){
                    $days = explode('-', $item['range']);
                    if($supplier_overdue_days >= $days[0]) {
                        if($supplier_overdue_days >= $days[1]){
                            $range_days = $days[1] - $days[0];
                            $snp_amount_supplier += ((int)$range_days + 1)*$item['price'];
                        }
                        else {
                            $days_for_cals = $supplier_overdue_days - $days[0];
                            $snp_amount_supplier += ((int)$days_for_cals + 1)*$item['price'];
                        }

                    }
                }

                $end_array = $range[array_key_last($range)];
                $finish_days = explode('-', $end_array['range']);

                if($supplier_overdue_days >= $finish_days[1]){
                    $full_price_days = $supplier_overdue_days - $finish_days[1];
                    $snp_amount_supplier += $full_price_days*$container->supplier_snp_after_range;
                }
            }
            else {
                $snp_amount_supplier = $container->supplier_snp_after_range*$supplier_overdue_days;
            }
        }

        if($client_overdue_days > 0){
            if($range_client){
                $range = $container->client_snp_range;

                foreach ($range as $item){
                    $days = explode('-', $item['range']);
                    if($client_overdue_days >= $days[0]) {
                        if($client_overdue_days >= $days[1]){
                            $range_days = $days[1] - $days[0];
                            $snp_amount_client += ((int)$range_days + 1)*$item['price'];
                        }
                        else {
                            $days_for_cals = $supplier_overdue_days - $days[0];
                            $snp_amount_client += ((int)$days_for_cals + 1)*$item['price'];
                        }

                    }
                }

                $end_array = $range[array_key_last($range)];
                $finish_days = explode('-', $end_array['range']);

                if($client_overdue_days >= $finish_days[1]){
                    $full_price_days = $client_overdue_days - $finish_days[1];
                    $snp_amount_client += $full_price_days*$container->client_snp_after_range;
                }
            }
            else {
                $snp_amount_client = $container->client_snp_after_range*$client_overdue_days;
            }
        }

        if($relocation_overdue_days > 0){
            if($range_relocation){
                $range = $container->relocation_snp_range;

                foreach ($range as $item){
                    $days = explode('-', $item['range']);
                    if($relocation_overdue_days >= $days[1]) {
                        $range_days = $days[1] - $days[0];
                        $snp_amount_relocation += ((int)$range_days + 1)*$item['price'];
                    }
                    else {
                        $days_for_cals = $relocation_overdue_days - $days[0];
                        $snp_amount_relocation += ((int)$days_for_cals + 1)*$item['price'];
                    }
                }

                $end_array = $range[array_key_last($range)];
                $finish_days = explode('-', $end_array['range']);

                if($relocation_overdue_days >= $finish_days[1]){
                    $full_price_days = $relocation_overdue_days - $finish_days[1];
                    $snp_amount_relocation += $full_price_days*$container->relocation_snp_after_range;
                }
            }
            else {
                $snp_amount_relocation = $container->relocation_snp_after_range*$relocation_overdue_days;
            }
        }

        $supplier_days_using != 0 ?: $supplier_days_using = null;
        $client_days_using != 0 ?: $client_days_using = null;
        $snp_amount_supplier != 0 ?: $snp_amount_supplier = null;
        $snp_amount_client != 0 ?: $snp_amount_client = null;
        $snp_amount_relocation != 0 ?: $snp_amount_relocation = null;

        return [
            'supplier_overdue_days' => $supplier_overdue_days,
            'relocation_overdue_days' => $relocation_overdue_days,
            'client_overdue_days' => $client_overdue_days,
            'supplier_days_using' => $supplier_days_using,
            'relocation_days_using' => $relocation_days_using,
            'client_days_using' => $client_days_using,
            'supplier_snp_total' => $snp_amount_supplier,
            'client_snp_total' => $snp_amount_client,
            'relocation_snp_total' => $snp_amount_relocation
        ];

    }

    public function updateUsageInfo(Container $container){
        $usage_info = $this->getUsageInfo($container->id);
        $container->update([
            'supplier_days_using' => $usage_info['supplier_days_using'],
            'supplier_snp_total' => $usage_info['supplier_snp_total'],
            'relocation_snp_total' => $usage_info['relocation_snp_total'],
            'client_days_using' => $usage_info['client_days_using'],
            'client_snp_total' => $usage_info['client_snp_total']
        ]);
    }

    public function saveContainerUsageHistory(Container $container, $application_id){

        $container_array = $container->toArray();
        if(array_key_exists('id', $container_array)) {
            $keys = array_keys($container_array);
            $keys[array_search('id', $keys)] = 'container_id';
            $container_array = array_combine($keys, $container_array);
            foreach ($container_array as $col => $val){
                if(in_array($col, ['supplier_date_get', 'supplier_date_start_using', 'relocation_date_send', 'relocation_date_arrival_to_terminal', 'client_date_get', 'client_date_return', 'client_date_manual_request', 'own_date_buy', 'own_date_sell', 'supplier_date_return'])){
                    if(!is_null($val)){
                        $container_array[$col] = \Carbon\Carbon::parse($val)->format('Y-m-d');
                    }
                }
            }
        }
        ContainerHistory::create($container_array);

        $application = Application::find($application_id);

        $this->clearContainerUsageInfo($container, $application->type);
    }

    public function clearContainerUsageInfo(Container $container, $application_type){

//        if($application_type == 'Поставщик'){
//            $container->update([
//                'supplier_application_id' => null,
//                'supplier_application_name' => null,
//                'supplier_price_amount' => null,
//                'supplier_price_currency' => null,
//                'supplier_price_in_rubles' => null,
//                'supplier_grace_period' => null,
//                'supplier_snp_range' => null,
//                'supplier_snp_after_range' => null,
//                'supplier_snp_currency' => null,
//                'supplier_country' => null,
//                'supplier_city' => null,
//                'supplier_terminal' => null,
//                'supplier_date_get' => null,
//                'supplier_date_start_using' => null,
//                'supplier_days_using' => null,
//                'supplier_snp_total' => null,
//                'supplier_place_of_delivery_country' => null,
//                'supplier_place_of_delivery_city' => null,
//                'supplier_terminal_storage_amount' => null,
//                'supplier_terminal_storage_currency' => null,
//                'supplier_terminal_storage_in_rubles' => null,
//                'supplier_payer_tx' => null,
//                'supplier_renewal_reexport_costs_amount' => null,
//                'supplier_renewal_reexport_costs_currency' => null,
//                'supplier_repair_amount' => null,
//                'supplier_repair_currency' => null,
//                'supplier_repair_in_rubles' => null,
//                'supplier_repair_status' => null,
//                'supplier_repair_confirmation' => null,
//                'removed' => null
//            ]);
//        }
//
//        if($application_type == 'Подсыл'){
//            $container->update([
//                'relocation_counterparty_id' => null,
//                'relocation_counterparty_name' => null,
//                'relocation_counterparty_type' => null,
//                'relocation_application_id' => null,
//                'relocation_application_name' => null,
//                'relocation_price_amount' => null,
//                'relocation_price_currency' => null,
//                'relocation_price_in_rubles' => null,
//                'relocation_date_send' => null,
//                'relocation_date_arrival_to_terminal' => null,
//                'relocation_place_of_delivery_city' => null,
//                'relocation_place_of_delivery_terminal' => null,
//                'relocation_delivery_time_days' => null,
//                'relocation_snp_range' => null,
//                'relocation_snp_after_range' => null,
//                'relocation_snp_currency' => null,
//                'relocation_snp_total' => null,
//                'relocation_repair_amount' => null,
//                'relocation_repair_currency' => null,
//                'relocation_repair_in_rubles' => null,
//                'relocation_repair_status' => null,
//                'relocation_repair_confirmation' => null,
//                'removed' => null
//            ]);
//        }
//
//        if($application_type == 'Клиент'){
//            $container->update([
//                'client_counterparty_id' => null,
//                'client_counterparty_name' => null,
//                'client_application_id' => null,
//                'client_application_name' => null,
//                'client_price_amount' => null,
//                'client_price_currency' => null,
//                'client_price_in_rubles' => null,
//                'client_grace_period' => null,
//                'client_snp_range' => null,
//                'client_snp_after_range' => null,
//                'client_snp_currency' => null,
//                'client_snp_in_rubles' => null,
//                'client_date_get' => null,
//                'client_date_return' => null,
//                'client_place_of_delivery_country' => null,
//                'client_place_of_delivery_city' => null,
//                'client_days_using' => null,
//                'client_snp_total' => null,
//                'client_repair_amount' => null,
//                'client_repair_currency' => null,
//                'client_repair_in_rubles' => null,
//                'client_repair_status' => null,
//                'client_repair_confirmation' => null,
//                'client_smgs' => null,
//                'client_manual' => null,
//                'client_location_request' => null,
//                'client_date_manual_request' => null,
//                'client_return_act' => null,
//                'removed' => null
//            ]);
//        }
//        if(is_null($container->supplier_application_id) && is_null($container->relocation_application_id) && is_null($container->client_application_id)){
//            $container->update([
//                'archive' => 'yes'
//            ]);
//        }
        $container->update([
            'supplier_application_id' => null,
            'supplier_application_name' => null,
            'supplier_price_amount' => null,
            'supplier_price_currency' => null,
            'supplier_price_in_rubles' => null,
            'supplier_grace_period' => null,
            'supplier_snp_range' => null,
            'supplier_snp_after_range' => null,
            'supplier_snp_currency' => null,
            'supplier_country' => null,
            'supplier_city' => null,
            'supplier_terminal' => null,
            'supplier_date_get' => null,
            'supplier_date_start_using' => null,
            'supplier_days_using' => null,
            'supplier_snp_total' => null,
            'supplier_place_of_delivery_country' => null,
            'supplier_place_of_delivery_city' => null,
            'supplier_terminal_storage_amount' => null,
            'supplier_terminal_storage_currency' => null,
            'supplier_terminal_storage_in_rubles' => null,
            'supplier_payer_tx' => null,
            'supplier_renewal_reexport_costs_amount' => null,
            'supplier_renewal_reexport_costs_currency' => null,
            'supplier_repair_amount' => null,
            'supplier_repair_currency' => null,
            'supplier_repair_in_rubles' => null,
            'supplier_repair_status' => null,
            'supplier_repair_confirmation' => null,
            'supplier_date_return' => null,
            'client_counterparty_id' => null,
            'client_counterparty_name' => null,
            'client_application_id' => null,
            'client_application_name' => null,
            'client_price_amount' => null,
            'client_price_currency' => null,
            'client_price_in_rubles' => null,
            'client_grace_period' => null,
            'client_snp_range' => null,
            'client_snp_after_range' => null,
            'client_snp_currency' => null,
            'client_snp_in_rubles' => null,
            'client_date_get' => null,
            'client_date_return' => null,
            'client_place_of_delivery_country' => null,
            'client_place_of_delivery_city' => null,
            'client_days_using' => null,
            'client_snp_total' => null,
            'client_repair_amount' => null,
            'client_repair_currency' => null,
            'client_repair_in_rubles' => null,
            'client_repair_status' => null,
            'client_repair_confirmation' => null,
            'client_smgs' => null,
            'client_manual' => null,
            'client_location_request' => null,
            'client_date_manual_request' => null,
            'client_return_act' => null,
            'relocation_counterparty_id' => null,
            'relocation_counterparty_name' => null,
            'relocation_counterparty_type' => null,
            'relocation_application_id' => null,
            'relocation_application_name' => null,
            'relocation_price_amount' => null,
            'relocation_price_currency' => null,
            'relocation_price_in_rubles' => null,
            'relocation_date_send' => null,
            'relocation_date_arrival_to_terminal' => null,
            'relocation_place_of_delivery_city' => null,
            'relocation_place_of_delivery_terminal' => null,
            'relocation_delivery_time_days' => null,
            'relocation_snp_range' => null,
            'relocation_snp_after_range' => null,
            'relocation_snp_currency' => null,
            'relocation_snp_total' => null,
            'relocation_repair_amount' => null,
            'relocation_repair_currency' => null,
            'relocation_repair_in_rubles' => null,
            'relocation_repair_status' => null,
            'relocation_repair_confirmation' => null,
            'removed' => null,
            'archive' => 'yes'
        ]);

    }

}
