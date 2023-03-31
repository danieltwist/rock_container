<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use App\Http\Requests\StoreExpenseTypeRequest;
use App\Http\Requests\UpdateExpenseTypeRequest;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.expense_types', [
           'expense_types' => ExpenseType::all()->sortBy('category')
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExpenseTypeRequest  $request
     * @return array
     */
    public function store(Request $request)
    {
        if($request->type == 'add_expense_category'){
            ExpenseType::create([
                'name' => $request->name,
                'type' => 'category'
            ]);
        }

        if($request->type == 'add_expense_type'){
            ExpenseType::create([
                'name' => $request->name,
                'category' => $request->category,
                'type' => 'type'
            ]);
        }

        return [
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => 'Успешно добавлено',
            'ajax' => view('settings.ajax.expense_types_settings', [
                'expense_types' => ExpenseType::all()->sortBy('category')
            ])->render()
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpenseType  $expenseType
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseType $expenseType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpenseType  $expenseType
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseType $expenseType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpenseTypeRequest  $request
     * @param  \App\Models\ExpenseType  $expenseType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpenseTypeRequest $request, ExpenseType $expenseType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpenseType  $expenseType
     * @return array
     */
    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();
        return [
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => 'Успешно удалено',
            'ajax' => view('settings.ajax.expense_types_settings', [
                'expense_types' => ExpenseType::all()->sortBy('category')
            ])->render()
        ];
    }

    public function loadExpenseTypesByCategory(Request $request){

        return view('invoice.expense_types_select', [
            'expense_types' => ExpenseType::where('category', $request->category)->get()
        ])->render();

    }

}
