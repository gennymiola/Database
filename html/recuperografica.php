<?php
require_once('../php/config.php');
$idgrafica = $_POST;
$risultato = [];
//verifico che gli sia arrivato l'id della grafica che sto modificando
if (isset($idgrafica['grafica']) && $idgrafica['grafica'] != "") {
    //aggiorno dati del post
    $recuperodatigrafica = "SELECT * FROM grafica WHERE codice='{$idgrafica['grafica']}'";
    $datigrafica = $db->query($recuperodatigrafica);
    //salvo l'id della grafica
    $risultato = $db->fetchArray($datigrafica);
    $risultato['risultato'] = 'ok';
} else {
    $risultato['risultato'] = 'errore';
}
//trasforma in formato json un array e lo ritorna alla richiesta ajax dove lo gestirò come array
echo json_encode($risultato);
