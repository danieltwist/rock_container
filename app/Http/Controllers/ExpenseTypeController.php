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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = [
            [
                'name' => 'ФОТ',
                'type' => 'category'
            ],

            [
                'name' => 'НАЛОГИ',
                'type' => 'category'
            ],

            [
                'name' => 'УР Б/Н',
                'type' => 'category'
            ],

            [
                'name' => 'УРС',
                'type' => 'category'
            ],

            [
                'name' => 'КОМИССИЯ БАНКА',
                'type' => 'category'
            ],

            [
                'name' => 'ШТРАФЫ',
                'type' => 'category'
            ],




            [
                'name' => 'БАНК',
                'type' => 'type',
                'category' => 'ФОТ'
            ],

            [
                'name' => 'УРС',
                'type' => 'type',
                'category' => 'ФОТ'
            ],

            [
                'name' => 'НДФЛ',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'ПФР',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'ФФОМС',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'ФСС',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'НС ФСС',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'НДС',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'НП',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'УСН',
                'type' => 'type',
                'category' => 'НАЛОГИ'
            ],

            [
                'name' => 'ТОПЛИВО',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'АРЕНДА ОФИСА',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ВОДА',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ПРИНТЕР',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'САЙТ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ПОДБОР КАДРОВ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'КАНЦЕЛЯРИЯ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => '1С/КОНС+',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'КРИПТОПРО',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'РЖД/ЭТРАН',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ИНТЕРНЕТ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'СОТОВАЯ СВЯЗЬ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ПЛОМБЫ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ОФИС ИНВЕРТАРЬ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ПРОЧЕЕ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'КОМАНДИРОВОЧНЫЕ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ДЕЯТ.КОМП',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'СРМ СИСТЕМА',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ПРОЧЕЕ',
                'type' => 'type',
                'category' => 'УР Б/Н'
            ],

            [
                'name' => 'ПРОДУКТЫ ОФИС',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ПРАЗДНИКИ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ПОЧТОВЫЕ РАСХОДЫ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'КОМАНДИРОВОЧНЫЕ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ПРЕДСТАВИТЕЛЬСКИЕ РАСХОДЫ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ГСМ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ДЕЯТ.КОМП',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'СРМ СИСТЕМА',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'АГЕНТСКИЕ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ПРОЧЕЕ',
                'type' => 'type',
                'category' => 'УРС'
            ],

            [
                'name' => 'ВЕДЕНИЕ СЧЕТА',
                'type' => 'type',
                'category' => 'КОМИССИЯ БАНКА'
            ],

            [
                'name' => 'ВАЛЮТНЫЕ ОПЕРАЦИИ',
                'type' => 'type',
                'category' => 'КОМИССИЯ БАНКА'
            ],

            [
                'name' => 'ИФНС',
                'type' => 'type',
                'category' => 'ШТРАФЫ'
            ],
            [
                'name' => 'ФОНДЫ',
                'type' => 'type',
                'category' => 'ШТРАФЫ'
            ],
            [
                'name' => 'ОТ ДЕЯТ. КОМПАНИИ',
                'type' => 'type',
                'category' => 'ШТРАФЫ'
            ],
            [
                'name' => 'ПРОЧЕЕ',
                'type' => 'type',
                'category' => 'ШТРАФЫ'
            ],

        ];

        foreach ($types as $type){
            ExpenseType::create($type);
        }

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
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExpenseTypeRequest $request)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseType $expenseType)
    {
        //
    }

    public function loadExpenseTypesByCategory(Request $request){

        return view('invoice.expense_types_select', [
            'expense_types' => ExpenseType::where('category', $request->category)->get()
        ])->render();

    }

}
