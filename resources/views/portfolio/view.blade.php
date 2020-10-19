<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Laravel</title>

	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

	<!-- Styles -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
	html, body {
		background-color: #fff;
		color: #636b6f;
		font-family: 'Nunito', sans-serif;
		font-weight: 200;
		height: 100vh;
		margin: 0;
		padding: 5px
	}

	.full-height {
		height: 100vh;
	}

	.flex-center {
		align-items: center;
		display: flex;
		justify-content: center;
	}

	.position-ref {
		position: relative;
	}

	.top-right {
		position: absolute;
		right: 10px;
		top: 18px;
	}

	.content {
		text-align: center;
	}

	.title {
		font-size: 30px;
	}

	.searchbox{
		height:30px;
		font-size:14pt;
		width: 400px;
	}

	.m-b-md {
		margin-bottom: 10px;
	}

	table {
		border-collapse: collapse; 
		border:1px solid #69899F;
	}

	table td{
		font-size: 13px;
		border:1px solid #69899F;
		padding:5px;
	}
</style>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
	<div class="flex-center position-ref full-height">
		@if(Session::has('user_email'))
		<div style="position: absolute; top: 0; right: 0; width: 350px; text-align:right; padding-right: 10px">
			<a href="{{ route('landingpage') }}">Search Stock</a> | 
			{{ Session::get('user_email')}} (<a href="/logout">Logout</a>)
		</div>
		@endif
		<div class="content">
			@if(Session::has('success'))
			<div style="color: green;">
				{{Session::get('success')}}
			</div>
			@endif

			<div class="title">
				Your Portfolio
			</div>
			<div>
				<table style="width: 98%">
					<tr>
						<td>Ticker</td>
						<td>Stock Name</td>
						<td>Markey Cap</td>
						<td>Exchange</td>
						<td>Day High</td>
						<td>Day Low</td>
						<td>52 Week High</td>
						<td>52 Week Low</td>
						<td>Day Open</td>
						<td>Day Close</td>
						<td>Latest Price</td>
						<td>Stocks Held</td>
						<td>Stock Purchase Price</td>
						<td>Current Value</td>
						<td>Profit</td>
						<td>Update</td>
						<td>Delete</td>
					</tr>

					@foreach($portfolios as $stock)
					<tr>
						<td><a href="{{ route('stock.index', $stock->stock_symbol) }}">{{ $stock->stock_symbol }}</a></td>
						<td>{{ $tickerdata[$stock->stock_symbol]['comp_name'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['market_cap'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['exchange'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['day_high'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['day_low'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['week52_high'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['week52_low'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['day_open'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['day_close'] }}</td>
						<td>{{ $tickerdata[$stock->stock_symbol]['latest_price'] }}</td>
						<td><input style="width: 40px; text-align: right" type="number" id="qty_{{$stock->id}}" name="qty_{{$stock->id}}" value="{{ $stock->stock_qty }}"></td>
						<td><input style="width: 60px; text-align: right" type="number" step="0.01" id="buy_price_{{$stock->id}}" name="buy_price_{{$stock->id}}" value="{{ $stock->stock_buy_price }}"></td>
						<td>{{ $stock->curr_val }}</td>
						<td>{{ $stock->profit }}</td>
						<td><input type="submit" name="updatestock" value="Update" onclick="updaterecord({{$stock->id}})"></td>
						<td><input type="submit" name="deletestock" value="Delete" onclick="deleterecord({{$stock->id}})"></td>
					</tr>
					@endforeach

					@if($portfolios->count() == 0)
					<tr><td colspan="17">No Data</td></tr>
					@endif
				</table>
			</div>
			<div style="padding-top: 20px">
				<table>
					<tr>
						<td>Total Stocks</td>
						<td>{{ $summarydata['tot_stk_held'] }}</td>
					</tr>
					<tr>
						<td>Total Purchase Price</td>
						<td>{{ $summarydata['tot_purchase_price'] }}</td>
					</tr>
					<tr>
						<td>Total Current Value</td>
						<td>{{ $summarydata['tot_curr_value'] }}</td>
					</tr>
					<tr>
						<td>Profit as on Date</td>
						<td>{{ $summarydata['tot_profit'] }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	function updaterecord(id){
		var r = confirm("You are about to update a record. Please click 'OK' to proceed.");
		if (r == true) {
			$.ajax({
				url: "{{ route('portfolio-update') }}",
				type: "post",
				dataType: 'json',
				data: { "_token": "{{ csrf_token() }}", 'id': id,'stock_qty': $('#qty_'+id).val(), 'stock_buy_price': $('#buy_price_'+id).val()} ,
				success: function (response) {
					alert('Data updated successfully');
					location.reload();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					var res = JSON.parse(jqXHR.responseText) 
					var err = '';
					if (typeof res.errors.stock_qty !== 'undefined') 
					{
						err += res.errors.stock_qty[0]+'\n';
					}
					if (typeof res.errors.stock_buy_price !== 'undefined') 
					{
						err += res.errors.stock_buy_price[0];
					}
					alert(err);
				}
			});
		} else {
			return false;
		}
	}

	function deleterecord(id){
		var r = confirm("You are about to delete a record. Please click 'OK' to proceed.");
		if (r == true) {
			$.ajax({
				url: "{{ route('portfolio-delete') }}",
				type: "post",
				data: { "_token": "{{ csrf_token() }}", 'id': id} ,
				success: function (response) {
					alert('Data deleted successfully');
					location.reload();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}
			});
		} else {
			return false;
		}
	}
</script>
</html>