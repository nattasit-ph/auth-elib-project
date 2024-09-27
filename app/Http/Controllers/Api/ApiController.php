<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use JWTAuth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{

 	protected function parseJWT() 
 	{
		$jwt = JWTAuth::getToken();
		// print_r(JWTAuth::parseToken()->authenticate()); exit;
 		try {
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}
		} catch (\Exception $e) {
			return false;
		}

		if (is_null($user->jwt)) // already logout
			return false;
		else {
			$payload = JWTAuth::parseToken()->getPayload();
			if (User::where('id', $user->id)->where('jwt', $jwt)->doesntExist())
				return false;
		}
		
 		// split the token
		$tokenParts = explode('.', $jwt);
		$header = base64_decode($tokenParts[0]);
		$payload = base64_decode($tokenParts[1]);
		$signatureProvided = $tokenParts[2];

		// check the expiration time - note this will cause an error if there is no 'exp' claim in the token
		$expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
		$tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);
		if ($tokenExpired) return false;

		// build a signature based on the header and payload using the secret
		$base64UrlHeader = base64UrlEncode($header);
		$base64UrlPayload = base64UrlEncode($payload);
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, config('bookdose.jwt.secret'), true);
		$base64UrlSignature = base64UrlEncode($signature);

		// verify it matches the signature provided in the token
		$signatureValid = ($base64UrlSignature === $signatureProvided);
		if (!$signatureValid) return false;

    	$base64Url = explode('.', $jwt)[1] ?? '';
    	$base64 = str_replace('-', '+', $base64Url);
    	$base64 = str_replace('_', '/', $base64);
    	$payload = json_decode(base64_decode($base64));
    	// $payload = (array) json_decode(base64_decode($base64));
    	// if (!empty($payload['id'] && !empty($payload['email']))) {
    	if ($payload) {
    		return $payload;
    	}
    	else {
    		return false;
    	}
 	}

}
