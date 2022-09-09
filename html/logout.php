<?php
//se trovo sessione già attiva riprendo quella
session_start();
session_destroy(); //svuota la sessione e slogga l'utente
echo json_encode(['ok'])

?>
