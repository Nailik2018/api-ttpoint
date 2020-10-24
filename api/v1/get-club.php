<?php

require_once "../../collections/DataBase.php";
require_once "../../collections/helpfunctions/elo-to-classment.php";

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

if(htmlspecialchars($_GET['clubname'])){

    $currentDay = (int)date('j');

    // default suche wenn kein paramter in der ULR mitgegeben wird. Sonst wir dieser Wert überschrieben
    $searchMonth = (int)date('n');
    $searchYear = (int)date('Y');

    // @todo auslagern in eine Funktion
    if($currentDay <= 10){
        switch ($searchMonth){
            case 1:
                $searchMonth = 12;
                $searchYear = $searchYear - 1;
                break;
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 12:
                $searchMonth = $searchMonth -1;
                break;
        }
    }

    $clubname = htmlspecialchars($_GET['clubname']);

    if(htmlspecialchars($_GET['month'])&& htmlspecialchars($_GET['year'])){
        $searchYear = intval(htmlspecialchars($_GET['year']));
        $searchMonth = intval(htmlspecialchars($_GET['month']));
    }

    $db = new DataBase();
    $db->connection();

    $CLUBNAME = 'CLUBNAME';

    $clubInformation[$CLUBNAME] = $clubname;

    if(is_int($searchYear) && is_int($searchMonth)){
        $SEARCHMONTH = 'SEARCHMONTH';
        $SEARCHYEAR = 'SEARCHYEAR';
        $clubInformation[$SEARCHMONTH] = $searchMonth;
        $clubInformation[$SEARCHYEAR] = $searchYear;
        $sqlStatement = "SELECT * FROM club 
                            INNER JOIN association ON club.associationID = association.id 
                            INNER JOIN player ON player.clubID = club.id 
                            INNER JOIN elos_archiv ON elos_archiv.licenceNr = player.licenceNr 
                            INNER JOIN months ON months.id = elos_archiv.monthID INNER JOIN gender ON gender.id = player.genderID 
                            WHERE club.clubname = :CLUBNAME AND elos_archiv.monthID = :SEARCHMONTH AND elos_archiv.year = :SEARCHYEAR ORDER BY player.lastname ASC";
    }else{
        $sqlStatement = "SELECT * FROM club 
                            INNER JOIN association ON club.associationID = association.id 
                            INNER JOIN player ON player.clubID = club.id 
                            INNER JOIN elos_archiv ON elos_archiv.licenceNr = player.licenceNr 
                            INNER JOIN months ON months.id = elos_archiv.monthID 
                            INNER JOIN gender ON gender.id = player.genderID 
                            WHERE club.clubname = :CLUBNAME AND elos_archiv.monthID = :SEARCHMONTH AND elos_archiv.year = :SEARCHYEAR ORDER BY player.lastname ASC";
    }

    $clubPlayers = $db->sqlPreparedStatement($sqlStatement, $clubInformation);

    $json = json_decode($clubPlayers);

    if(count($json) >= 1){

        $clubJson = [];

        $clubJson['associationID'] = $json[0]->associationID;
        $clubJson['association_fullname'] = $json[0]->association_fullname;
        $clubJson['clubID'] = $json[0]->clubID;
        $clubJson['clubname'] = $json[0]->clubname;
        $clubJson['monthID'] = $json[0]->monthID;
        $clubJson['month'] = $json[0]->month;
        $clubJson['year'] = $json[0]->year;
        $clubJson['countedPlayers'] = count($json);

        $i = 0;

        foreach($json as $player) {

            $clubJson['players'][$i]['firstname'] = $player->firstname;
            $clubJson['players'][$i]['lastname'] = $player->lastname;
            $clubJson['players'][$i]['licenceNr'] = $player->licenceNr;
            $clubJson['players'][$i]['genderID'] = $player->genderID;
            $clubJson['players'][$i]['gender'] = $player->gender;
            $clubJson['players'][$i]['currentElo'] = $player->elo;
            $clubJson['players'][$i]['currentClassment'] = getClassment($player->elo);

            $i++;
        }

        $json = json_encode($clubJson);
        print_r($json);
    }else{
        //@todo bei keinem spieler
    }
}else{
    echo "Die Suche ist ungültig";
}

?>