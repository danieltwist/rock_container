<?php

namespace App\Http\Controllers\ExportToExcel;

use App\Filters\ApplicationFilter;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationExportController extends Controller
{
    function exportApplications (ApplicationFilter $filter, Request $request){


        $applications = Application::filter($filter);

        $sort = null;

        if($request->status != 'undefined'){
            $applications = $applications->where('type', $request->status);

            switch ($request->status) {
                case 'Поставщик':
                    $sort = 'Взять в аренду';
                    break;
                case 'Клиент':
                    $sort = 'Выдать в аренду';
                    break;
                default:
                    $sort = $request->status;
            }
        }

        $applications = $applications->orderBy('id', 'DESC')->get();

        $array = [];

        foreach ($applications as $application){

            $from = '';
            $to = '';
            $place_of_delivery = '';

            switch ($application->type) {
                case 'Поставщик':
                    $type = 'Взять в аренду';
                    break;
                case 'Клиент':
                    $type = 'Выдать в аренду';
                    break;
                default:
                    $type = $application->type;
            }

            if($application->surcharge == 1){
                $type .= ' / Доплатная';
            }

            if(!is_null($application->send_from_country)){
                $from = $application->send_from_country.', ';
                if(!is_null($application->send_from_city)){
                    $from .= implode('/', $application->send_from_city);
                }
            }
            if(!is_null($application->send_to_country)){
                $to = $application->send_to_country.', ';
                if(!is_null($application->send_to_city)){
                    $to .= implode('/', $application->send_to_city);
                }
            }
            if(!is_null($application->place_of_delivery_country)){
                $place_of_delivery = $application->place_of_delivery_country.', ';
                if(!is_null($application->place_of_delivery_city)){
                    $place_of_delivery .= implode('/', $application->place_of_delivery_city);
                }
            }

            if(!is_null($application->client_name)) {
                $counterparty = 'Клиент '.$application->client_name;
            }
            else {
                $counterparty = 'Поставщик '.$application->supplier_name;
            }

            if(!is_null($application->contract_info)){
                $contract = $application->contract_info['name'];
            }
            else $contract = '';

            if(!is_null($application->snp_range)){
                foreach($application->snp_range as $range) {
                    $snp .= $range['range']. ' день - '.$range['price'].$application->snp_currency;
                }
                $snp .= '; далее '.$application->snp_after_range. $application->snp_currency;
            }
            elseif (is_null($application->snp_after_range)){
                $snp = '';
            }
            else $snp = $application->snp_after_range. $application->snp_currency;

            if(!is_null($application->containers)){
                $containers_fact = count($application->containers);
                $containers_list = implode(', ', $application->containers);
            }
            else {
                $containers_fact = 0;
                $containers_list = '';
            }

            !is_null($application->grace_period) ? $grace_period = $application->grace_period.' дней' :  $grace_period = '';

            $array [] = [
                'id' => $application->id,
                'name' => $application->name,
                'date' => $application->created_at->format('d.m.Y'),
                'type' => $type,
                'status' => $application->status,
                'counterparty' => $counterparty,
                'contract' => $contract,
                'from' => $from,
                'to' => $to,
                'place_of_delivery' => $place_of_delivery,
                'price' => $application->price_amount.$application->price_currency,
                'grace_period' => $grace_period,
                'snp' => $snp,
                'containers_amount' => $application->containers_amount,
                'containers_fact' => $containers_fact,
                'containers_list' => $containers_list,
                'user' => $application->user_name,
                'info' => $application->additional_info
            ];
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/application_export_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        $parameters = unserialize($request->parameters);

        is_null($sort) ? $type = $parameters['sorting_type'] :  $type = $parameters['sorting_type'] . ' / '. $sort;

        $sheet->setCellValue('A1', $type);

        $i = 3;

        foreach ($array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$i);
            $i++;
        }

        $filename = config('app.prefix_view').preg_replace ("/[^a-zA-ZА-Яа-я0-9.,_:;?!\s]/u","",date('Y-m-d').'_'.$parameters['filename'].'_выгрузка.xlsx');

        $path = 'public/Заявки/'.$filename;
        $savepath = 'storage/Заявки/';

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return Storage::download($path);

    }
}
