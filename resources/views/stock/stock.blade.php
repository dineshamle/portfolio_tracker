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
		padding: 5px;
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
		width: 800px;
		border-collapse: collapse; 
		border:1px solid #69899F;
	}

	table td{
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
		<div style="position: absolute; top: 0; right: 0; width: 500px; text-align:right; padding-right: 10px">
			<a href="{{ route('landingpage') }}">Search Stock</a> |
			<a href="{{ route('portfolio-view') }}">Portfolio</a> | 
			{{ Session::get('user_email')}} (<a href="/logout">Logout</a>)
		</div>
		@else
		<div style="position: absolute; top: 0; right: 0; width: 200px; text-align:right; padding-right: 10px">
			<a href="{{ route('landingpage') }}">Search Stock</a> |
			<a href="{{ Config::get('constants.github_auth') }}?client_id={{ env('GITHUB_CLIENTID') }}&scope=user&allow_signup=true&redirect_uri=http://localhost:8000/validate-github">Login</a>
		</div>
		@endif
		<div class="content">
			<div style="color: red;">
				@foreach ($errors->all() as $error)
				{{ $error }}<br/>
				@endforeach
			</div>

			

			<div class="title">
				Stock: <b>{{ $stk_name }}</b> ({{ $stk_symbol }})
			</div>
			<div>
				<table>
					<tr>
						<td>Stock Price (Date Time)</td>
						<td>{{ $stk_curr_price }} ({{$stk_curr_time}})</td>
					</tr>
					<tr>
						<td>Day High</td>
						<td>{{ $stk_high_price }}</td>
					</tr>
					<tr>	
						<td>Day Low</td>
						<td>{{ $stk_low_price }}</td>
					</tr>
					<tr>
						<td>Day Open</td>
						<td>{{ $stk_open_price }}</td>
					</tr>
					<tr>
						<td>Day Close</td>
						<td>{{ $stk_close_price }}</td>
					</tr>
					<tr>
						<td>52 Week High</td>
						<td>{{ $stk_52week_high }}</td>
					</tr>
					<tr>
						<td>52 Week Low</td>
						<td>{{ $stk_52week_low }}</td>
					</tr>
				</table>
			</div>
			@if(Session::has('user_email'))
			<div style="padding-top: 20px">
				<form method="POST" action="/portfolio/add">
					{{ csrf_field() }}
					<input type="hidden" name="stk_symbol" id="stk_symbol" value="{{ $stk_symbol }}">
					<table style="width: 800px">
						<tr>
							<td>Stock Quantity</td>
							<td><input type="number" name="stock_qty" id="stock_qty"></td>
							<td>Buy Price</td>
							<td><input type="number" step="0.01" name="stock_buy_price" id="stock_buy_price"></td>
							<td><input type="submit" name=""></td>
						</tr>
					</table>
				</form>
			</div>
			@endif
		</div>
		
	</div>
</body>
<script type="text/javascript">
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

</script>
</html>