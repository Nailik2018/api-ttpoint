<?php

require_once "../../collections/DataBase.php";

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$db = new DataBase();
$db->connection();

$currentDay = (int)date('j');
$searchMonth = (int)date('n');
$searchYear = (int)date('Y');

$gender = '';

// @todo auslagern in eine Funktion
if ($currentDay <= 10) {
    switch ($searchMonth) {
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
            $searchMonth = $searchMonth - 1;
            break;
    }
}

if ($_GET['month']) {
    $searchMonth = (int)htmlspecialchars($_GET['month']);

}

if ($_GET['year']) {
    $searchYear = (int)htmlspecialchars($_GET['year']);
}

if ($_GET['gender']) {
    $gender = (string)htmlspecialchars($_GET['gender']);
}

if($_GET['association']){
    $association = (string)htmlspecialchars($_GET['association']);
}

if (is_int($searchYear) && is_int($searchMonth)) {
    $SEARCHMONTH = 'SEARCHMONTH';
    $SEARCHYEAR = 'SEARCHYEAR';
    $rankingInformation[$SEARCHMONTH] = $searchMonth;
    $rankingInformation[$SEARCHYEAR] = $searchYear;

    $gender = $_GET['gender'];
    $gender = htmlentities($gender);

    if (htmlspecialchars($gender) && htmlspecialchars($association)) {
        $GENDER = 'GENDER';
        $rankingInformation[$GENDER] = $gender;

        $ASSOCIATION = 'ASSOCIATION';
        $rankingInformation[$ASSOCIATION] = $association;

//        $sqlStatement  = "SELECT * FROM elos_archiv INNER JOIN player ON player.licenceNr = elos_archiv.licenceNr INNER JOIN gender ON gender.id = player.genderID INNER JOIN months ON months.id = elos_archiv.monthID WHERE gender.gender = :GENDER AND months.id = :SEARCHMONTH AND elos_archiv.year = :SEARCHYEAR ORDER BY elos_archiv.elo DESC";
        $sqlStatement = "SELECT * FROM elos_archiv 
                            INNER JOIN player ON player.licenceNr = elos_archiv.licenceNr 
                            INNER JOIN gender ON gender.id = player.genderID 
                            INNER JOIN months ON months.id = elos_archiv.monthID 
                            INNER JOIN club ON club.id = elos_archiv.clubID 
                            INNER JOIN association ON association.id = club.associationID
                            WHERE gender.gender = :GENDER AND months.id = :SEARCHMONTH AND elos_archiv.year = :SEARCHYEAR AND association.association =:ASSOCIATION ORDER BY elos_archiv.elo DESC";
    } else {

        $ASSOCIATION = 'ASSOCIATION';
        $rankingInformation[$ASSOCIATION] = $association;
//        $sqlStatement = "SELECT * FROM elos_archiv INNER JOIN player ON player.licenceNr = elos_archiv.licenceNr INNER JOIN gender ON gender.id = player.genderID INNER JOIN months ON months.id = elos_archiv.monthID WHERE months.id = :SEARCHMONTH AND elos_archiv.year = :SEARCHYEAR ORDER BY elos_archiv.elo DESC";
        $sqlStatement = "SELECT * FROM elos_archiv 
                            INNER JOIN player ON player.licenceNr = elos_archiv.licenceNr 
                            INNER JOIN gender ON gender.id = player.genderID 
                            INNER JOIN months ON months.id = elos_archiv.monthID 
                            INNER JOIN club ON club.id = elos_archiv.clubID
                            INNER JOIN association ON association.id = club.associationID
                            WHERE months.id = :SEARCHMONTH AND elos_archiv.year = :SEARCHYEAR AND  association.association =:ASSOCIATION ORDER BY elos_archiv.elo DESC";
    }
}

$playerRanking = $db->sqlPreparedStatement($sqlStatement, $rankingInformation);
$json = json_decode($playerRanking);
$rankingNumber = 1;
$rankingArray = [];

foreach ($json as $player) {
    $p = [];
    $p['ranking'] = $rankingNumber;
    $p['licenceNr'] = $player->licenceNr;
    $p['firstname'] = $player->firstname;
    $p['lastname'] = $player->lastname;
    $p['clubID'] = $player->clubID;
    $p['clubname'] = $player->clubname;
    $p['monthID'] = $player->monthID;
    $p['month'] = $player->month;
    $p['elo'] = $player->elo;
    $p['genderID'] = $player->genderID;
    $p['gender'] = $player->gender;
    $p['associationID'] = $player->associationID;
    $p['association'] = $player->association;
    $rankingNumber++;
    array_push($rankingArray, $p);
}
$json = json_encode($rankingArray);
print_r($json);