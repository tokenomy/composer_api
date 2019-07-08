<?php

namespace Tokenomy;
class Tokex
{
	$tokenomy_key = ""; //api key
	$tokenomy_secret = ""; //secret key
	$public_api_url = "https://exchange.tokenomy.com/api/";

	function tokenomy_query($method, array $req = array()) {
        // API settings
        $key = $tokenomy_key; // your API-key
        $secret = $tokenomy_secret; // your Secret-key
 
        $req['method'] = $method;
        $req['nonce'] = time();
       
        // generate the POST data string
        $post_data = http_build_query($req, '', '&');
 
        $sign = hash_hmac('sha512', $post_data, $secret);
 
        // generate the extra headers
        $headers = array(
			'Sign: '.$sign,
			'Key: '.$key,
        );
 
        // our curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; TOKENOMY PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
        }
        curl_setopt($ch, CURLOPT_URL, 'https://exchange.tokenomy.com/tapi/');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  
        // run the query
        $res = curl_exec($ch);
        if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
        $dec = json_decode($res, true);
        if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists: '.$res);
        
        curl_close($ch);
        $ch = null;
        return $dec;
	}

	public static function private_init($api_key, $secret_key){
		$tokenomy_key = $api_key;
		$tokenomy_secret = $secret_key;
	}

	public static function get_info(){
		$result = tokenomy_query("getInfo");
		return $result;
	}

	
	//PUBLIC API
	function public_query($url) {       
        // our curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; TOKENOMY PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  
        // run the query
        $res = curl_exec($ch);
        if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
        $dec = json_decode($res, true);
        if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists: '.$res);
        
        curl_close($ch);
        $ch = null;
        return $dec;
	}

	public static function summaries(){
		$url = $public_api_url + "summaries";
		$result = public_query($url);

		return $result;
	}

}

?>