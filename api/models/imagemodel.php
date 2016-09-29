<?php

/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/25/2016
 * Time: 4:20 AM
 */

class ImageModel extends Database
{
    public $image_id, $app_id, $file_name, $extension, $caption, $size, $created_on;

    public function __construct(){
        parent::__construct();
    }

    public function getImages($from = 0, $count = 0){

        // $this->processPrepDeleteQuery();
        $query = "SELECT *, CONCAT(file_name, extension) AS image_name
        FROM images ORDER BY created_on DESC";

        if($count > 0){
            $query .= " LIMIT {$from}, {$count}";
        }

        return $this->processQuery($query);

    }

    public function getImage(){

        $query = "SELECT *, CONCAT(file_name, extension) AS image_name
        FROM images WHERE image_id = ? LIMIT 1";
        $params = [
            'i',
            &$this->image_id
        ];
        return $this->processPrepQuery($query, $params, false);

    }

    public function saveImage(){

        $query = "INSERT INTO images
        (file_name, extension, caption, size, updated_on)
        VALUES (?, ?, ?, ?, NOW())";

        $params = [
            'ssss',
            &$this->file_name,
            &$this->extension,
            &$this->caption,
            &$this->size,
        ];

        $res['photo_id'] = $this->processPrepInsertQuery($query, $params);
        return $res;
    }

    public function deleteImage(){

            $query = "DELETE FROM images WHERE image_id = ?";
            $params = [
                'i',
                &$this->image_id
            ];

            $res['deleted'] = $this->processPrepDeleteQuery($query, $params);
        return $res;

    }

}