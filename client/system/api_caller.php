<?php

/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/25/2016
 * Time: 5:15 PM
 */
class Api_Caller
{

    private $_app_id, $_app_key, $_api_url;

    // construct
    public function __construct($app_id, $app_key, $api_url)
    {
        $this->_app_id = $app_id;
        $this->_app_key = $app_key;
        $this->_api_url = $api_url;
    }

    // first encrypts the requests and sends the request to the API Server
    public function sendRequest($request_params){

        // encrypt the request parameters
        $encrypted_request = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_app_key,
            json_encode($request_params), MCRYPT_MODE_ECB ));

        // create the params array
        $params = array();
        $params['encrypted_request'] = $encrypted_request;
        $params['app_id'] = $this->_app_id;

        try {
            // initialize and setup the curl handler
            $curl_handler = curl_init();
            curl_setopt($curl_handler, CURLOPT_URL, $this->_api_url);
            curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_handler, CURLOPT_POST, 1);// count($params));
            curl_setopt($curl_handler, CURLOPT_POSTFIELDS, http_build_query($params));

            // execute the request
            //$result = curl_exec($curl_handler);
            if(! $result = curl_exec($curl_handler)){
                die('Error: "' . curl_error($curl_handler) . '" - Code: ' . curl_errno($curl_handler));
            }

            //close the connection
            curl_close($curl_handler);

            // json_decode the result
            $result = json_decode($result, true);

            // check if were able to json decode the result correctly
            if ($result == false || isset($result['success']) == false) {
                throw new Exception('Request sent was Incorrect');
            }

            // check the there was an error with the request, if so throw an exception
            if (!$result['success']) {
                throw new Exception($result['error_msg']);
            }

            // return the data if request is good
            return $result['data']; /* ** */

        }catch (Exception $e){ echo $e->getMessage(); }

    }


}