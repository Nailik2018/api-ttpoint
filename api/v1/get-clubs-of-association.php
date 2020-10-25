<?php

require_once "../../collections/DataBase.php";

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

if(htmlspecialchars($_GET['association'])){

    $db = new DataBase();
    $db->connection();

    $getAssociation = htmlspecialchars($_GET['association']);
    $ASSOCIATION = 'ASSOCIATION';

    $associationInformation[$ASSOCIATION] = $getAssociation;

    $currentDay = (int)date('j');
    $currentMonth = (int)date('n');
    $currentYear = (int)date('Y');

    if($currentDay <= 10){
        switch ($currentMonth){
            case 1:
                $currentMonth = 12;
                $searchYear = $currentYear - 1;
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
                $currentMonth = $currentMonth -1;
                break;
        }
    }

    $CURRENTMONTH = 'CURRENTMONTH';
    $CURRENTYEAR = 'CURRENTYEAR';

    $associationInformation[$CURRENTMONTH] = $currentMonth;
    $associationInformation[$CURRENTYEAR] = $currentYear;

    $sqlStatement = "SELECT club.id, club.clubname, club.id, association.association, elos_archiv.elo AS highestEloOfClubPlayer, COUNT(elos_archiv.clubID) AS licencedPlayer FROM club
                        INNER JOIN association ON association.id = club.associationID
                        INNER JOIN elos_archiv ON elos_archiv.clubID = club.id                        
                        WHERE association.association = :ASSOCIATION AND elos_archiv.year = :CURRENTYEAR AND elos_archiv.monthID = :CURRENTMONTH GROUP BY club.clubname ASC";

    print_r($db->sqlPreparedStatement($sqlStatement, $associationInformation));
}else{
    echo "Die Suche ist ungültig";
}

?>