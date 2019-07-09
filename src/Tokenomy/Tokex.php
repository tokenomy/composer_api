<?php

namespace Tokenomy;
class Tokex
{
        private $tokenomy_key = ""; //api key
        private $tokenomy_secret = ""; //secret key
        private $public_api_url = "https://exchange.tokenomy.com/api/";

        public function __construct($api_key="", $secret_key=""){
                $this->tokenomy_key = $api_key;
                $this->tokenomy_secret = $secret_key;
        }

        //PRIVATE API
        private function tokenomy_query($method, array $req = array()) {
                // API settings
                $key = $this->tokenomy_key; // your API-key
                $secret = $this->tokenomy_secret; // your Secret-key
         
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

        public function getInfo(){
                $result = $this->tokenomy_query("getInfo");
                return $result;
        }

        public function transHistory(){
                $result = $this->tokenomy_query("transHistory");
                return $result;
        }

        public function trade($params){
                $result = $this->tokenomy_query("trade", $params);
                return $result;
        }

        public function tradeHistory(array $params = array()){
                $result = $this->tokenomy_query("tradeHistory", $params);
                return $result;
        }

        public function openOrders(array $params = array()){
                $result = $this->tokenomy_query("openOrders", $params);
                return $result;
        }

        public function orderHistory(array $params = array()){
                $result = $this->tokenomy_query("orderHistory", $params);
                return $result;
        }

        public function getOrder($params){
                $result = $this->tokenomy_query("getOrder", $params);
                return $result;
        }

        public function cancelOrder($params){

                $result = $this->tokenomy_query("getOrder", $params);
                return $result;
        }

        public function withdrawCoin($params){
                $result = $this->tokenomy_query("withdrawCoin", $params);
                return $result;
        }

        
        //PUBLIC API
        private function public_query($url) {       
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

        public function summaries(){
                $url = $this->public_api_url . "summaries";
                $result = $this->public_query($url);

                return $result;
        }

        public function market_info(){
                $url = $this->public_api_url . "market_info";
                $result = $this->public_query($url);

                return $result;
        }

        public function ticker($pair){
                $url = $this->public_api_url . $pair . "/ticker";
                $result = $this->public_query($url);

                return $result;
        }

        public function trades($pair){
                $url = $this->public_api_url . $pair . "/trades";
                $result = $this->public_query($url);

                return $result;
        }

        public function depth($pair){
                $url = $this->public_api_url . $pair . "/depth";
                $result = $this->public_query($url);

                return $result;
        }

}

?>