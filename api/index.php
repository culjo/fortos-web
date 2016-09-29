<?php
/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/25/2016
 * Time: 3:23 AM
 */

define('DATA_PATH', realpath(dirname(__FILE__) . '/data'));
define('CONTROLLERS_DIR', 'controllers/');

// Connect to the database here
include_once "config/config.php";
include_once "system/database.php";

// include our models
include_once "models/imagemodel.php";
include_once "models/appmodel.php";
// todo : auto load classes

$result = [];

// ready to catch error
try{
    //var_dump($_REQUEST);

    // get the encrypted request
    $encrypted_request = $_REQUEST['encrypted_request'];

    // Get the provided app id
    $app_id = $_REQUEST['app_id'];

    //check if the application id exist in our database
    $appModel = new AppModel();
    $app_details = $appModel->getApplication($app_id); // try an get the app details
    if(empty($app_details)) throw new Exception('Application does not exist');

    // decrypt the request
    $params = json_decode(trim(
        mcrypt_decrypt(MCRYPT_RIJNDAEL_256,
            $app_details['application_key'],
            base64_decode($encrypted_request),
            MCRYPT_MODE_ECB)));

    // check the validity of the request
    if( $params == false || isset($params->controller) == false || isset($params->action) == false){
        throw new Exception('Request is not a Valid one');
    }

    // get all of the parameters in the REQUEST : POST / GET e.t.c
    $params = (array) $params;

    //get the controller
    ! empty($params['controller']) ? $controller = strtolower($params['controller']) : $controller = "";

    // get the action and format it correctly
    ! empty($params['action']) ? $action = strtolower($params['action']) : $action = "";

    //check if the controller exist
    if(file_exists(CONTROLLERS_DIR . $controller . '.php')){
        include_once CONTROLLERS_DIR . $controller .".php";
    }else{
        throw new Exception('Controller is invalid');
    }

    //initialize the controller and pass the parameters form the request
    $controller = ucfirst($controller); // class name us camel case
    $controller = new $controller($params);

    // check if the action / method exists in the controller.
    if(method_exists($controller, $action) === false){
        throw new Exception('Action Call is invalid');
    }

    // Execute the action to be performed
    $result['data'] = $controller->$action();
    $result['success'] = true;

}catch (Exception $e){
    $result['success'] = false;
    $result['error_msg'] = $e->getMessage();
}
// $result['params'] = $params;
// echo the result
echo json_encode($result);
//exit();