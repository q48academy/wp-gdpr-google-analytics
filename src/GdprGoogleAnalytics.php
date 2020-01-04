<?php

namespace Q48academy\Gdpr;

class GdprGoogleAnalytics extends Gdpr
{

	const GDPR_BLOCK_COOKIE_NAME = 'gdpr_ga_block';

	public static function run(){

	}

	///
	///  Anonymous Tracking
	///


	public function track(  $data ){

		if(isset($_COOKIE[self::GDPR_BLOCK_COOKIE_NAME])){
			return false;
		}

		$endpoint = 'https://www.google-analytics.com/collect';

		$default = [
			'v' => 1,
			'aip' => 1,
			//'cid' => rand(1000000,9999999),
			'z' => rand(1000000,9999999),
		];
		$query = array_merge($default,$data);

		// creat daily consistent cid for Visit identification
		if(empty($query['cid'])){
			$cid = date('Y-m-d');
			if(isset($query['uip'])){
				$cid .= $query['uip'];
			}
			if(isset($query['ua'])){
				$cid .= $query['ua'];
			}
			$query['cid'] = md5($cid);
		}


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