<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Container;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request){

        $projects = Project::where('name', 'like', '%'.$request->search.'%')->orderBy('created_at', 'desc')->limit(5)->get();
        $invoices = Invoice::where('id', 'like', '%'.$request->search.'%')->orderBy('created_at', 'desc')->limit(5)->get();
        $clients = Client::where('name', 'like', '%'.$request->search.'%')->orderBy('created_at', 'desc')->limit(5)->get();
        $suppliers = Supplier::where('name', 'like', '%'.$request->search.'%')->orderBy('created_at', 'desc')->limit(5)->get();
        $contrainers = Container::where('name', 'like', '%'.$request->search.'%')->orderBy('created_at', 'desc')->limit(5)->get();

        $results = '<div class="list-group">';

        foreach ($projects as $project){
            $results .= '<a href="/project/'.$project->id.'" class="list-group-item">
            <div class="search-title">
                '.$project->name.'
            </div>
            <div class="search-path">'
                . __('general.projects') .
            '</div>
            ';
        }
        foreach ($invoices as $invoice){
            $results .= '<a href="/invoice/'.$invoice->id.'" class="list-group-item">
            <div class="search-title">
                Счет №'.$invoice->id.' на сумму '.$invoice->amount.'р.
            </div>
            <div class="search-path">'
                . __('general.invoices') .
            '</div>
            ';
        }
        foreach ($clients as $client){
            $results .= '<a href="/client/'.$client->id.'" class="list-group-item">
            <div class="search-title">
                '.$client->name.'
            </div>
            <div class="search-path">'
                . __('general.clients') .
            '</div>
            ';
        }
        foreach ($suppliers as $supplier){
            $results .= '<a href="/supplier/'.$supplier->id.'" class="list-group-item">
            <div class="search-title">
                '.$supplier->name.'
            </div>
            <div class="search-path">'
                . __('general.suppliers') .
            '</div>
            ';
        }
        foreach ($contrainers as $contrainer){
            $results .= '<a href="/container/'.$contrainer->id.'" class="list-group-item">
            <div class="search-title">
                '.$contrainer->name.'
            </div>
            <div class="search-path">'
                . __('general.containers') .
            '</div>
            ';
        }
        $results .= '</div>';

        return $results;
    }
}
