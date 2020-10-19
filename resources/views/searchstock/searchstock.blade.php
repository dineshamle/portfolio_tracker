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
		font-size: 50px;
	}

	.searchbox{
		height:30px;
		font-size:14pt;
		width: 400px;
	}

	.m-b-md {
		margin-bottom: 10px;
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
			<a href="{{ route('portfolio-view') }}">Portfolio</a> | 
			{{ Session::get('user_email')}} (<a href="/logout">Logout</a>)
		</div>
		@else
		<div style="position: absolute; top: 0; right: 0; width: 100px; text-align:right; padding-right: 10px">
			<a href="{{ Config::get('constants.github_auth') }}?client_id={{ env('GITHUB_CLIENTID') }}&scope=user&allow_signup=true&redirect_uri=http://localhost:8000/validate-github">Login</a>
		</div>
		@endif
		<div class="content">
			<div class="title">
				Search Stocks
			</div>
			<div>
				<input class="searchbox" type="" name="searchstock" id="searchstock" placeholder="Type stock name or Ticker">
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$( function() {

		$( "#searchstock" ).autocomplete({
			source: function( request, response ) {
				$.ajax( {
					type: 'POST',
					url: "{{ route('searchstock.post') }}",
					// dataType: "jsonp",
					data: {
						term: request.term
					},
					success: function( data ) {
						response( data );
					}
				} );
			},
			minLength: 2,
			select: function( event, ui ) {
				var url = '{{ route("stock.index", ":id") }}';
				url = url.replace(':id', ui.item.id);
				window.open(url);
			}
		} );
	} );
</script>
</html>