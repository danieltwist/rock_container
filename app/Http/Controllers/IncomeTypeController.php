<?php

namespace App\Http\Controllers;

use App\Models\IncomeType;
use Illuminate\Http\Request;

class IncomeTypeController extends Controller
{

    public function index()
    {
        return view('settings.income_types', [
            'income_types' => IncomeType::all()->sortBy('category')
        ]);

    }
    public function store(Request $request)
    {
        if($request->type == 'add_income_category'){
            IncomeType::create([
                'name' => $request->name,
                'type' => 'category'
            ]);
        }

        if($request->type == 'add_income_type'){
            IncomeType::create([
                'name' => $request->name,
                'category' => $request->category,
                'type' => 'type'
            ]);
        }

        return [
            'bg-class' => 'bg-success',
            'from' => __('interface.system'),
            'message' => __('invoice.added_successfully'),
            'ajax' => view('settings.ajax.income_types_settings', [
                'income_types' => IncomeType::all()->sortBy('category')
            ])->render()
        ];
    }

    public function destroy(IncomeType $IncomeType)
    {
        $IncomeType->delete();
        return [
            'bg-class' => 'bg-success',
            'from' => __('interface.system'),
            'message' => __('invoice.removed_successfully'),
            'ajax' => view('settings.ajax.income_types_settings', [
                'income_types' => IncomeType::all()->sortBy('category')
            ])->render()
        ];
    }

    public function loadIncomeTypesByCategory(Request $request){

        return view('invoice.income_types_select', [
            'income_types' => IncomeType::where('category', $request->category)->get()
        ])->render();

    }
}
