<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use App\Http\Models\User;

class ValidategithubController extends Controller
{

	public function validateGithub(Request $request)
	{

		$response = Http::withHeaders([
			'Accept' => 'application/json'
		])->post(Config::get('constants.github_access_token'), [
			'client_id' => env('GITHUB_CLIENTID'),
			'client_secret' => env('GITHUB_SECRET'),
			'code' => $request->query('code')
		]);

		$accessData = json_decode($response->body(), true);
		$accessToken = $accessData['access_token'];

		$response = Http::withHeaders([
			'Accept' => 'application/json',
			'Authorization' => 'token '.$accessToken,
		])->get(Config::get('constants.github_user'));
		$userData = json_decode($response->body(), true);
		$userEmail = $userData[0]['email'];

		//check if email exists in db then login
		//if email doesn't exist in db then store and then login
		$user = User::updateOrCreate(
			['user_email' => $userEmail],
			['updated_at' => date('Y-m-d H:i:s')]
		);

		session(['user_email' => $userEmail]);
		session(['user_id' => $user->id]);
		
		return redirect()->route('portfolio-view');
	}
}
