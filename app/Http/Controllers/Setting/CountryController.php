<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(){

        $countries = Country::all();

        return view('settings/countries', [
           'countries' => $countries
        ]);
    }

    public function store(Request $request){

        $new_country = new Country();
        $new_country->name = $request->name;
        $new_country->save();

        return redirect()->back()->withSuccess(__('settings.country_added_successfully'));
    }

    public function destroy(Country $country){

        $country->delete();
        return redirect()->back()->withSuccess(__('settings.country_removed_successfully'));

    }
}
