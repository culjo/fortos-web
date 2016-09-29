<?php

/**
 * Created by Olad lekan.
 * User: Singteractive
 * Date: 2/26/2016
 * Time: 12:42 AM
 * This class make the uploading of file on singteractive easy and in one place
 */

class Upload
{
//    private $UPLOAD_DIRECTORY = "uploads/";

    const FILE_KEY = "image_file";

    private $DIRECTORY;

    private $myFile;
    public $response;


    function __construct(){
        $this->response = [];
    }

    /*public function profileImageUpload(){
        $this->DIRECTORY = "photo/";
        return $this->imageUpload();
    }*/


    public function imageUpload(){
        $image_ext = array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'bmp' => 'image/bmp',
        );
        $this->DIRECTORY = "../photos/";
        return $this->uploadFile(8000000, $image_ext);//7mb
    }

    private function uploadFile($maxSize, $allowedExtTypes = []){

//        var_dump($_FILES[Upload::FILE_KEY]);

        if(! empty($_FILES[Upload::FILE_KEY])){

            $this->myFile = $_FILES[Upload::FILE_KEY];

            if($this->myFile["error"] !== UPLOAD_ERR_OK){
                $this->response["error"] = "An error has occurred while uploading your file";
                return $this->response;
            }

            //Check the size of the file that is uploaded
            if($this->myFile["size"] <= $maxSize){

                // DO NOT TRUST $_FILES['uploaded_file']['mime'] VALUE !!
                // Check MIME Type by yourself.
                $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                $origFileMimeType = $fileInfo->file($this->myFile['tmp_name']);

                //echo $origFileExt . " :: File extension :: ";

                $extension = array_search($origFileMimeType, $allowedExtTypes, true);//returns .mp3 or .jpg

                // Note: A fallback for if an mp3 is not identified but seen as octet-stream
                // todo: Fix this flaw of mp3 and octet-stream
                if($extension == false){
                    //Check if the file is an application/octet-stream
                    // if octet-stream then check the extension
                    if($origFileMimeType == 'application/octet-stream'){
                        $nameExt = pathinfo($this->myFile['name']);
                        if($nameExt["extension"] == 'mp3'){
                            //echo $nameExt['extension'];
                            $extension = 'mp3';
                        }
                    }
                }

                if($extension !==  false){

                    //ensure a safe filename
                    $origFilename = preg_replace("/[^A-Z0-9._-]/i", "_", $this->myFile["name"]);
                    $hashFname = sha1_file($this->myFile["tmp_name"]);
                    $hashFilename = $hashFname . "." . $extension;


                    //Dont overwrite an existing file
                    $i = 0;
                    $parts = pathinfo($hashFilename);
                    while(file_exists($this->DIRECTORY . $hashFilename)){
                        $i++;
                        $hashFilename = $parts["filename"] . "-" . $i . "." . $parts["extension"];
                    }
                    //echo $this->myFile["tmp_name"] ."<br/>";
                    //preserve file from temp directory
                    $success = move_uploaded_file($this->myFile["tmp_name"], $this->DIRECTORY . $hashFilename);

                    if($success){
                        //go return correct
                        $this->response["error"] = false;
                        $this->response["filename"] = $hashFname;
                        $this->response["extension"] = "." . $extension;
                        $this->response['size'] = $this->myFile["size"];

                        chmod($this->DIRECTORY . $hashFilename, 0644);// file permission to read/write

                    }else{ $this->response["error"] = "Unable to save file"; }

                }else{
                    $this->response["error"] = "Invalid file format, Please Upload an " .
                        implode(",", array_keys($allowedExtTypes)) . " File types";
                }

            }else{ $this->response["error"] = "File is too large, must not exceed max size"; }

        }else{ $this->response["error"] = "No File was Uploaded"; }

        return $this->response;

    }

}