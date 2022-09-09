<?php 
// .. = fa riferimento al tornare indietro di una radice dell'albero; . fa riferimento alla stessa cartella
include '../classes/db.php';
//creo un array associativo per identificare le variabili
$array_config = array (
	"DB" => "crea_blog", //database di MariaDB
	"username" => "root", //username e password di PHPmyadmin
	"password" => "", 
	"urlsito" => "www.blog.it", //url del sito web
	"titolo" => "blog", //titolo del blog
	"email" => "gennymiola99@gmail.com", //email dell'admin
	"nomeserver" => "localhost"
	);
// creo una classe per interagire con il database
$db = new db($array_config["nomeserver"], $array_config["username"], $array_config["password"],$array_config["DB"]);
session_start(); #quando includo il file il comando avvia una sessione php
    $_SESSION['ID'] = session_id(); #salvo id della sessione
  //salvo il percorso per il salvataggio delle immagini
$percorsoimmagine="../images/";
$percorsoimmagineblog="../imgblog/";
$percorsoimgutente="../imguser/";
//fisso la soglia per i vari livelli
$livello1=5;
$livello2=8;
$livello3=10;
//con un'espressione regolare controllo il formato dell'email
$controllo_email="/[A-z0-9\.\+_-]+@[A-z0-9\._-]+\.[A-z]{2,6}$/";
$controllo_telefono="/^((00|\+)39??)??(05|3)\d{2}\d{6,7}$/";
$controllo_password="/^(?=.*\d)(?=.*[#$@!%&*?._])[A-Za-z\d#$@!%&*?._]{8,}$/";
function calcolalivelloutente($id_utente){
	//ricerco la variabili all'interno del file
	global $db, $livello1, $livello2, $livello3;
	 //conto i post e i commenti dell'utente con una query aggregata
    $livelloutente="SELECT sum(numero) as numeropostcommenti FROM (SELECT count(*) as numero FROM post_esempio WHERE codice_autore='{$id_utente}'
     UNION ALL
     SELECT count(*) as numero FROM commento WHERE id_utente='{$id_utente}') as livelloutente
     ";

     #eseguo la query
    $querylivelloutente= $db->query($livelloutente);
     $liv_utente= $db->fetchArray($querylivelloutente);
        //salvo il numero di commenti e post dell'utente, aggiungo a array l'elemtno numeropostcommenti
      $_SESSION['logged_user']['numeropostcommenti']=$liv_utente['numeropostcommenti'];
      //quando supero livello 2 e sono al tre 
     if($liv_utente['numeropostcommenti']>=$livello3){
      //salvo il livello dell'utente
      $_SESSION['logged_user']['livelloutente']=3;
     }
     elseif ($liv_utente['numeropostcommenti']>=$livello2){
      //salvo il livello dell'utente
      $_SESSION['logged_user']['livelloutente']=2;
    
     } elseif ($liv_utente['numeropostcommenti']>=$livello1){
      //salvo il livello dell'utente
      $_SESSION['logged_user']['livelloutente']=1;
         
     } else {
      //salvo il livello dell'utente
      $_SESSION['logged_user']['livelloutente']=0;
        
     }
 };