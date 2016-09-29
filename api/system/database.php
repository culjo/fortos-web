<?php
/**
 * Created by PhpStorm.
 * User: WeaverBird
 * Date: 9/25/2016
 * Time: 4:05 AM
 */

class Database {

    protected $db_connection;

    public function __construct(){
        $this->connect();
    }

    // Connecting to database
    private function connect() {

        $live_server =  dirname(__DIR__). "/config/config.php";
        require_once $live_server; // very important part of the system please...

        // Connecting to mysql database
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

        if($this->db_connection) {

            // return database handler
            return $this->db_connection;

        }else{
            //conn->mysqli_connect_error();
            echo mysqli_connect_error();
            return false;
        }

    }

    private function getResult( $Statement ) {

        $RESULT = array();
        $Statement->store_result();

        for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
            $Metadata = $Statement->result_metadata();
            $PARAMS = array();
            while ( $Field = $Metadata->fetch_field() ) {
                $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
            }
            call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
            $Statement->fetch();
        }

        return $RESULT;
    }


    protected function processQuery($query){

        $response = false;
        $stmt = $this->db_connection->prepare($query);

        if($stmt){
            //Then bind the parameters
            //$stmt->bind_param("i", 1);
            if($stmt->execute()){
                //$response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $response = $this->getResult($stmt);

            }else{ echo $stmt->error; return false; }

            $stmt->close();

        }else{
            echo $this->db_connection->error;
        }
        return $response;
    }


    protected function processRowQuery($query){
        $response = null;
        $stmt = $this->db_connection->prepare($query);

        if($stmt){
            //Then bind the parameters
            //$stmt->bind_param("i", 1);
            if($stmt->execute()){

//                $response = $stmt->get_result()->fetch_assoc();
                $response = $this->getResult($stmt);
                $response = $response[0];

            }else{ echo $stmt->error; }
            $stmt->close();
        }else{
            echo $this->db_connection->error;
        }
        return $response;

    }

    /**
     * This method processes a prepared statement
     * @param string $query The query string that is to be processed using a prepared statement
     * @param array $params The parameters values should be passed as reference to the params array,
     * e.g: $params = array( &$params_types, &$param1, &$param2 [, &$param...] )
     * @param bool $multi_rows if true it will be processed for multiple rows, if false it
     * will be processed for single row query.
     * @return null | array returns an array of data or empty data if no data is available
     * it also returns null if the query failed.
     */
    protected function processPrepQuery($query, &$params, $multi_rows = true){

        $data = null;
        $stmt = $this->db_connection->prepare($query);
        if($stmt){

            $bound = call_user_func_array(array(&$stmt, "bind_param"), $params);
            if($bound){
                if($stmt->execute()){

                    if($multi_rows){//multi_rows
                        //$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $data = $this->getResult($stmt);
                    }else{
                        //$data = $stmt->get_result()->fetch_assoc();
                        $data = $this->getResult($stmt);
                        if( !empty($data) ) $data = $data[0];
                    }

                }else{
                    echo $stmt->error;
                }
            }

        }else{ $this->db_connection->error; }
        return $data;

    }


    /**
     * Will perform an insert query for data
     * @param $query
     * @param $params
     * @return int the inserted ID of the last inserted row or 0 if query was not successful
     */
    protected function processPrepInsertQuery($query, &$params){

        $response = 0;
        $stmt = $this->db_connection->prepare($query);
        if($stmt){
            $bound = call_user_func_array(array(&$stmt, "bind_param"), $params);
            if($bound){
                if($stmt->execute()){
                    if($stmt->affected_rows > 0){
                        $response = $this->db_connection->insert_id;
                    }
                }else{ echo $stmt->error; }
            }

        }else{ echo $this->db_connection->error; }
        return $response;

    }


    /**
     * @param $query
     * @param $params
     * @return boolean true if update was successful or false if not
     */
    protected function processPrepUpdateQuery($query, &$params){

        $response = false;
        $stmt = $this->db_connection->prepare($query);
        if($stmt){
            $bound = call_user_func_array(array(&$stmt, "bind_param"), $params);
            if($bound){
                if($stmt->execute()){
                    if($stmt->affected_rows > 0){
                        $response = true;
                    }
                }else{ echo $stmt->error; }
            }

        }else{ $this->db_connection->error; }
        return $response;

    }

    /**
     * @param $query
     * @param $params
     * @return int  number of rows if the delete was successful,
     * returns affeted rows if the delete was performed and
     * -1  if the delete was not performed or unsuccessful
     */
    protected function processPrepDeleteQuery($query, &$params){

        $response = -1;
        $stmt = $this->db_connection->prepare($query);
        if($stmt){
            $bound = call_user_func_array(array(&$stmt, "bind_param"), $params);
            if($bound){
                if($stmt->execute()){

                    $response = $stmt->affected_rows;

                }else{ echo $stmt->error; }
            }

        }else{ $this->db_connection->error; }
        return $response;

    }


}
