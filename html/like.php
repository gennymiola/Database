<?php
require_once ('../php/config.php');
//rcupero id utente e id post passati dalla richiesta ajax
$id_utente = $db->escape($_POST['id_utente']);
$id_post = $db->escape($_POST['id_post']);
//controllo che per utente loggato e per il post visualizzato se quell'utente ha o meno messo like
$controllolike = "SELECT * FROM utente_like WHERE id_utente='{$id_utente}' AND id_post='{$id_post}'";
//eseguo la query
$like = $db->query($controllolike);
//salvo dati in un array
$datilike = $db->fetchArray($like);
//datilike è un array: se ho un elemento nell'array vuol dire che ho già messo like e quindi lo tolgo con il -1, altrimenti lo aggiungo con +1
if (count($datilike) > 0)
{
    $aggiornolike = "UPDATE post_esempio SET n_like=n_like - 1 WHERE codicepost={$id_post}";
    #eseguo la query
    $db->query($aggiornolike);
    //cancello la relazione fra l'utente e il like al post, così la volta dopo se clicco la query non fa nulla
    $cancellolike = "DELETE FROM utente_like WHERE id_utente='{$id_utente}' AND id_post='{$id_post}'";
    #eseguo la query
    $db->query($cancellolike);
}
else
{

    $aggiornolike = "UPDATE post_esempio SET n_like=n_like + 1 WHERE codicepost={$id_post}";
    #eseguo la query
    $db->query($aggiornolike);
    //inserisco la relazione fra l'utente e il like al post
    $aggiungolike = "INSERT INTO utente_like (id_utente, id_post) VALUES ('{$id_utente}' ,'{$id_post}')";
    #eseguo la query
    $db->query($aggiungolike);
}

//recupero il numero dei like attuale del post
$numero_like = "SELECT n_like FROM post_esempio WHERE codicepost={$id_post}";
#eseguo la query
$eseguoquery = $db->query($numero_like);
//salvo dati in un array
$nlike = $db->fetchArray($eseguoquery);
//trasforma in formato json un array con unico indice (nlike) e lo ritorna alla richiesta ajax dove lo gestirò come array
echo json_encode($nlike);

?>
