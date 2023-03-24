<?php
require_once("lib/user.php");

class gerecht_info {

    private $connection;
    private $usr;

    public function __construct($connection) {
        $this->connection = $connection;
        $this->usr = new user($connection);
    }

    private function ophalenUser($user_id) {
        $data_usr = $this->usr->selecteerUser($user_id);
        return($data_usr);
    }

// user ophalen bij opmerking en favoriet    
    public function selecteerGerechtInfo($gerecht_id, $record_type) {
        $sql = "select * from gerecht_info where gerecht_id = $gerecht_id and record_type = '$record_type'";
        //echo $sql;
        $result = mysqli_query($this->connection, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            if ($record_type == 'O' || $record_type == 'F') { 
                //var_dump($row);
                $user_id = $row['user_id'];
                //echo $user_id;
                $user = $this->ophalenUser($user_id);
                //var_dump($user);
            
            $gerecht_info[] = [
                'gerecht_info_id' => $row['gerecht_info_id'],
                'record_type' => $row['record_type'],   
                'gerecht_id' => $row['gerecht_id'],
                'datum' => $row['datum'],
                'nummeriekveld' => $row['nummeriekveld'],
                'tekstveld' => $row['tekstveld'],
                'user_name' => $user['user_name'],
                'user_password' => $user['user_password'],
                'email' => $user['email']
            ];
        }
        else {
            $gerecht_info[] = $row;
        }
            
        }
        return($gerecht_info);
    }
    
// aan favorieten toevoegen/verwijderen
 
    /* public function toevoegenFavoriet($gerecht_id, $user_id) {
        if(($record_type == 'F' && $gerecht_id && $user_id) === false) {
            $sql = "INSERT INTO gerecht_info ('record_type', 'gerecht_id', 'user_id')
            VALUES('F', $gerecht_id, $user_id)";
            echo "Toegevoegd aan favorieten";   
        }
    }

    public function verwijderenFavoriet($gerecht_id) {
        if(($record_type == 'F' && $gerecht_id && $user_id) === true) {
        $sql = "DELETE FROM gerecht_info where gerecht_id = $gerecht_id and record_type = 'F'";
        echo "Verwijderd uit favorieten";
        }
    } */


    public function toevoegenFavoriet($gerecht_id, $user_id) {
        $favoriet_data = $this->selectGerechtInfo($gerecht_id, "F" ,$user_id);
        if(count($favoriet_data) > 0) {
            $this->verwijderenFavoriet($favoriet_data[0]["id"]);
            return;
            echo "Verwijderd uit favorieten";
        }
        else {
        $sql = "INSERT INTO gerecht_info ('record_type', 'gerecht_id', 'user_id') 
        VALUES ('F', $gerecht_id, $user_id)";
        $result = mysqli_query($this->connection,$sql);
        echo "Toegevoegd aan favorieten";
        }   
    }

    public function verwijderenFavoriet($gerecht_info_id) {
        $sql = "DELETE FROM gerecht_info WHERE id = $gerecht_info_id";
        $result = mysqli_query($this->connection,$sql);
    }


}








