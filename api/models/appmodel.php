<?php

/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/25/2016
 * Time: 11:15 AM
 */
class AppModel extends Database
{
    function __construct(){
        parent::__construct();
    }

    public function getApplication($app_id){

        $query = "SELECT * FROM applications
        WHERE application_id = ? ORDER BY application_id ASC LIMIT 1";

        $params = [
            'i',
            &$app_id
        ];

        return $this->processPrepQuery($query, $params, false);

    }


}