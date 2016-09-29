<?php
/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/26/2016
 * Time: 2:07 PM
 */
session_start();
include_once "config/site_config.php";
include 'system/upload_class.php';
include "../client/system/api_caller.php";

$response['success'] = false;

// Check for the upload thing
if( !empty( $_FILES['image_file']['tmp_name'])){


    if(! empty($_POST['image_caption'])){

        $image_caption = $_POST['image_caption'];
    }

    //proceed with the upload stuff
    $uploadOb = new Upload();
    $uploaded = $uploadOb->imageUpload();

    if( ! $uploaded['error']){

        //get the file info here
        $file_name = $uploaded['filename'];
        $file_ext = $uploaded['extension'];
        $file_size = $uploaded['size'];

        //now make the api call her to save into the database.. thanks
        $apiCaller = new Api_Caller('1', 'asdfghjkl', FORTOS_API_URL);
        $saved = $apiCaller->sendRequest([
            'controller' => 'image',
            'action' => 'save',
            'file_name' => $file_name,
            'file_ext' => $file_ext,
            'file_size' => $file_size,
            'caption' => isset($image_caption) ? $image_caption : ''

        ]);

        if($saved['photo_id'] != 0){

            $response['data'] = [
                'image_id' => $saved['photo_id'],
                'image_name' => $file_name . $file_ext,
                'size' => $file_size,
                'caption' => isset($image_caption) ? $image_caption : '',
                'created_on' => date("Y-m-d H:i:s")
            ];

            $response['success'] = true;

        }else{
            //Delete the uploaded file
            $file_loc = "../photos/".$file_name.$file_ext;
            file_exists($file_loc) ? unlink($file_loc) : null;
            $response['error_msg'] = "Error! Could Not Save Your Photo";
        }

    }else{
        $response['error_msg'] = $uploaded['error'];
    }

}else { $response['error_msg'] = "Error! Please Select a file to upload"; }

if(isset($_GET['f'])){
    $_SESSION = $response;
    header('location:../client/index.php'); exit();
}

//get the image data from the database

echo json_encode($response);
