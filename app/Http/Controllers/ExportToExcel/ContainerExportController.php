<?php

namespace App\Http\Controllers\ExportToExcel;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContainerExportController extends Controller
{
    public function exportExtendedTable(Request $request)
    {
        $filename = date('Y-m-d').'_контейнеры_выгрузка.xlsx';

        $array = [];

        if($request->chosen_containers_id != ''){
            foreach (explode(',', $request->chosen_containers_id) as $container_id){

                $container = Container::find($container_id);
                $supplier_snp = $container->supplier_snp_after_range.$container->supplier_snp_currency;
                if(!is_null($container->supplier_snp_range)){
                    $supplier_snp_range = [];
                    foreach ($container->supplier_snp_range as $range){
                        $supplier_snp_range [] = $range['range'].' - '.$range['price'];
                    }
                    $supplier_snp_range [] = 'далее - '.$container->supplier_snp_after_range.$container->supplier_snp_currency;

                    $supplier_snp = implode(', ', $supplier_snp_range);
                }

                $client_snp = $container->client_snp_after_range.$container->client_snp_currency;
                if(!is_null($container->client_snp_range)){
                    $client_snp_range = [];
                    foreach ($container->supplier_snp_range as $range){
                        $client_snp_range [] = $range['range'].' - '.$range['price'];
                    }
                    $client_snp_range [] = 'далее - '.$container->client_snp_after_range.$container->client_snp_currency;

                    $client_snp = implode(', ', $client_snp_range);
                }

                $relocation_snp = $container->relocation_snp_after_range.$container->relocation_snp_currency;
                if(!is_null($container->relocation_snp_range)){
                    $relocation_snp_range = [];
                    foreach ($container->relocation_snp_range as $range){
                        $relocation_snp_range [] = $range['range'].' - '.$range['price'];
                    }
                    $relocation_snp_range [] = 'далее - '.$container->relocation_snp_after_range.$container->relocation_snp_currency;

                    $relocation_snp = implode(', ', $relocation_snp_range);
                }

                if(!is_null($container->supplier_snp_total)){
                    $supplier_snp_total = $container->supplier_snp_total.$container->supplier_snp_currency;
                }
                else {
                    $supplier_snp_total = '';
                }

                if(!is_null($container->relocation_snp_total)){
                    $relocation_snp_total = $container->relocation_snp_total.$container->relocation_snp_currency;
                }
                else {
                    $relocation_snp_total = '';
                }

                if(!is_null($container->client_snp_total)){
                    $client_snp_total = $container->client_snp_total.$container->client_snp_currency;
                }
                else {
                    $client_snp_total = '';
                }

                $array [] = [
                    "id" => $container->id,
                    "status" => $container->status,
                    "type" => $container->type,
                    "owner_name" => $container->owner_name,
                    "size" => $container->size,
                    "supplier_application_name" => $container->supplier_application_name,
                    "supplier_price_amount" => $container->supplier_price_amount.$container->supplier_price_currency,
                    "supplier_grace_period" => $container->supplier_grace_period,
                    "supplier_snp_after_range" => $supplier_snp,
                    "name" => $container->name,
                    "supplier_country" => $container->supplier_country,
                    "supplier_city" => $container->supplier_city,
                    "supplier_terminal" => $container->supplier_terminal,
                    "supplier_date_get" => $container->supplier_date_get,
                    "supplier_date_start_using" => $container->supplier_date_start_using,
                    "supplier_days_using" => $container->supplier_days_using,
                    "supplier_snp_total" => $supplier_snp_total,
                    "supplier_place_of_delivery_country" => $container->supplier_place_of_delivery_country,
                    "supplier_place_of_delivery_city" => $container->supplier_place_of_delivery_city,
                    "svv" => $container->svv,
                    "supplier_terminal_storage_amount" => $container->supplier_terminal_storage_amount.$container->supplier_terminal_storage_currency,
                    "supplier_payer_tx" => $container->supplier_payer_tx,
                    "supplier_renewal_reexport_costs_amount" => $container->supplier_renewal_reexport_costs_amount.$container->supplier_renewal_reexport_costs_currency,
                    "supplier_repair_amount" => $container->supplier_repair_amount.$container->supplier_repair_currency,
                    "supplier_repair_status" => $container->supplier_repair_status,
                    "supplier_repair_confirmation" => $container->supplier_repair_confirmation,
                    "relocation_counterparty_name" => $container->relocation_counterparty_name,
                    "relocation_application_name" => $container->relocation_application_name,
                    "relocation_price_amount" => $container->relocation_price_amount.$container->relocation_price_currency,
                    "relocation_date_send" => $container->relocation_date_send,
                    "relocation_date_arrival_to_terminal" => $container->relocation_date_arrival_to_terminal,
                    "relocation_place_of_delivery_city" => $container->relocation_place_of_delivery_city,
                    "relocation_place_of_delivery_terminal" => $container->relocation_place_of_delivery_terminal,
                    "relocation_delivery_time_days" => $container->relocation_delivery_time_days,
                    "relocation_snp_after_range" => $relocation_snp,
                    "relocation_snp_total" => $relocation_snp_total,
                    "relocation_repair_amount" => $container->relocation_repair_amount.$container->relocation_repair_currency,
                    "relocation_repair_status" => $container->relocation_repair_status,
                    "relocation_repair_confirmation" => $container->relocation_repair_confirmation,
                    "client_counterparty_name" => $container->client_counterparty_name,
                    "client_application_name" => $container->client_application_name,
                    "client_price_amount" => $container->client_price_amount.$container->client_price_currency,
                    "client_grace_period" => $container->client_grace_period,
                    "client_snp_after_range" => $client_snp,
                    "client_date_get" => $container->client_date_get,
                    "client_date_return" => $container->client_date_return,
                    "client_place_of_delivery_city" => $container->client_place_of_delivery_city,
                    "client_days_using" => $container->client_days_using,
                    "client_snp_total" => $client_snp_total,
                    "client_repair_amount" => $container->client_repair_amount.$container->client_repair_currency,
                    "client_repair_status" => $container->client_repair_status,
                    "client_repair_confirmation" => $container->client_repair_confirmation,
                    "client_smgs" => $container->client_smgs,
                    "client_manual" => $container->client_manual,
                    "client_location_request" => $container->client_location_request,
                    "client_date_manual_request" => $container->client_date_manual_request,
                    "client_return_act" => $container->client_return_act,
                    "own_date_buy" => $container->own_date_buy,
                    "own_date_sell" => $container->own_date_sell,
                    "own_sale_price" => $container->own_sale_price,
                    "own_buyer" => $container->own_buyer,
                    "processing" => $container->processing,
                    "removed" => $container->removed,
                    "additional_info" => $container->additional_info,
                ];

                !is_null($container->owner_id) ? $owner = 'supplier/'.$container->owner_id : $owner = null;
                !is_null($container->supplier_application_id) ? $supplier_application = 'application/'.$container->supplier_application_id : $supplier_application = null;
                !is_null($container->relocation_counterparty_id) ? $relocation_counterparty = 'supplier/'.$container->relocation_counterparty_id : $relocation_counterparty = null;
                !is_null($container->relocation_application_id) ? $relocation_application = 'application/'.$container->relocation_application_id : $relocation_application = null;
                !is_null($container->client_counterparty_id) ? $client_counterparty = 'client/'.$container->client_counterparty_id : $client_counterparty = null;
                !is_null($container->client_application_id) ? $client_application = 'application/'.$container->client_application_id : $client_application = null;

                $url [] = [
                    'owner' => $owner,
                    'supplier_application' => $supplier_application,
                    'relocation_counterparty' => $relocation_counterparty,
                    'relocation_application' => $relocation_application,
                    'client_counterparty' => $client_counterparty,
                    'client_application' => $client_application
                ];

            }
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/extended_containers_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        $k = 0;
        $i = 2;
        foreach ($array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$i);

            is_null($url[$k]['owner']) ?: $sheet->getCell('D'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['owner']);
            is_null($url[$k]['supplier_application']) ?: $sheet->getCell('F'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['supplier_application']);
            is_null($url[$k]['relocation_counterparty']) ?: $sheet->getCell('AA'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['relocation_counterparty']);
            is_null($url[$k]['relocation_application']) ?: $sheet->getCell('AB'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['relocation_application']);
            is_null($url[$k]['client_counterparty']) ?: $sheet->getCell('AN'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['client_counterparty']);
            is_null($url[$k]['client_application']) ?: $sheet->getCell('AO'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['client_application']);

            $k++; $i++;
        }

        $folder = 'public/Контейнеры выгрузка/';
        $path = 'public/Контейнеры выгрузка/'.$filename;
        $savepath = 'storage/Контейнеры выгрузка/';

        Storage::makeDirectory($folder);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return Storage::download($path);

    }

}
