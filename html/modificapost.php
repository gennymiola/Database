<?php
require_once('../php/config.php');
$datipost = $_POST;
$risultato = [];
//verifico che gli sia arrivato l'id del post che sto modificando
if (isset($datipost['id_post']) && $datipost['id_post'] != "") {
    $titolo = trim($db->escape($datipost['titolo']));
    $testo = trim($db->escape($datipost['testo']));
    //controlli
    if (strlen($titolo) < 3) {
        $errori[] = "Il titolo deve contenere almeno 3 caratteri";
    }
    if (strlen($testo) < 50) {
        $errori[] = "Il testo del post deve essere compreso tra 50 e 2000 caratteri";
    }

    //recupero l'argomento principale del blog
    $recuperomacroargomento = "SELECT argomento FROM blog WHERE codice='{$datipost['id_blog']}'";
    #eseguo la query
    $queryrecuperomacroargomento = $db->query($recuperomacroargomento);
    $macroarg = $db->fetchArray($queryrecuperomacroargomento);
    //salvo l'argometno del blog
    $argomento = $datipost['argomento'];

    if ($datipost['argomento'] == '') $argomento = 'NULL';
    else $argomento = $datipost['argomento'];


    //se in post è arrivato il nuovo campo e controllo se è diverso da vuoto (allora c'è statp scritto qualcosa)
    if (isset($datipost['nuovoargomento']) && trim($datipost['nuovoargomento']) != "") {
         $nuovoargomento=trim($db->escape($datipost['nuovoargomento']));
        //inseriso il nuovo argomento nel db, tipo è il nome scritto, id utente è l'id dell'utente loggato che l'ha inserito e il macro argomento sarà l'argomento principale del blog macroarg['argomento']
        //siccome tipo macro argomento e id utente sono chiave unica della tab argomento, se un utente inserisse una stessa sottocategoria ci sarebbe un errore per duplicazione della chiave, con ignore invece l'errore viene ignorato e l'inserimento non effettuato comunque
        $inseriscoargomento = "INSERT ignore INTO argomento (tipo, id_utente, macro_argomento) values ('{$datipost['nuovoargomento']}', '{$datipost['id_utente']}','{$macroarg['argomento']}' )";

        #eseguo la query
        $queryinserimentoargomento = $db->query($inseriscoargomento);
        // se l'inserimento sopra è andato a buon fine allora gli passo  id del nuovo sottoargomento
        if ($db->lastInsertID() != 0) {
            $argomento = $db->lastInsertID();
        } else {
            //se sono qui vuol dire che sto provando ad inserire un sottoargometo già inserito, allora con una query recupero l'id del vecchio argomento
            //la query filtra tra i valori della chiave che è unica
            $recuperaargomento = "SELECT codice FROM argomento WHERE tipo='{$datipost['nuovoargomento']}' AND macro_argomento='{$macroarg['argomento']}' ";
            #eseguo la query
            $queryrecupero = $db->query($recuperaargomento);
            $recuperoarg = $db->fetchArray($queryrecupero);
            //assegno vecchio argomento al post
            $argomento = $recuperoarg['codice'];
        }
    }


    //se non ci sono errori
    if (empty($errori)) {
        //aggiorno dati del post
        $datimodifica = "UPDATE post_esempio SET titolo='{$titolo}', testo='{$testo}', argomento={$argomento} WHERE codicepost='{$datipost['id_post']}'";
        $db->query($datimodifica);
        $risultato['risultato'] = 'ok';
    } else {
        $risultato['risultato'] = 'errore';
        $risultato['elencoerrori'] = implode("<br/>", $errori);
    }
} else {
    $risultato['risultato'] = 'errore';
    $risultato['elencoerrori'] = 'Errore nella modifica del blog';
}
//trasforma in formato json un array e lo ritorna alla richiesta ajax dove lo gestirò come array
echo json_encode($risultato);
