<?php
require_once ('../php/config.php');
//gli passo l'id del commento
$id_commento = $_POST['id_commento'];
// verifico di non inserire commenti vuoti e con trim evito eventuali spazi
if (trim($id_commento) != "" && $id_commento != 0)
{
    //elimino il commento che ha quell'id
    $eliminocommento = "DELETE FROM commento WHERE codicecomm='{$id_commento}'";
    //eseguo la query
    $db->query($eliminocommento);
    //trasforma in formato json un array e lo ritorna alla richiesta ajax dove lo gestirò come array
    echo json_encode(['ok']);
}
?>
