<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientExcelImport extends Controller
{
    public function uploadList (Request $request)
    {
        $message = __('client.error_in_template');

        if($request->hasFile('clients_list')) {

            $file = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->clients_list);
            $worksheet = $file->getActiveSheet();
            $rows = [];

            $error_found = false;
            foreach ($worksheet->getRowIterator() AS $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                $rows[] = $cells;
            }

            $isFirst = true;
            $added = 0;
            $deleted = 0;
            foreach ($rows as $row){

                if ($isFirst)
                {
                    $isFirst = false;
                    continue;
                }

                $inn = str_replace(' ', '', $row[1]);
                $client = Client::where('inn', $inn)->orWhere('name', $row[0])->first();

                if(!is_null($client)){
                    $deleted++;
                    continue;
                }
                else{
                    if($row[0] != ''){
                        $new_client = new Client();

                        $new_client->name = $row[0];
                        $new_client->requisites = $row[2];
                        $new_client->inn = $inn;
                        $new_client->country = str_replace(' ', '', $row[3]);

                        $new_client->save();

                        $added++;
                    }
                    else{
                        $deleted++;
                        continue;
                    }

                }

            }

            if ($error_found){
                return redirect()->back()->withError($message);
            }
            else
            {
                return redirect()->route('client.index')->withSuccess(__('client.successfully_added') .' '. $added.' ' . __('client.clients') . ', ' . __('client.not_added') . ' '.$deleted.' ' . __('client.clients'));
            }

        }

        return redirect()->back()->withError(__('general.first_choose_file'));

    }
}
