<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Portfolio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{

	public function add(Request $request)
	{

		$this->validate($request, [
			'stock_qty' => 'required|numeric|min:0|not_in:0',
			'stock_buy_price' => 'required|between:0,999999.99',
		]);

		$data = $request->input();
		$portfolio = new Portfolio;
		$portfolio->user_id = session('user_id');
		$portfolio->stock_symbol = $data['stk_symbol'];
		$portfolio->stock_qty = $data['stock_qty'];
		$portfolio->stock_buy_price = $data['stock_buy_price'];
		$portfolio->save();

		return redirect()->route('portfolio-view')->with('success',"Data added successfully");
	}

	public function update(Request $request)
	{
		$this->validate($request, [
			'stock_qty' => 'required|numeric|min:0|not_in:0',
			'stock_buy_price' => 'required|between:0,999999.99',
		]);
		
		$data = $request->input();
		Portfolio::where('id', $data['id'])->update(array('stock_qty' => $data['stock_qty'], 'stock_buy_price' => $data['stock_buy_price']));
		return response()->json(['status' => 'success']);
	}

	public function delete(Request $request)
	{
		$data = $request->input();
		Portfolio::destroy($data['id']);
		return response()->json(['status' => 'success']);
	}

	public function view(Request $request){
		if(session('user_id')){
			$data = Portfolio::where('user_id', session('user_id'))
			->orderBy('updated_at', 'desc')->get();

			$tickerArr = [];
			foreach ($data as $key => $value) {
				$tickerArr[] = $value->stock_symbol;
			}
			$tickerArr = array_unique($tickerArr);

			$stockAPI = Config::get('constants.alphavantage.symbol_search');

			$tickerDataArr = [];
			foreach ($tickerArr as $ticker) {
				$tickerDataArr[$ticker]['comp_name'] = '-';
				$tickerDataArr[$ticker]['market_cap'] = '-';
				$tickerDataArr[$ticker]['exchange'] = '-';
				$tickerDataArr[$ticker]['day_high'] = '-';
				$tickerDataArr[$ticker]['day_low'] = '-';
				$tickerDataArr[$ticker]['week52_high'] = '-';
				$tickerDataArr[$ticker]['week52_low'] = '-';
				$tickerDataArr[$ticker]['day_open'] = '-';
				$tickerDataArr[$ticker]['day_close'] = '-';
				$tickerDataArr[$ticker]['latest_price'] = '-';

				//day Open, Close, High, Low
				$stk_ohlc = Cache::remember('stk_ohlc_'.$ticker, 300, function () use ($stockAPI, $ticker) {
					$stockAPIDayDet = Http::get($stockAPI, [
						'function' => 'TIME_SERIES_DAILY',
						'symbol' => $ticker,
						'apikey' => env('ALPHAVANTAGE_KEY'),
					]);
					return $stockAPIDayDet->body();
				});

				$stockAPIDayDetRes = json_decode($stk_ohlc, true);
				if(isset($stockAPIDayDetRes['Time Series (Daily)'])){
					foreach ($stockAPIDayDetRes['Time Series (Daily)'] as $key => $value) {
						$tickerDataArr[$ticker]['day_open'] = $value['1. open'];
						$tickerDataArr[$ticker]['day_close'] = $value['4. close'];
						$tickerDataArr[$ticker]['day_high'] = $value['2. high'];
						$tickerDataArr[$ticker]['day_low'] = $value['3. low'];
						$tickerDataArr[$ticker]['latest_price'] = $value['4. close'];
						break;
					}
				}

				//stock overview
				$stk_overview = Cache::remember('stk_overview_'.$ticker, 86400, function () use ($stockAPI, $ticker) {
					$stockAPI52WeekDet = Http::get($stockAPI, [
						'function' => 'OVERVIEW',
						'symbol' => $ticker,
						'apikey' => env('ALPHAVANTAGE_KEY'),
					]);
					return $stockAPI52WeekDet->body();
				});

				$stk_overview = json_decode($stk_overview, true);
				$tickerDataArr[$ticker]['comp_name'] = isset($stk_overview['Name'])?$stk_overview['Name'] : '-';
				$tickerDataArr[$ticker]['market_cap'] = isset($stk_overview['MarketCapitalization'])?$stk_overview['MarketCapitalization']:'-';
				$tickerDataArr[$ticker]['exchange'] = isset($stk_overview['Exchange'])?$stk_overview['Exchange']:'-';
				$tickerDataArr[$ticker]['week52_high'] = isset($stk_overview['52WeekHigh'])?$stk_overview['52WeekHigh']:'-';
				$tickerDataArr[$ticker]['week52_low'] = isset($stk_overview['52WeekLow'])?$stk_overview['52WeekLow']:'-';
			}
			// dd($data);

			$summaryDataArr = ['tot_stk_held' => 0, 'tot_purchase_price' => 0, 'tot_curr_value' => 0, 'tot_profit' => 0];
			foreach ($data as $key => $value) {
				$data[$key]->curr_val = $curr_val = $tickerDataArr[$value->stock_symbol]['latest_price'] * $value->stock_qty;

				$data[$key]->profit = ($tickerDataArr[$value->stock_symbol]['latest_price'] * $value->stock_qty - $value->stock_qty * $value->stock_buy_price);
				$summaryDataArr['tot_stk_held'] += $value->stock_qty;
				$summaryDataArr['tot_purchase_price'] += $value->stock_qty * $value->stock_buy_price;
				$summaryDataArr['tot_curr_value'] += $curr_val;
				$summaryDataArr['tot_profit'] += ($curr_val - ($value->stock_qty * $value->stock_buy_price));
			}

			return view('portfolio.view',['portfolios' => $data, 'tickerdata' => $tickerDataArr, 'summarydata' => $summaryDataArr]);
		}else{
			return redirect()->route('landingpage');
		}
	}
}
