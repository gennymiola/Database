<?php
require_once ('../php/config.php');
//passo il testo del commento e l'id del commento e gli applico la funzione escape, così se il testo cotiene un singolo apice la stringa non si interrompe e non genera un errore
$testo = $db->escape($_POST['testo']);
$id_commento = $db->escape($_POST['id_commento']);
// verifico di non inserire commenti vuoti e con trim evito eventuali spazi
if (trim($testo) != "")
{
   
    $testo = trim($testo);
    $aggiornocommento = "UPDATE commento SET testo='{$testo}' WHERE codicecomm='{$id_commento}'";
    //eseguo la query
    $db->query($aggiornocommento);
    //trasforma in formato json un array e lo ritorna alla richiesta ajax dove lo gestirò come array
    echo json_encode(['ok']);
}
?>
