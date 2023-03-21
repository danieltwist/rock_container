<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierExcelImport extends Controller
{
    public function uploadList (Request $request)
    {
        $message = __('client.error_in_template');

        if($request->hasFile('suppliers_list')) {

            $file = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->suppliers_list);
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
                $supplier = Supplier::where('inn', $inn)->orWhere('name', $row[0])->first();

                if(!is_null($supplier)){
                    $deleted++;
                    continue;
                }
                else{
                    if($row[0] != ''){
                        $new_supplier = new Supplier();

                        $new_supplier->name = $row[0];
                        $new_supplier->requisites = $row[2];
                        $new_supplier->inn = $inn;
                        $new_supplier->country = str_replace(' ', '', $row[3]);
                        $new_supplier->type = $row[4];

                        $new_supplier->save();

                        $added++;
                    }
                    else {
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
                return redirect()->route('supplier.index')->withSuccess(__('client.successfully_added') .' '. $added.' ' . __('supplier.suppliers') . ', ' . __('client.not_added') . ' '.$deleted.' ' . __('supplier.suppliers'));
            }

        }

        return redirect()->back()->withError(__('general.first_choose_file'));

    }
}
