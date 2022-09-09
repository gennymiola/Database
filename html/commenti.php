<?php
require_once ('../php/config.php');
//rcupero id utente e id post e testo passati dalla richiesta ajax
$id_post = $_POST['id_post'];
$id_utente = $_POST['id_utente'];
$testo = $_POST['testo'];
// verifico di non inserire commenti vuoti e con trim evito eventuali spazi
if (trim($testo) != "")
{
    //cosiì se il testo cotiene un singolo apice la stringa non si interrompe e non genera un errore
    $testo = $db->escape($testo);
    //inserisco i dati nel database
    $aggiornocommento = "INSERT INTO commento (testo, codice_post, id_utente, dataora) VALUES ('{$testo}','{$id_post}', '{$id_utente}', NOW() )";
    #eseguo la query
    $db->query($aggiornocommento);
    //recupero l'id dell'ultimo commento
    $id_ultimocommento = $db->lastInsertID();
    //recupero l'ultimo commento inserito e ordino dal più recente al più vecchio mostrando solo l'ultimo e recupero anche lo username dell'utente che lo ha inserito e la data
    $commento_inserito = "SELECT date_format(dataora,'%d/%m/%Y %H:%i') as dataora, testo, u.username, c.codicecomm FROM commento as c JOIN utente_registrato as u ON c.id_utente=u.id WHERE codicecomm='{$id_ultimocommento}' order by dataora desc, codicecomm desc LIMIT 1";
    #eseguo la query
    $eseguoquery = $db->query($commento_inserito);
    //salvo dati in un array
    $ultimocommento = $db->fetchArray($eseguoquery);
    //trasforma in formato json un array con unico indice () e lo ritorna alla richiesta ajax dove lo gestirò come array
    echo json_encode($ultimocommento);
}
else
{
    echo json_encode(['error']);
}
?>
