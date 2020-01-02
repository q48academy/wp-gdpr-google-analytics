<?php

namespace Q48academy\Gdpr;

class GdprGoogleAnalytics extends Gdpr
{
	public static function run(){

	}

	///
	///  Anonymous Tracking
	///


	public function track(  $data ){


		$endpoint = 'https://www.google-analytics.com/collect';

		$default = [
			'v' => 1,
			'aip' => 1,
			'cid' => rand(1000000,9999999),
			'z' => rand(1000000,9999999),
		];
		$query = array_merge($default,$data);

		$url = $endpoint . '?' . http_build_query($query);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $endpoint,
			CURLOPT_HEADER => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => http_build_query($query),
			CURLOPT_USERAGENT => 'WP-GDPR-Google-Analytics',
		));
		$resp = curl_exec($curl);
		curl_close($curl);

		return $resp;
	}
}