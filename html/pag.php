<?php
require_once('../php/config.php');
//se l'utente è loggato, recupero i suoi dati che ho in sessione, altrimenti inizializzo un array vuoto
$datiutente = (isset($_SESSION['logged_user'])) ? $_SESSION['logged_user'] : array();
//recupero la variabile usata nella query string in area riservata
$id_post = $db->escape($_GET["id_post"]);
//uso la left join perché il campo potrebbe essere null
$dettagliopost = "SELECT p.*, b.*, a.*, a1.*, u.*, g.*, a1.tipo as argomento_post, a.tipo as argomento_blog, b.nome as nomeblog FROM post_esempio as p JOIN blog as b ON p.codice_blog=b.codice JOIN argomento as a ON b.argomento=a.codice LEFT JOIN argomento as a1 ON p.argomento=a1.codice JOIN utente_registrato as u ON p.codice_autore=u.id LEFT JOIN grafica as g ON g.codice=b.grafica WHERE p.codicepost={$id_post}";
#eseguo la query
$querydettagliopost = $db->query($dettagliopost);
#ottengo il risultato della query e sovrascrivo nella var  i dati del post che stampo in pagina
$datipost = $db->fetchArray($querydettagliopost);
//inizializzo una var che sarà true o false e che mi permette di controllare che l'utente sia loggato, che l'utente sia o l'autore del post o il coautore del blog a cui appartiene il post
$controllo_utente = (!empty($datiutente) && ($datiutente['id'] == $datipost['codice_autore'] || $datiutente['id'] == $datipost['coautore']));
//uso la funzione php glob per recuperare le immagini relative al post che stampo e le cerco nella cartella images e con il nome id post seguito immaginecopertina (per l'immagine di copertina) seguita da qualsiasi carattere(* per l'estensione) e il nome id post seguito da immmaginedainserire (per l'immagine allegato)
$immaginecopertina = glob($percorsoimmagine . $datipost['codicepost'] . "_immagine_copertina.*");
$immagineallegato = glob($percorsoimmagine . $datipost['codicepost'] . "_immagine_dainserire.*");
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
    <script src="../js/jQuery.js">
    </script>
    <script src="../js/function.js">
    </script>
    <?php if (!empty($datipost['font'])) {
        //includo il link del font da google e poi assegno il font family agli elementi in pagina
    ?>

        <link id="blogfont" href="<?php //stampo il valore del link del font
                                    echo $datipost['font'] ?>" rel="stylesheet">
        <style>
            #grafica {
                font-family: "<?php //recupero il tipo di font, il colore e la dimensione della grafica selezionata
                                echo $datipost['nome_font'] ?>";
            }
        </style>
    <?php
    }
    ?>
</head>

<body>
    <?php
    include("nav.php");
    ?>
    <div class="columns body-columns" style="padding:20px; background-color: black; opacity: 2.0;">
        <div class="column is-half is-offset-one-quarter">
            <div class="card">
                <div class="header" style="padding:20px">
                    <div class="media">
                        <div class="media-left">
                            <?php
                            if (count($immaginecopertina) > 0) {
                            ?>
                                <figure class="image is-48x48"> <img src="<?php echo $immaginecopertina[0] ?>" /> </figure>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="media-content">
                            <p class="title is-4" style="font-family:<?php //recupero il tipo di font, il colore e la dimensione della gfrafica selezionata
                                                                        echo $datipost['nome_font'] ?>;font-size: <?php echo $datipost['dimensione_font'] ?>px !important ; color:<?php echo $datipost['colore'] ?> !important ;">
                                <?php echo $datipost['titolo']; ?>
                            </p>
                            <p class="subtitle is-6">Blog:
                                <a href="ricercablog.php?id_blog=<?php echo $datipost['codice_blog'] ?>">
                                    <?php echo $datipost['nomeblog']; ?>
                                </a>
                            </p>
                            <p class="subtitle is-7">categoria:
                                <?php echo $datipost['argomento_blog']; ?>
                            </p>
                            <?php if (!empty($datipost['argomento_post'])) { ?>
                                <p class="subtitle is-7">sottocategoria:
                                    <?php echo $datipost['argomento_post']; ?>
                                </p>
                            <?php
                            }
                            ?>
                            <p>a cura di <span class="tag is-rounded is-warning"> @<a href="profilo2.php?id=<?php echo $datipost['codice_autore'] ?>"><?php echo $datipost['username']; ?></a></span></p>
                            <style>
                                a {
                                    color: black;
                                }
                            </style>
                        </div>
                    </div>
                </div>
                <?php
                //stampo il bottone modifica solo se l'utente è loggato e l'utente sia o l'autore del post o il coautore del blog a cui appartiene il post
                if ($controllo_utente) {
                ?>
                    <div class="column is-1 is-offset-9">
                        <a style="border-style: solid ;border-width: thin; margin-bottom: 2%;" class="button is-small is-dark" href="crea.php?id_post=<?php echo $id_post ?>"> <span> modifica post</span></a>
                    </div>
                <?php
                }
                if (count($immagineallegato) > 0) {
                ?>
                    <div class="card-image" style="padding: 0px 5px 0px 5px;">
                        <figure class="image"> <img src="<?php echo $immagineallegato[0] ?>" /> </figure>
                    </div>
                <?php
                } else {
                ?>
                    <div class="card-image">
                        <figure class="image is-4by3"> <img src="../img/default.jpeg" /> </figure>
                    </div>
                <?php
                }
                ?>
                <div class="card-content">
                    <div class="content">
                        <?php //stampo il bottone  per mettere like solo se l'utente è loggato
                        if (!empty($datiutente)) {
                        ?>
                        <form action="" method="POST">
                            <p>
                            <a href="javascript:void(0)" onclick="like('<?php
                                                                        //passo i parametri alla funzione
                                                                        echo $datiutente['id'] ?>', '<?php echo $id_post ?>')" name="like" class="button is-small" style="margin:auto;"> <span class="icon is-medium">
                                    <img src="../img/like.png" />
                                </span> <strong>
                                    LIKE
                                </strong> <span id="n_like"> <?php echo $datipost['n_like']; ?></span>
                                <style>
                                    #n_like {
                                        margin-left: 10%;
                                        font-weight: bold;
                                    }
                                </style>
                            </a>
                            </p>
                        </form>
                    </div>
                </div>
            <?php //se invece l'utente non è loggato mostro solo il nlike

                        } else {
            ?>
                <p style="display: inline-block;"> <span class="icon is-medium">
                        <img src="../img/like.png" style="width:50%;" />
                    </span> <strong>
                        LIKE
                    </strong></p> <span id="n_like2"> <?php echo $datipost['n_like']; ?></span>
                <style>
                    #n_like2 {
                        font-weight: bold;
                    }
                </style>
            <?php
                        }
                    ?>
                    <div class="box">
                            <p style=" word-wrap: break-word;font-family:<?php //recupero il tipo di font, il colore e la dimensione della gfrafica selezionata
                                                                            echo $datipost['nome_font'] ?> !important;font-size: <?php echo $datipost['dimensione_font'] ?>px !important ; color:<?php echo $datipost['colore'] ?> !important ;">
                                <?php echo $datipost['testo']; ?>
                            </p>
                            <br>
                            <div style="text-align: center;">
                                <?php echo date('d/m/Y H:i', strtotime($datipost['data'])); ?>
                            </div>
                        </div>
                    <?php
                        if(!empty($datiutente)){
                ?> 
                <br>
                <br>
                <div class="card-footer">
                    <div class="column is-multiline">
                        <div class="column is-12">
                            <input name="commento" id="testo" class="input is-medium" maxlength="250" type="text" placeholder="aggiungi un commento . . ." required />
                        </div>
                    </div>
                    <a name="comment" href="javascript:void(0)" onclick="commento('<?php
                                                                                    //passo i parametri alla funzione
                                                                                    echo $datiutente['id'] ?>', '<?php echo $id_post ?>')" class="button is-small is-warning" style="word-wrap: break-word;margin:auto 20px;border-color:black;"> <span> Commenta</span> </a>
                </div>
                <?php
                        }
            ?>
            <div id="contenitore_commenti">
                <?php
                //recupero tutti i commenti del post
                $stampacommenti = "SELECT * FROM commento as c JOIN utente_registrato as u ON c.id_utente=u.id WHERE c.codice_post='{$id_post}' ORDER BY dataora desc, codicecomm desc ";
                //eseguo la query
                $querystampacommenti = $db->query($stampacommenti);
                $daticommento = $db->fetchAll($querystampacommenti);

                //faccio un ciclo per stampare i commenti con testo, username e dataora
                foreach ($daticommento as $commento) {
                ?>
                    <div class="commento">
                        <div class="divider"></div>
                        <style>
                            .divider {
                                background-color: #f7eac1;
                                height: 1px;
                                width: 100%;
                            }
                        </style>
                        <div class="autore">
                            <span class="tag is-rounded is-warning">@<?php echo $db->escape($commento['username']); ?></span>
                        </div>
                        <div class="buttons">
                            <?php
                            //controllo che l'utente che visualizza la pagina sia loggato e  quello che ha inserito il commento e, nel caso, mostro i bottoni x modificare o eliminare il commento
                            if (!empty($datiutente) && $commento['username'] == $datiutente['username']) {
                            ?> 
                            <a href="javascript:void(0)" onclick="modificacommento(<?php echo $commento['codicecomm']; ?>)" class="button is-small is-warning" style="margin-left:50%;" id="modifica-<?php echo $commento['codicecomm']; ?>"> modifica </a> 
                            <a href="javascript:void(0)" onclick="salvacommento(<?php echo $commento['codicecomm']; ?>)" class="button is-small is-warning" style="margin-left:50%; display:none;" id="salva-<?php echo $commento['codicecomm']; ?>"> salva </a> 
                            <a href="javascript:void(0)" onclick="eliminacommento(<?php echo $commento['codicecomm']; ?>)" class="button is-small is-warning" id="elimina-<?php echo $commento['codicecomm']; ?>"> elimina </a>
                            <?php
                            }
                            ?>
                        </div>
                        <div style=" word-wrap: break-word;" id="testo-<?php
                                                                        //ogni div dei commmenti avrà come id testo e l'id del commento
                                                                        echo $commento['codicecomm']; ?>">
                            <?php echo $commento['testo']; ?>
                        </div>
                        <div class="data">
                            <?php echo date('d/m/Y H:i', strtotime($commento['dataora'])); ?>
                        </div>
                    </div>
                <?php
                }
                ?>
                <style>
                    .data {
                        font-size: 10px;
                    }

                    .commento {
                        text-align: left;
                        padding: 0 20px 10px;
                    }

                    .autore {
                        padding-top: 10px;
                        display: inline-block;
                        width: 10%;
                    }
                    .buttons {
                        padding-top: 10px;
                        display: inline-block;
                        width: 89%;
                        text-align: right;
                    }
                </style>
            </div>
            </div>
        </div>
    </div>
    <div class="modal" id="e">
        <div class="modal-background"></div>
        <div class="modal-content">
            <article class="message is-dark">
                <div class="message-header">
                    <p>Attenzione</p>
                    <button class="delete" aria-label="delete" id="errore" onclick="$('#e').removeClass('is-active');"></button>
                </div>
                <div class="message-body">
                    <p class="content has-text-justified"> </p>
                </div>
            </article>
        </div>
    </div>
</body>

</html>