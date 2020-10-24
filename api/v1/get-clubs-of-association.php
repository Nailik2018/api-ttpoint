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

    $sqlStatement = "SELECT club.id, club.clubname, club.id, association.association FROM club INNER JOIN association ON association.id = club.associationID WHERE association.association = :ASSOCIATION ORDER BY club.clubname ASC";

    print_r($db->sqlPreparedStatement($sqlStatement, $associationInformation));
}else{
    echo "Die Suche ist ungültig";
}

?>