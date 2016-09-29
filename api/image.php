<?php
/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/27/2016
 * Time: 6:26 PM
 */
include_once "config/site_config.php";

if(!empty($_REQUEST['app_id']) && !empty($_REQUEST['app_key']) && !empty($_REQUEST['action'])){

    $app_id = $_REQUEST['app_id'];
    $app_key = $_REQUEST['app_key'];

    $params = [
        'controller' => 'image',
        'action' => $_REQUEST['action']
    ];
    
    if($_REQUEST['action'] == 'delete'){
    	$params['image_id'] = $_REQUEST['image_id'];
    }

    if(isset($_REQUEST['fpos'], $_REQUEST['thold'])){
        $params['from'] = $_REQUEST['fpos'];
        $params['count'] = $_REQUEST['thold'];
    }

    echo json_encode(sendRequest($params, $app_id, $app_key));

}else{
    echo json_encode(['success' => false]);
}

function sendRequest($request_params, $app_id, $app_key){

    // encrypt the request parameters
    $encrypted_request = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $app_key,
        json_encode($request_params), MCRYPT_MODE_ECB ));

    // create the params array
    $params = array();
    $params['encrypted_request'] = $encrypted_request;
    $params['app_id'] = $app_id;

    try {
        // initialize and setup the curl handler
        $curl_handler = curl_init();
        curl_setopt($curl_handler, CURLOPT_URL, FORTOS_API_URL);
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
        return $result;

    }catch (Exception $e){ echo $e->getMessage(); }

}
