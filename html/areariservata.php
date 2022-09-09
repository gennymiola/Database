<?php
require_once('../php/config.php');
//inizializzo un array vuoto
$errori = [];
//chiamo la funzione nel file config che calcola il livello dell'utente e gli passo l'id dell'utente loggato
calcolalivelloutente($_SESSION['logged_user']['id']);
$utente = $_SESSION['logged_user'];

//inizializzo la var come vuota
$aggiorna_password = '';
#controllo se arriva post
if (count($_POST) > 0) 
{
    //controllo se arriva dal form di modifica dei dati dell'utente
    if (isset($_POST['modifica_datiutente'])) {
        //controlli di sicurezza
        if (trim($_POST['username']) != '' && trim($_POST['email']) != '') {
            $username = $db->escape($_POST['username']);
            $email = $db->escape($_POST['email']);
            #controllo che l'username non sia usato da un altro utente nel db
            $sqlcontrollousername = "SELECT * FROM utente_registrato WHERE username = '{$username}'AND id != '{$utente['id']}'";
            #eseguo la query
            $querycontrollousername = $db->query($sqlcontrollousername);
            #ottengo il risultato della query
            $queryrisultatousername = $db->fetchArray($querycontrollousername);
            
            #controllo che l'email non sia usata da un altro utente nel db
            $sqlcontrolloemail = "SELECT * FROM utente_registrato WHERE email = '{$email}' AND id != '{$utente['id']}'";
            #eseguo la query
            $querycontrolloemail = $db->query($sqlcontrolloemail);
            #ottengo il risultato della query
            $queryrisultatoemail = $db->fetchArray($querycontrolloemail);

            #controllo che $queryrisultato sia vuoto (uso io i dati), se non è vuoto è già usato quell'username o email nel db (metodo empty restituisce true o false, se true l'array è vuoto)
            if (empty($queryrisultatousername) && empty($queryrisultatoemail)) {
                //se l'email non rispetta il formato stabilito nella RE nel file config
                if (!preg_match($controllo_email, $_POST['email'])) {
                    $errori[] = "email non valida";
                }
                //lo username deve essere di almeno 6 caratteri
                if (strlen($_POST['username']) < 6) {
                    $errori[] = "usa almeno 6 caratteri per lo username";
                }

                if ($_POST['newpassword'] != '' && $_POST['password'] != '') {
                    $password = $db->escape($_POST['password']);
                    $newpassword = $db->escape($_POST['newpassword']);
                    // controllo che la password inserita nel campo password sia uguale a quella registrata nel db e che quella passw sia legata al mio utente
                    $sqlcontrollopassword = "SELECT * FROM utente_registrato WHERE password = MD5('{$password}') AND id = '{$utente['id']}'";
                    #eseguo la query
                    $querycontrollopassword = $db->query($sqlcontrollopassword);
                    #ottengo il risultato della query, se ho un risultato allora ho inserito la passw giusta
                    $queryrisultatopassword = $db->fetchArray($querycontrollopassword);
                    //controllo che la nuova e la vecchia passw siano diverse
                    if ($_POST['newpassword'] != $_POST['password']) {
                        // se il risultato della query non è vuoto (ho un risultato) allora entro nell'if e controllo che la nuova password sia uguale a conferma password

                        if (!empty($queryrisultatopassword) && $_POST['newpassword'] == $_POST['conferma_newpassword']) {

                            //cripta il valore di new password
                            $aggiorna_password = ",password=MD5('{$newpassword}')";
                        } else {
                            if (empty($queryrisultatopassword)) $errori[] = "La vecchia password non è corretta";
                            else $errori[] = "Le nuove password non coincidono";
                        }
                        //la password deve rispettare il formato della RE per essere sicura
                        if (!preg_match($controllo_password, $_POST['newpassword'])) {
                            $errori[] = " Password: usa almeno un carattere speciale, un numero. La password deve contenere almeno 8 caratteri.";
                        }
                    } else {
                        $errori[] = "Inserisci una password diversa da quella precendente";
                    }
                } else {
                	// controllo che abbia inserito password vecchia, nuova e conferma password
                    if (($_POST['password'] == '' && $_POST['newpassword'] != '' && $_POST['conferma_newpassword'] != '') || ($_POST['password'] != '' && $_POST['newpassword'] == '' && $_POST['conferma_newpassword'] == '') || ($_POST['password'] == '' && $_POST['newpassword'] == '' && $_POST['conferma_newpassword'] != '') || ($_POST['password'] == '' && $_POST['newpassword'] != '' && $_POST['conferma_newpassword'] == '')) {
                        $errori[] = "Per aggiornare la password inserisci la password attuale e quella nuova";
                    }
                }
                //se  il telefono non rispetta il formato stabilito nella RE nel file config
                if (!empty($_POST['telefono']) && !preg_match($controllo_telefono, $_POST['telefono'])) {
                    $errori[] = "numero non valido. Formati validi per i mobili 0039, +39. Non usare spazi e punti";
                }
                if (empty($errori)) {
                    $telefono = $db->escape($_POST['telefono']);
                    //allora se i dati non sono già usati li aggiorno
                    $sql = "UPDATE utente_registrato SET username='{$username}', email='{$email}', telefono='{$telefono}'{$aggiorna_password} WHERE id='{$utente['id']}'";
                    #eseguo la query
                    $db->query($sql);
                    //recupero le info dell'utente dopo l'aggiornamento
                    $recuperoUtente = "SELECT * FROM utente_registrato WHERE id = '{$utente['id']}'";
                    $queryRecuperoUtente = $db->query($recuperoUtente);
                    $datiUtente = $db->fetchArray($queryRecuperoUtente);

                    //aggiorno  ogni campo del'array utente' per la var utente che poi riutilizzo 
                    foreach ($datiUtente as $campo => $valore) {
                        $utente[$campo] = $valore;
                        /*
                        $datiUtente=[
                            "id" => 134
                            "username" => "genny",
                            "password" => "pippo"
                        ]

                        1 ciclo:
                        $campo = "id";
                        $valore = "134";

                        2ciclo:
                        $campo = "username";
                        $valore = "genny"
                        */
                    }
                    $errori[] = "Dati salvati con successo";
                }
            } else {
                $errori[] = "Username o email già utilizzati";
            }
        } else {
            $errori[] = "Username e email non possono essere vuote";
        }
    }


    // controllo se è settato elimina coautore (ho cliccato su una x), se ho cliccato, nella tabella blog aggiorno il valore dell'id del coautore, mettendolo null, quando quell'id è uguale a quello di elimina_coautore
    if (isset($_POST['elimina_coautore'])) {
        $cancella_coautore = "UPDATE blog SET coautore=NULL WHERE coautore={$_POST['elimina_coautore']} ";
        //eseguo la query
        $db->query($cancella_coautore);
    }
    // controllo se è settato elimina blog (ho cliccato su una x), se l'ho fatto elimino dalla tabella blog il blog con quell'id
    if (isset($_POST['elimina_blog'])) {
        $cancella_blog = "DELETE FROM blog WHERE codice={$_POST['elimina_blog']} ";
        //eseguo la query
        $db->query($cancella_blog);
    }
    // controllo se è settato elimina profilo (ho cliccato su bottone), se l'ho fatto elimino dalla tabella blog il profilo con quell'id
    if (isset($_POST['eliminaprofilo'])) {
        $cancella_profilo = "DELETE FROM utente_registrato WHERE id='{$utente['id']}' ";
        //eseguo la query
        $db->query($cancella_profilo);
        unset($_SESSION['logged_user']);
        unset($utente);
        session_destroy();
        header("location:index.php");
    }
    // svuota la variabile post
    unset($_POST);
}

//inizializzo le tre immagini dei livelli con una classe
$iconaliv1 = "opacity0";
$iconaliv2 = "opacity0";
$iconaliv3 = "opacity0";
//quando supero livello 2 e sono al tre
if ($utente['livelloutente'] == 3) {
    //metto tutte e tre le immagini trasparenti a livello 3
    $iconaliv1 = "opacity1";
    $iconaliv2 = "opacity1";
    $iconaliv3 = "opacity1";
    //perché siamo al 100%
    $calcolopercentuale = 1;
    $colore_barra = "#F4C430";
} elseif ($utente['livelloutente'] == 2) {
    $iconaliv1 = "opacity1";
    $iconaliv2 = "opacity1";
    //quando completo livello 2
    $colore_barra = "#CCFF00";
    //calcolo la percentuale del 3 livello, prendendo il n di post e di commenti a cui si trova l'utente, meno la soglia massima del livello precendente (così da far ripartire la barra da 0) diviso la soglia massima del livello a cui mi trovo meno la soglia massima del livello precendente
    $calcolopercentuale = ($utente['numeropostcommenti'] - $livello2) / ($livello3 - $livello2);
} elseif ($utente['livelloutente'] == 1) {
    $calcolopercentuale = ($utente['numeropostcommenti'] - $livello1) / ($livello2 - $livello1);
    $iconaliv1 = "opacity1";
    $colore_barra = "#FBCEB1";
} else {
    $calcolopercentuale = ($utente['numeropostcommenti'] / $livello1);
    $colore_barra = "#F0DC82";
}
//moltiplico la variabile per 100 per avere la percentuale di completamento del livello
$calcolopercentuale *= 100;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Il mio blog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <link rel="stylesheet" href="tabs.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="../js/jQuery.js"> </script>
    <script src="../js/function.js"> </script>
    <script src="../js/jquery.cookie.js"> </script>
    <script src="../js/menu.js"></script>
    <script src="../js/jquery-ui/jquery-ui.min.js"> </script>
    <link rel="stylesheet" href="../js/jquery-ui/jquery-ui.min.css">
    <script src="admin.js"></script>
</head>

<body>
    <?php
    include("nav.php");
    ?>




    <section class="hero is-fullheight" style="background-color: #f7eac1;">

        <div class="columns">
            <div class="column is-0"> </div>
            <div class="column is-3">
                <br>
                <aside class="menu is-hidden-mobile">
                    <p class="menu-label">
                        Personali
                    </p>
                    <ul class="menu-list">
                        <li><a>Modifica profilo</a></li>
                    </ul>
                    <p class="menu-label">
                        Amministrazione
                    </p>
                    <ul class="menu-list">
                        <li>
                            <!-- gli passo la funzione con l'id della tab e l'id dei div del contenitore --->
                            <a href="javascript:void(0)" onclick="toggleTab(3,'pane-3')">Gestisci Blog</a>
                        </li>
                        <li><a href="javascript:void(0)" onclick="toggleTab(4,'pane-4')">Collaborazioni</a></li>


                        <li>
                            <a class="button is-dark is-outlined is-normal is-fullheight " href="blog.php" style="border: 1px solid; display:inline-block; "> Crea nuovo blog </a>
                        </li>
                        <br>
                        <li>
                            <form id="elimina_utente" action="" method="post">
                                <button name="eliminaprofilo" class="button is-danger is-outlined is-normal is-fullheight" style="border: 1px solid;"> elimina profilo </button>
                            </form>
                            <script type="text/javascript">
                                $('#elimina_utente').submit(function() {
                                    //confirm metodo js che apre un alert con conferma e annulla, assegna il risultato ad una var r, così che se clicchi su ok confirm ritorna true, se clicchi su annulla ritorna false. il confirm è nella funzione submit che ha l'id del form, così che quando si clicca sul bottone x eliminare il profilo prima di fare la submit apre l'alert e se ritorna true fa la submit sennò non fa niente
                                    r = confirm("Sei sicuro di voler eliminare il tuo profilo? non si torna indietro!");
                                    if (r === true) return true; // return false to cancel form action
                                    else return false;
                                });
                            </script>
                        </li>
                    </ul>
                </aside>
            </div>
            <div class="column is-6">
                <br>
                <section class="hero has-text-centered welcome is-small">
                    <div class="hero-body">
                        <div class="container">
                            <h1 class="title">
                                Ciao, <?php echo $utente['nome']; ?> <?php echo $utente['cognome']; ?>
                            </h1>
                            <h2 class="subtitle">
                                Bentornato/a!
                            </h2>
                        </div>
                    </div>

                </section>
                <div class="column is-0">
                </div>
                <script>
                    $(function() {
                        $("#progressbar").progressbar({
                            value: <?php echo $calcolopercentuale ?>
                        });
                    });
                </script>
                <style>
                    #progressbar {
                        background-color: white;
                    }

                    .ui-progressbar .ui-progressbar-value {
                        background-color: <?php echo $colore_barra ?>;
                    }

                    .opacity0 {
                        opacity: 0.3;
                    }

                    .opacity1 {
                        opacity: 1;
                    }

                    #immaginelivello div {
                        display: inline-block;
                        max-width: 100px;
                        vertical-align: top;
                        margin-top: 10px;

                    }

                    #immaginelivello div img {
                        border-radius: 50px;

                    }
                </style>
                <div id="progressbar"></div>
                <div class="tabs is-centered" style="white-space: normal; overflow: unset;" id="immaginelivello">
                    <div>
                        <img class="<?php  //stampo la classe di appartenenza dell'immagine
                                    echo $iconaliv1 ?> " src="../img/livello1.png" />

                    </div>
                    <div>
                        <img class="<?php echo $iconaliv2 ?> " src="../img/livello2.png" />

                    </div>
                    <div>
                        <img class="<?php echo $iconaliv3 ?> " src="../img/livello3.png" />

                    </div>
                </div>


                <div class="tile is-ancestor has-text-centered">
                    <div class="tile is-parent">
                        <?php if ($utente['livelloutente'] >= 1) {
                        ?>
                            <article class="tile is-child box">
                                <p class="title is-4">Livello <?php echo $utente['livelloutente']; ?> superato!</p>
                                <?php //in base al livello in cui si trova l'utente stampo un caso dello switch
                                switch ($utente['livelloutente']) {
                                    case 1:
                                ?>
                                        <p class="subtitle">Complimenti! <br>Hai sbloccato una nuova grafica!</p>
                                    <?php break;
                                    case 2:
                                    ?>
                                        <p class="subtitle">Complimenti!<br>  Hai sbloccato una nuova grafica! <br> Adesso puoi vedere anche i nuovi argomenti e sottoargomenti creati da altri utenti!</p>
                                    <?php break;
                                    case 3: ?>
                                        <p class="subtitle">Complimenti! <br>Hai sbloccato una nuova grafica! <br> Da adesso hai la possibilità di aggiungere argomenti e sottoargomenti personalizzati per i tuoi blog e post!</p>
                                <?php break;
                                } ?>
                            </article>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>


        <section class="hero">
            <div class="hero-body">
                <div class="container">

                    <div class="tabs is-boxed is-centered main-menu" id="nav">
                        <ul>
                            <li data-target="pane-1" class="is-active" id="1">
                                <a>

                                    <span>Dati personali</span>
                                </a>
                            </li>

                            <li data-target="pane-3" id="3">
                                <a>

                                    <span>I miei blog</span>
                                </a>
                            </li>
                            <li data-target="pane-4" id="4">
                                <a>

                                    <span>I miei coautori</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane is-active" id="pane-1">

                            <p class="title is-4" style="text-align: center;">DATI PERSONALI</p>
                            <p align="center"> <?php echo $utente['nome']; ?> <?php echo $utente['cognome']; ?> </p>

                            <br>
                            <form id="datiUtente" action="" method="post">
                                <div id="personale" class="columns">

                                    <div class="column is-half" style="align-items: stretch;">


                                        <!-- apro flusso php e stampo la variabile utente dentro il campo utente -->
                                        <div class="field">
                                            <p class="control">
                                            <p style="text-align: center;"><strong>username</strong> </p>
                                            <input maxlength="20" class="input" name="username" value="<?php echo $utente['username']; ?>" type="text" placeholder="Username">
                                            </p>
                                        </div>

                                        <div class="field">
                                            <p class="control has-icons-left">
                                                <input class="input" maxlength="20" name="password" type="password" placeholder="Password attuale">
                                                <span class="icon is-small is-left">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            </p>
                                        </div>

                                        <div class="field">
                                            <p class="control has-icons-left">

                                                <input class="input" maxlength="20" name="newpassword" type="password" placeholder="Nuova Password">

                                                <span class="icon is-small is-left">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field">
                                            <p class="control has-icons-right">


                                                <input class="input" maxlength="20" name="conferma_newpassword" type="password" placeholder="Conferma nuova password">
                                                <span class="icon is-small is-right">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </p>
                                        </div>

                                    </div>

                                    <div class="column is-half" style="align-items: stretch;">

                                        <p style="text-align: center;"><strong>email</strong></p>
                                        <div class="field">
                                            <p class="control has-icons-left">


                                                <input class="input" name="email" value="<?php echo $utente['email']; ?>" type="email" maxlength="100" placeholder="Email">
                                                <span class="icon is-small is-left">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" name="telefono" value="<?php echo $utente['telefono']; ?>" maxlength="10" type="tel" placeholder="Telefono">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-half is-offset-4">
                                    <style>
                                        .disableClick {
                                            pointer-events: none;
                                            cursor: pointer;
                                        }
                                    </style>
                                    <input type="submit" id="modifica-dati-utente" name="modifica_datiutente" class="button is-dark is-outlined is-normal disableClick" value="Modifica dati" style="border: 1px solid">
                                </div>

                            </form>
                        </div>

                        <div class="tab-pane" id="pane-3">
                            <p class="title is-4" style="text-align: center">I MIEI BLOG </p>
                            <?php
                            //recupero i dati del blog e dell'utente che lo ha creato e l'username del coautore con una join con cui controllo che l'id del coautore sia uguale a quello dell'utente che lo ha creato (uso left join perché considera anche i record di blog anche se non c'è corrispondenza)
                            $recuperoblog = "SELECT b.*, u.username, a.tipo FROM blog as b LEFT JOIN utente_registrato as u ON b.coautore=u.id JOIN argomento as a ON b.argomento=a.codice WHERE b.id_utente='{$utente['id']}' OR b.coautore='{$utente['id']}'";
                            //eseguo la query
                            $eseguiqueryblog = $db->query($recuperoblog);
                            #ottengo il risultato della query (tutti i blog dell'utente)
                            $blogutente = $db->fetchAll($eseguiqueryblog);
                            //per ogni elemento che stampa il foreach stampa ciò che c'è all'interno del corpo html
                            //ogni elemento di blogutente è assegnato a riga blog, che sarà la singola riga della tabella blog con tutti i campi della tabella (select *) 
                            foreach ($blogutente as $rigablog) {
                            ?>

                                <div class="columns" style="margin-left: 5%" ;>

                                    <div class="column">
                                        <p> titolo </p>
                                        <?php
                                        //tutto ciò che è dopo il ? è query string: una stringa che contiene delle variabili che si possono recuperare nella pagina di atterraggio (no dati sensibili)
                                        echo "<a href='ricercablog.php?id_blog={$rigablog['codice']}'>{$rigablog['nome']} </a>";
                                        ?>

                                    </div>
                                    <div class="column">
                                        <p> argomento </p>
                                        <?php
                                        echo $rigablog['tipo'];
                                        ?>
                                    </div>
                                    <div class="column">
                                        <a class="button is-small button is-dark is-outlined " href="blog.php?id_blog=<?php echo $rigablog['codice'] ?>">Modifica Blog</a>
                                    </div>
                                    <div class="column">
                                        <?php if ($rigablog['id_utente'] == $utente['id']) { ?>
                                            <form action="" method="POST">
                                                <button name='elimina_blog' value='<?php echo $rigablog['codice']; ?>' class='fa-solid fa-trash-can button is-dark is-outlined is-small '> Elimina Blog</button>
                                            </form>
                                        <?php } ?>
                                    </div>
                                    <div class="column" id="post">
                                        <?php
                                        echo "<a class='button is-dark is-outlined is-small' href='crea.php?id_blog={$rigablog['codice']}'>Crea nuovo post </a>"
                                        ?>
                                    </div>

                                </div>
                            <?php
                            }
                            ?>
                            <div class="column is-half is-offset-5">
                                <a id="but" class="button is-dark is-outlined is-normal" href="blog.php" style="border: 1px solid;margin-top: 5%;"> Crea nuovo blog </a>
                            </div>

                        </div>

                        <div class="tab-pane" id="pane-4">
                            <p class="title is-4" style="text-align: center;">I MIEI COAUTORI</p>
                            <div class="column">
                                <form action="" method="post">
                                    <div class="column is-half is-offset-3" style="margin:auto">

                                        <?php
                                        //ciclo tutti i blog che l'utente ha creato, controllo che lo username sia diverso da vuoto e stampo lo usename del coautore in un div
                                        foreach ($blogutente as $rigablog) {
                                            if ($rigablog['username'] != "" && $rigablog['coautore'] != $utente['id'])
                                                echo "<div class='coautore'>" . "<a href='profilo2.php?id={$rigablog['coautore']}'>" . "<strong>" . $rigablog['username'] . "</strong>" . "</a>" . " " . "per il blog" . " " . "<a href='ricercablog.php?id_blog={$rigablog['codice']}'>{$rigablog['nome']} </a>" . "<button name='elimina_coautore' id='coau' value='{$rigablog['coautore']}' class='fa-solid fa-trash-can button is-dark is-outlined is-small'> Elimina Coautore </button></div>";
                                        }
                                        ?>
                                        <style>
                                            .coautore {
                                                display: inline-block;
                                            }

                                            #coau {
                                                margin-top: 5%;
                                            }
                                        </style>
                                </form>
                            </div>

                        </div>
                    </div>



                </div>
            </div>
        </section>
        <script src="../js/tabs.js"></script>
        <?php //gestione errore
        //inizializzo var per visualizzare il pop  di errore quando c'è un errore
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
                        <p></p>
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