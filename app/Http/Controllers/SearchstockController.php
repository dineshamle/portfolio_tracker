<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SearchstockController extends Controller
{
	public function index()
	{
		return view('searchstock.searchstock');
	}

	public function searchStock(Request $request)
	{
		$input = $request->all();
		$searchString = $input['term'];

		$stockAPI = Config::get('constants.alphavantage.symbol_search');
		$stk_search = Cache::remember('stk_search_'.$searchString, 86400, function () use ($stockAPI, $searchString) {
			$searchStockAPIRes = Http::get($stockAPI, [
				'function' => 'SYMBOL_SEARCH',
				'keywords' => $searchString,
				'apikey' => env('ALPHAVANTAGE_KEY'),
			]);
			return $searchStockAPIRes->body();
		});
		
		$searchStockAPIRes = json_decode($stk_search, true);

		$response = [];
		foreach ($searchStockAPIRes['bestMatches'] as $key => $value) {
			$response[] = ['id'=> $value['1. symbol'], 'label' => $value['2. name'], 'value' => $value['2. name']];
		}

		return response()->json($response);
	}
}
