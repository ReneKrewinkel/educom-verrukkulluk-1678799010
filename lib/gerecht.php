<?php

require_once("lib/user.php");
require_once("lib/type_keuken.php");
require_once("lib/ingredient.php");
require_once("lib/gerecht_info.php");

class gerecht {

    private $connection;
    private $usr;
    private $tk;
    private $ing;
    private $gerinfo;

    public function __construct($connection) {
        $this->connection = $connection;
        $this->usr = new user($connection);
        $this->tk = new type_keuken($connection);
        $this->ing = new ingredient($connection);
        $this->gerinfo = new gerecht_info($connection);
    }

    private function ophalenUser($user_id) {
        $data_usr = $this->usr->selecteerUser($user_id);
        return($data_usr);
    }

    private function ophalenTypeKeuken($type_keuken_id) {
        $data_tk = $this->tk->selecteerTypeKeuken($type_keuken_id);
        return($data_tk);
    }

    private function ophalenIngredient($gerecht_id) {
        $data_ing = $this->ing->selecteerIngredient($gerecht_id);
        return($data_ing);
    }

    private function ophalenGerechtInfo($gerecht_info_id) {
        $data_gerinfo = $this->gerinfo->selecteerGerechtInfo($gerecht_info_id);
        return($data_gerinfo);
    }

    private function ophalenWaardering($gerecht_id) {
        return $this->gerinfo->selecteerGerechtInfo($gerecht_id, 'W');
    }

    private function ophalenFavoriet($gerecht_id) {
        return $this->gerinfo->selecteerGerechtInfo($gerecht_id, 'F');
    }

    private function ophalenOpmerking($gerecht_id) {
        return $this->gerinfo->selecteerGerechtInfo($gerecht_id, 'O');
    }

    private function ophalenBereiding($gerecht_id) {
        return $this->gerinfo->selecteerGerechtInfo($gerecht_id, 'B');
    }



//ophalen user
    public function selecteerGerecht($gerecht_id) {
        $gerecht_data = [
            "id" => "",
            "keuken" => [],
            "type" => [],
            "user" => [],
            "datum_toegevoegd" => "",
            "titel" => "",
            "korte_omschrijving" => "",
            "lange_omschrijving" => "",
            "afbeelding" => "",
            "ingredienten" => [],
            "favoriet" => [],
            "waardering" => [],
            "bereidingswijze" => [],
            "opmerkingen" => [],
            "prijs_gerecht" => "",
            "calorieen" => "",
            "gemiddelde_waardering" => ""
        ];


        $sql = "select * from gerecht where gerecht_id = $gerecht_id";
        $result = mysqli_query($this->connection, $sql);

        $kaalGerechtData = mysqli_fetch_array($result, MYSQLI_ASSOC);

        $gerecht_data["id"] = $kaalGerechtData["gerecht_id"];
        $gerecht_data["keuken"] = $this->ophalenTypeKeuken($kaalGerechtData["keuken_id"]);
        $gerecht_data["type"] = $this->ophalenTypeKeuken($kaalGerechtData["type_id"]);
        $gerecht_data["user"] = $this->ophalenUser($kaalGerechtData["user_id"]);
        $gerecht_data["datum_toegevoegd"] = $kaalGerechtData["datum_toegevoegd"];
        $gerecht_data["titel"] = $kaalGerechtData["titel"];
        $gerecht_data["korte_omschrijving"] = $kaalGerechtData["korte_omschrijving"];
        $gerecht_data["lange_omschrijving"] = $kaalGerechtData["lange_omschrijving"];
        $gerecht_data["ingredienten"] = $this->ophalenIngredient($kaalGerechtData["gerecht_id"]);
        $gerecht_data["favoriet"] = $this->ophalenFavoriet($kaalGerechtData["gerecht_id"]);
        $gerecht_data["waardering"] = $this->ophalenWaardering($kaalGerechtData["gerecht_id"]);
        $gerecht_data["bereidingswijze"] = $this->ophalenBereiding($kaalGerechtData["gerecht_id"]);
        $gerecht_data["opmerkingen"] = $this->ophalenOpmerking($kaalGerechtData["gerecht_id"]);
        //$gerecht_data["totaalprijs"] = $this->berekenPrijs($gerecht_data["ingredienten"]);
        //$gerecht_data["calorieen"] = $this->berekenCalorieen($gerecht_data["ingredienten"]);
        //$gerecht_data["gemiddelde_waardering"] = $this->berekenGemiddeldeWaardering($gerecht_data["waardering"]);

        return $gerecht_data;
    }
} 

        



        

/*
//berekenen calorieen
    private function berekenCalorieen($gerecht_id) {
        $calorieen_gerecht = [];

        $sql = "select * from ingredient where gerecht_id = $gerecht_id)";
        $result = mysqli_query($this->connection, $sql);
        $ingredient = mysqli_fetch_array($result, MYSQLI_ASSOC);


        foreach($ingredients as $ingredient) {
            $calorieen_gerecht = [(($ingredient["aantal"] / $ingredient["verpaking"]) * $ingredient["calorieen"])];
        }
        return array_sum($calorieen_gerecht);
    }


//berekenen prijs
    public function berekenPrijs($gerecht_id) {
        $prijs_gerecht = [];

        $sql = "select * from gerecht where gerecht_id = $gerecht_id)";
        $result = mysqli_query($this->connection, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $prijs_gerecht= (($ingredient["aantal"] / $ingredient["verpaking"]) * $ingredient["prijs"]);
        }
        return array_sum($prijs_gerecht); 
    }


//ophalen waardering
    public function selecteerGerechtInfo($gerecht_info_id, $record_type) {
        $sql = "select * from gerecht_info where gerecht_id = $gerecht_id and record_type = 'W'";
        echo $sql;
        $result = mysqli_query($this->connection, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $gerecht_id = $row['gerecht_id'];
            $gerecht = $this->ophalenWaardering($gerecht_info_id);

            $waardering[] = [
                'gerecht__info_id' => $row['gerecht_info_id'],
                'gerecht_id' => $row['gerecht_id'],
                'record_type' => $row['record_type'],
                'nummeriekveld' => $row['nummeriekveld'],
            ];
        }
        return($waardering);
    }



//ophalen bereidingswijze stappen
    public function selecteerBereiding($gerecht_id, $record_type) {
        $sql = "select * from gerecht_info where gerecht_id = $gerecht_id and record_type = 'B'";
        $result = mysqli_query($this->connection, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $gerecht_id = $row['gerecht_id'];
            $gerecht = $this->ophalenBereiding($gerecht_id);

        $bereiding[] = [
            'gerecht__info_id' => $row['gerecht_info_id'],
            'gerecht_id' => $row['gerecht_id'],
            'record_type' => $row['record_type'],
            'nummeriekveld' => $gerecht_info['nummeriekveld'],
            'tekstveld' => $gerecht_info['tekstveld'],
        ];
        return($bereiding);
        }
    }
}
//ophalen opmerkingen
//ophalen keuken
//ophalen type
//maak favoriet 
*/