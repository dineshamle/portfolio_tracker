<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{

	public function index(Request $request)
	{

		$symbol = $request->id;
		$stockAPI = Config::get('constants.alphavantage.symbol_search');

		//stock details
		$stockSymbol = strtoupper($request->id);
		
		//Current Price
		$stk_curr_price = Cache::remember('stk_curr_price_'.$symbol, 300, function () use ($stockAPI, $symbol) {
			$stockAPICurrPrice = Http::get($stockAPI, [
				'function' => 'TIME_SERIES_INTRADAY',
				'symbol' => $symbol,
				'interval' => '1min',
				'apikey' => env('ALPHAVANTAGE_KEY'),
			]);
			return $stockAPICurrPrice->body();
		});
		

		$stockAPICurrPriceRes = json_decode($stk_curr_price, true);
		$stockCurrPriceTime = $stockCurrPriceVal = '-';
		if(isset($stockAPICurrPriceRes['Time Series (1min)'])){
			foreach ($stockAPICurrPriceRes['Time Series (1min)'] as $key => $value) {
				$stockCurrPriceTime = $key;
				$stockCurrPriceVal = $value['4. close'];
				break;
			}
		}

		//day Open, Close, High, Low
		$stk_ohlc = Cache::remember('stk_ohlc_'.$symbol, 300, function () use ($stockAPI, $symbol) {
			$stockAPIDayDet = Http::get($stockAPI, [
				'function' => 'TIME_SERIES_DAILY',
				'symbol' => $symbol,
				'apikey' => env('ALPHAVANTAGE_KEY'),
			]);
			return $stockAPIDayDet->body();
		});

		$stockAPIDayDetRes = json_decode($stk_ohlc, true);
		$stockOpenPrice = $stockClosePrice = $stockHighPrice = $stockLowPrice = '-';
		if(isset($stockAPIDayDetRes['Time Series (Daily)'])){
			foreach ($stockAPIDayDetRes['Time Series (Daily)'] as $key => $value) {
				$stockOpenPrice = $value['1. open'];
				$stockClosePrice = $value['4. close'];
				$stockHighPrice = $value['2. high'];
				$stockLowPrice = $value['3. low'];
				break;
			}
		}

		//52week high low
		$stk_overview = Cache::remember('stk_overview_'.$symbol, 300, function () use ($stockAPI, $symbol) {
			$stockAPI52WeekDet = Http::get($stockAPI, [
				'function' => 'OVERVIEW',
				'symbol' => $symbol,
				'apikey' => env('ALPHAVANTAGE_KEY'),
			]);
			return $stockAPI52WeekDet->body();
		});

		$stockAPI52WeekDetRes = json_decode($stk_overview, true);
		$stock52WeekHigh = $stock52WeekLow = '-';

		$stock52WeekHigh = isset($stockAPI52WeekDetRes['52WeekHigh'])?$stockAPI52WeekDetRes['52WeekHigh'] : '-';
		$stock52WeekLow = isset($stockAPI52WeekDetRes['52WeekLow'])?$stockAPI52WeekDetRes['52WeekLow']:'-';
		
		$response = [];
		$response['stk_name'] = isset($stockAPI52WeekDetRes['Name'])?$stockAPI52WeekDetRes['Name']:'';
		$response['stk_symbol'] = $stockSymbol;

		$response['stk_curr_price'] = $stockCurrPriceVal;
		$response['stk_curr_time'] = $stockCurrPriceTime;

		$response['stk_open_price'] = $stockOpenPrice;
		$response['stk_close_price'] = $stockClosePrice;
		$response['stk_high_price'] = $stockHighPrice;
		$response['stk_low_price'] = $stockLowPrice;

		$response['stk_52week_high'] = $stock52WeekHigh;
		$response['stk_52week_low'] = $stock52WeekLow;

		return view('stock.stock',$response);
	}
}
