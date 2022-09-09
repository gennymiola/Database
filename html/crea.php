<?php
require_once('../php/config.php');
$errori = [];
//verifico che la variabile (id del blog) usata nella query string in area riservata sia settata: se torna true facciamo get di id blog, altrimenti false
$id_blog = (isset($_GET["id_blog"]) ? $db->escape($_GET["id_blog"]) : false);
//verifico che la variabile (id del post) sia settata: se torna true facciamo get di id blog, altrimenti false
$id_post = (isset($_GET["id_post"]) ? $db->escape($_GET["id_post"]) : false);
$utente = $_SESSION['logged_user'];
#quando clicco su bottone pubblica (se ho dei dati in post)
if (count($_POST) > 0) 
{
    //controllo se sono in fase modifica (se la var è true:valore restituito dal campo hidden)
    $modifica = (isset($_POST['modifica']));
    //con escape se il testo contiene un singolo apice la stringa non si interrompe e non genera un errore
    $titolo = trim($db->escape($_POST['titolo']));
    $testo = trim($db->escape($_POST['testo']));

    //controllo se esiste già un post con il titolo inserito
    $controllotitolopost = "SELECT * from post_esempio WHERE titolo='{$titolo}'";
    #eseguo la query
    $querycontrollonome = $db->query($controllotitolopost);
    #ottengo il risultato della query
    $queryrisultatotitolo = $db->fetchArray($querycontrollonome);
    //controllo che non ci sia un post con lo stesso titolo inserito e che ho inserito almeno un carattere diverso dallo spazio nel titolo (funzione trim)
    if ((empty($queryrisultatotitolo) && trim($_POST['titolo']) != "") || $modifica) {
        //controllo che id blog sia diverso da false e che quindi sia settato
        if ($id_blog !== false || $modifica) {

            if(!$modifica){
                if (strlen($titolo) < 3) {
                    $errori[] = "Il titolo deve contenere almeno 3 caratteri";
                }

                if (strlen($testo) < 50) {
                    $errori[] = "Il testo del post deve essere compreso tra 50 e 2000 caratteri";
                }
            }
            
            //se non ci sono errori faccio inserimenti nel db
            if (empty($errori)) {
                if(!$modifica){
                    //recupero l'argomento principale del blog
                    $recuperomacroargomento = "SELECT argomento FROM blog WHERE codice='{$id_blog}'";
                    #eseguo la query
                    $queryrecuperomacroargomento = $db->query($recuperomacroargomento);
                    $macroarg = $db->fetchArray($queryrecuperomacroargomento);
                    //la var argomento prende il valore settato dalla select
                    $argomento = $_POST['argomento'];
                    //se in post è arrivato il nuovo campo e controllo se è diverso da vuoto (allora c'è stato scritto qualcosa)
                    if (isset($_POST['nuovoargomento']) && trim($_POST['nuovoargomento']) != "") {
                        $nuovoargomento=trim($db->escape($_POST['nuovoargomento']));
                        //inserisco il nuovo argomento nel db, tipo è il nome scritto, id utente è l'id dell'utente loggato che l'ha inserito e il macro argomento sarà l'argomento principale del blog macroarg['argomento']
                        //siccome tipo macro argomento e id utente sono chiave unica della tab argomento, se un utente inserisse una stessa sottocategoria ci sarebbe un errore per duplicazione della chiave, con ignore invece l'errore viene ignorato e l'inserimento non effettuato comunque
                        $inseriscoargomento = "INSERT ignore INTO argomento (tipo, id_utente, macro_argomento) values ('{$nuovoargomento}', '{$utente['id']}','{$macroarg['argomento']}' )";

                        #eseguo la query
                        $queryinserimentoargomento = $db->query($inseriscoargomento);
                        // se l'inserimento sopra è andato a buon fine allora gli passo  id del nuovo sottoargomento
                        if ($db->lastInsertID() != 0) {
                            $argomento = $db->lastInsertID();
                        } else {
                            //se sono qui vuol dire che sto provando ad inserire un sottoargometo già inserito, allora con una query recupero l'id del vecchio argomento
                            //la query filtra tra i valori della chiave che abbiamo come unica
                            $recuperaargomento = "SELECT codice FROM argomento WHERE tipo='{$nuovoargomento}' AND macro_argomento='{$macroarg['argomento']}' ";
                            #eseguo la query
                            $queryrecupero = $db->query($recuperaargomento);
                            $recuperoarg = $db->fetchArray($queryrecupero);
                            //assegno vecchio argomento al post
                            $argomento = $recuperoarg['codice'];
                        }
                    }
                    //inserisce i dati del post nel database
                    $postinserito = "INSERT into post_esempio(titolo, testo, data, codice_blog, codice_autore, argomento) values ('{$titolo}','{$testo}', now(),'{$id_blog}','{$utente['id']}'," . ($argomento == "" ? "NULL" : $argomento) . ")";
                    //eseguo la query
                    $db->query($postinserito);
                    //recupero l'id dell'ultimo post (appena inserito)
                    $idpostinserito = "SELECT MAX(codicepost) as id_post FROM post_esempio";
                    //eseguo la query
                    $eseguoquerypost = $db->query($idpostinserito);
                    //salvo l'id del post
                    $idpost = $db->fetchArray($eseguoquerypost);

                    $id_post = $idpost['id_post'];
                } else {

                    $recuperoIdBlog = "SELECT * from post_esempio WHERE codicepost = '{$id_post}'";
                    $queryrecuperoIdBlog = $db->query($recuperoIdBlog);
                    //se sono in modifica non ho l'id blog quindi lo recupero partendo dall'id del post che sto modificando
                    $id_blog = $db->fetchArray($queryrecuperoIdBlog)['codice_blog'];
                }
                //ciclo files così che tipoimmagine la prima volta sarà l'immagine di copertina e la seconda immagine da inserire e immagine conterrà i rispettivi dati
                foreach ($_FILES as $tipoimmagine => $immagine) {
                    //funzione trim elimina gli spazi all'inizio e alla fine della stringa e l'if controlla che il campo nome dell'immagine sia diverso da vuoto per verificare che ho caricato un file
                    if (trim($immagine['name']) != "") {
                        //la var info recupera le info dal file caricato (immagine di copertina o immagine da inserire e nome vero del file caricato), pathinfo è una funzione che recupera i dettagli del file (nome, estensione etc..)
                        $info = pathinfo($immagine['name']);
                        //salvo l'estensione del file caricato
                        $ext = $info['extension'];
                        //crea la stringa del nuovo nome mantendendo la sua estensione e aggiungndo l'id del post
                        $newname = $id_post . '_' . $tipoimmagine . '.' . $ext;
                        //stabilisco il percorso in cui salvare il file
                        $target = '../images/' . $newname;
                        //se esiste già l'immagine per il post, la cancello e salvo la nuova

                        $checkExist = glob($percorsoimmagine . $id_post . '_' . $tipoimmagine.".*");
                        if(file_exists($checkExist[0])) unlink($checkExist[0]);

                        //funzione prende il file e lo sposta nella cartella di destinazione, prendendolo tramite parametro tmp_name che è il nome che php ha dato temporaneamente al file caricato prima che lo salvi
                        move_uploaded_file($immagine['tmp_name'], $target);
                    }
                }
                #redirect alla pagina ricercablog.php e concatenazione stringhe e recupero id blog per rimandare alla pagina del blog
                header("location: ricercablog.php?id_blog=" . $id_blog);
            }
            // svuota la variabile post
            unset($_POST);
        }
    } else {
        if (!empty($queryrisultatotitolo)) {
            $errori[] = "Esiste già un post con questo titolo";
        }
        if (trim($_POST['titolo']) == "") {
            $errori[] = "Il campo titolo è obbligatorio.";
        }
    }
}
// dichiaro datipost come array vuoto
$datipost = [];
//controllo che id post sia diverso da false e che quindi sia settato
if ($id_post !== false) {
    $querydatipost = "SELECT * FROM post_esempio WHERE codicepost={$id_post}";
    $eseguoquery = $db->query($querydatipost);
    $datipost = $db->fetchArray($eseguoquery);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Il mio blog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="../js/menu.js"></script>
    <script src="../js/jQuery.js"> </script>
    <script src="../js/function.js"> </script>
    <script src="../js/jquery-ui/jquery-ui.min.js"> </script>
    <link rel="stylesheet" href="../js/jquery-ui/jquery-ui.min.css">
</head>

<body>
    <?php
    include("nav.php");
    ?>





    <section class="hero is-small" style="background-color: black;">
        <div class="hero-body">
            <div class="container has-text-centered">
                <img src="../img/index.jpg" width="200" height="40">
                <h2 class="subtitle is-4" style="color: #f7eac1;">
                    Scrivi un post per il tuo blog
                </h2>
            </div>
        </div>
    </section>
    <section style="background-color: #f7eac1;" class="hero is-fullheight is-default is-bold">
        <form id="form-post" action="" method="post" enctype="multipart/form-data">
            <div class="hero-body">
                <div class="container has-text-centered">
                    <div class="columns is-vcentered">
                        <div class="column is-6">


                            <input maxlength="50" class="input is-warning is-medium" type="text" style="text-align: center; margin-bottom: 10px;" value="<?php if (isset($datipost['titolo'])) echo $datipost['titolo']; ?>" name="titolo" placeholder="titolo">

                            <div class="field">
                                <div class="control">
                                    <textarea maxlength="2000" class="textarea is-large is-warning" placeholder="Scrivi il tuo post" name="testo"><?php if (isset($datipost['testo'])) echo $datipost['testo']; ?></textarea>
                                </div>
                                <br>
                                <div class="file">
                                    <label class="file-label">
                                        <input class="file-input" type="file" name="immagine_copertina" accept="image/*">
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="fas fa-upload"></i>
                                            </span>
                                            <span class="file-label">
                                                Scegli un'immagine di copertina
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <div class="file">
                                    <label class="file-label">
                                        <input class="file-input" type="file" name="immagine_dainserire" accept="image/*">
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="fas fa-upload"></i>
                                            </span>
                                            <span class="file-label">
                                                Scegli un'immagine allegato
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <style>
                                    .file {
                                        display: inline-block;
                                    }
                                </style>
                            </div>
                        </div>
                        <div class="column is-3 is-offset-0">
                            <h2 class="subtitle is-5">
                                Seleziona un sottoargomento
                            </h2>

                            <div class="select is-rounded is-normal">
                                <select id="argomento" name="argomento">
                                    <option value="">seleziona sottoargomento</option>
                                    <?php
                                    $where = "";
                                    //se l'utente non è al livello 2 non mostro gli argomenti che sono stati creati dagli utenti del liv 3
                                    if ($utente['livelloutente'] < 2) {
                                        //se l'utente è al al liv 0 o 1 mostro i macroargomenti ma sono quelli in cui l'id dell'utente è null, quelli che non sono stati inseriti da altri utenti registrati
                                        $where = " AND a.id_utente is null";
                                    }
                                    if ($id_blog == false) {
                                        $selectBlogDaPost = "SELECT codice_blog FROM post_esempio WHERE codicepost = '{$id_post}'";
                                        $queryIdBlogDaPost = $db->query($selectBlogDaPost);
                                        $id_blog = $db->fetchArray($queryIdBlogDaPost)['codice_blog'];
                                    }
                                    //recupero dinamicamente i sottoargomento disponnibili in base al macroargomento del blog, l'arg del blog è macroargomento dell'argomento che sto cercando
                                    $argomenti = "SELECT a.codice, a.tipo FROM argomento as a JOIN blog as b ON b.argomento=a.macro_argomento WHERE b.codice='{$id_blog}'{$where}";
                                    var_dump($argomenti);
                                    #eseguo la query
                                    $queryargomento = $db->query($argomenti);
                                    #ottengo il risultato della query e scrivo nella var dati argomento i dati degli argomenti che stampo in pagina
                                    $datiargomento = $db->fetchAll($queryargomento);
                                    //faccio un ciclo con cui associare ogni array dell'array datiargomento alla variabile argomento
                                    foreach ($datiargomento as $argomento) {
                                        //per preimpostare la select in modo da modificare il blog faccio un controllo per vedere se l'argomento preimpostato è quello che è stato selezionato da datiblog

                                        $selected = "";
                                        if (!empty($datipost) && $argomento['codice'] == $datipost['argomento']) {
                                            $selected = " selected='selected'";
                                            //se l'argomento che sto stampando nella select è quello abbinato al blog che sto modificando, la var selected prenderà il valore selected='selected e quindi dopo nella stampa, dopo il valore delle option stamperà l'attrbuto selected (visualizzo l'argomento selezionato in fase di creazione del blog)
                                        }
                                        //stampa il valore del campo codice inerente al record di argomento che sta stampando e il tipo di argomento
                                        echo "<option value='{$argomento['codice']}'{$selected}>{$argomento['tipo']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <?php //mostro il campo x creare un nuovo argomento solo se l'utente è al livello3
                        if ($utente['livelloutente'] == 3) {
                        ?>
                            <div class="field" id="c">
                                <div id="c" class="control ">
                                    <h2 class="subtitle is-5">

                                        Inserisci un nuovo sottoargomento</h2>
                                    <input class="input is-normal" style="border-radius: 25px;" maxlength="20" type="text" name="nuovoargomento" placeholder="nuovo argomento" />
                                    <style>
                                        #c {
                                            display: inline-block;
                                            padding: 5px;

                                        }
                                    </style>


                                <?php }
                                ?>
                                </div>
                            </div>
                            <?php
                            if ($id_post === false) {
                            ?>


                    </div>
                </div>
            </div>
            </div>

            <br>

            <p class="has-text-centered">


                <input type="submit" class="button is-normal is-light" style="border-style: solid 1px; border-color: black; margin:auto;" value="pubblica">
            </p>
        <?php
                            } else {
                                // quando clicco su modifica chiama la funzione javascript
                                //con type hidden faccio sì che l'id del post venga imviato alla richiesta ajax
        ?>
            </div>
            </div>
            </div>
            </div>

            <br>

            <p class="has-text-centered">
                <input type="hidden" id="id_post" name="id_post" value="<?php echo $id_post ?>" />
                <input type="hidden" name="id_utente" value="<?php echo $utente['id'] ?>" />
                <input type="hidden" name="id_blog" value="<?php echo $id_blog ?>" />
                <input type="hidden" name="modifica" value="true" />
                <a class="button is-normal is-light" style="border-style: solid 1px; border-color: black;margin:auto;" onclick="modificapost();" href="javascript:void(0)">Modifica</a>
            </p>
        <?php
                            }
        ?>
        </div>
        </div>
        </form>
    </section>
    <?php //gestione errore
    //inizializzo var per visualizzare il pop ip di errore quano c'è un errore
    $popupvisibile = "";
    // se l'array errori non è vuoto (c'è almeno un errore) faccio comparire pop up
    if (!empty($errori)) {
        $popupvisibile = " is-active";
    }
    ?>
    <div class="modal<?php echo $popupvisibile; ?>" id="e">
        <div class="modal-background"></div>
        <div class="modal-content">
            <article class="message is-dark">
                <div class="message-header">
                    <p>Attenzione</p>
                    <button class="delete" aria-label="delete" id="errore" onclick="$('#e').removeClass('is-active');"></button>
                </div>
                <div class="message-body">
                    <p class="content has-text-justified">
                        <?php echo implode("<br/>", $errori); //stampo, con il metodo implode che trasforma gli elementi dell'array errori in un'unica stringa separati dal separatore <Br>, l'array
                        ?>
                    </p>

                </div>
            </article>
        </div>
    </div>
</body>
</html>