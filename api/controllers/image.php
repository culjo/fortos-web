<?php

/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/25/2016
 * Time: 3:26 AM
 */
class Image
{

    private $_params, $imageModel;

    public function __construct($params)
    {
        $this->_params = $params;
        $this->imageModel = new ImageModel(); // initialize the image model

    }

    public function save(){ // upload new image

//        $this->_params->
        $this->imageModel->app_id;
        $this->imageModel->file_name = $this->_params['file_name'];
        $this->imageModel->extension = $this->_params['file_ext'];
        $this->imageModel->size = $this->_params['file_size'];
        $this->imageModel->caption = $this->_params['caption'];

        return $this->imageModel->saveImage();

    }

    public function get(){ // read all images

        if(isset($this->_params['from'], $this->_params['count'])){

            return $this->imageModel->getImages($this->_params['from'], $this->_params['count']);
        }else{
            return $this->imageModel->getImages();
        }

    }

    public function update(){ // update an image

    }

    public function delete(){ // delete an image

        $this->imageModel->image_id = $this->_params['image_id'];
        // Get the file name of the image to delete
        $image = $this->imageModel->getImage();

        if(! empty($image)){
            $file_location = "../photos/" . $image['image_name'];
            if(file_exists($file_location)){
                unlink($file_location);
            }
            return $this->imageModel->deleteImage();
        }
        return ['deleted', false];

    }

}