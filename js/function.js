function modificapost() {
	//dentro la var form ho tutto il form della pag, dollaro recupera l'elemento con l'id $form-post
	var form = $("#form-post");
	// serializzazione del form
	var data = form.serialize();
	//creo richiesta
	$.ajax({
		//passo i dati in post al file che richiamo
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		// gli passo la serializzazione del form
		data: data,
		//percorso al file
		url: "../html/modificapost.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella var datirisposta, datirisposta conterrà il risultato di modificapost.php
		success: function(datirisposta) {
			//se la modifica ha successo
			if (datirisposta['risultato'] == 'ok') {
				//faccio la submit del form 
				$('#form-post').submit();
			} else {
				//se l'utente fa un errore nella modifica del post, popolo il div con classe messagebody di blog.php con un messaggio contenente la lista degli errori che mi escono da modificablog.php e lo rendo visibile dando al contenitore del messaggio la classe is-active
				$(".message-body p").html(datirisposta['elencoerrori']);
				$(".modal").addClass("is-active");
			}
		},
		//nel caso di errore
		error: function(datierrore) {

		}
	});
}

function modificablog() {
	//dentro la var form ho tutto il form della pag, dollaro recupera l'elemento con l'id $form-blog
	var form = $("#form-blog");
	// serializzazione del form (i valori degli input del form li rendo in una stringa unica)
	var data = form.serialize();
	//creo richiesta
	$.ajax({
		//passo i dati in post al file che richiamo
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		// gli passo la serializzazione del form
		data: data,
		//percorso al file
		url: "../html/modificablog.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di modificapost.php
		success: function(datirisposta) {
			//se la modifica ha successo
			if (datirisposta['risultato'] == 'ok') {
				$('#form-blog').submit();
			} else {
				//se l'utente fa un errore nella modifica del blog, popolo il div con classe messagebody di blog.php con un messaggio contenente la lista degli errori che mi escono da modificablog.php e lo rendo visibile dando al contenitore del messaggio la classe is-active
				$(".message-body p").html(datirisposta['elencoerrori']);
				$(".modal").addClass("is-active");
			}
		},
		//nel caso di errore
		error: function(datierrore) {}
	});
}
// la funzione viene chiamata solo se viene cambiato il valore della select da predefinita a altro
function previewGrafica() {
	// recupero il valore della select grafica che è stato selezionato
	var select = $("#grafica").val();
	//creo richiesta
	$.ajax({
		//passo i dati in post al file che richiamo
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		// gli passo il valore che è stato selezionato nella select della grafica
		data: {
			grafica: select
		},
		//percorso al file
		url: "../html/recuperografica.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di recuperografica.php
		success: function(datirisposta) {
			//se la modifica ha successo
			if (datirisposta['risultato'] == 'ok') {
				//modifico campo href del link per il font della pagina blog.php e gli assegno quello ritornato da recuperografica.php
				$("#blogfont").attr('href', datirisposta['font']);
				//modifica css
				$("#previewgrafica").css({
					fontFamily: datirisposta['nome_font'],
					fontSize: datirisposta['dimensione_font'],
					color: datirisposta['colore']
				});
			}
		},
		//nel caso di errore
		error: function(datierrore) {}
	});
}

function like(id_utente, id_post) {

	//creo richiesta
	$.ajax({
		//passo i dati in post al file che richiamo
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		// a sinistra dei due punti ho il nome della var che mi ritroverò nel file php, mentre a destra ho il valore che assegno alla variabile
		data: {
			id_utente: id_utente,
			id_post: id_post
		},
		//percorso al file
		url: "../html/like.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di like.php
		success: function(datirisposta) {


			//prendo lo span con id n_like e con il metodo. html ci scrivo dentro il numero di like attuale che è tornato dalla query in like.php
			$("#n_like").html(datirisposta['n_like']);


		},
		//nel caso di errore
		error: function(datierrore) {}
	});

}

function commento(id_utente, id_post) {
	// recupero il valore del testo che è stato inserito nel commento
	var testo = $("#testo").val();
	//controllo che il testo non sia vuoto e abbia almeno 1 carattere inserito
	if (testo != '' || testo.length >= 1) {
		//creo richiesta
		$.ajax({
			//passo i dati in post al file che richiamo
			type: "POST",
			//definisco il tipo di dato che mi ritorna dal file php
			dataType: "json",
			// a sinistra dei due punti ho il nome della var che mi ritroverò nel file php, mentre a destra ho il valore che assegno alla variabile
			data: {
				id_utente: id_utente,
				id_post: id_post,
				testo: testo
			},
			//percorso al file
			url: "../html/commenti.php",
			//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di commenti.php
			success: function(datirisposta) {

				//racchiudo div etc.. dentro una lunga stringa facendo la concatenazione di stringhe; metto un + alla riga 135 così che js capisce che quella riga è il proseguimento della stringa della var alla riga 134
				var html = "<div class=\"commento\"><div class=\"autore\"><span class=\"tag is-rounded is-warning\">@" + datirisposta['username'] + "</span></div><div class=\"buttons\"> <a href=\"javascript:void(0)\" onclick=\"modificacommento(" + datirisposta['codicecomm'] + ")\" class=\"button is-small is-warning\" style=\"margin-left:50%;\" id=\"modifica-" + datirisposta['codicecomm'] + "\"> modifica </a><a href=\"javascript:void(0)\" onclick=\"salvacommento(" + datirisposta['codicecomm'] + ")\" class=\"button is-small is-warning\" style=\"margin-left:50%; display:none;\" id=\"salva-" + datirisposta['codicecomm'] + "\"> salva  </a><a href=\"javascript:void(0)\" onclick=\"eliminacommento(" + datirisposta['codicecomm'] + ")\" class=\"button is-small is-warning\"> elimina  </a></div><div id=\"testo-" + datirisposta['codicecomm'] + "\" style=\"word-wrap: break-word; \">" + datirisposta['testo'] + "</div><div class=\"data\">" + datirisposta['dataora'] + "</div></div>";
				//setto a vuoto il valore dell'input e svuoto il campo commento
				$("#testo").val("");

				//prendo elemento con id contenitore_commenti e con il metodo. prepend ci attacco il contenuto della variabile come primo elemento in modo da averli in ordine cronologico
				$("#contenitore_commenti").prepend(html);


			},
			//nel caso di errore
			error: function(datierrore) {}
		});
	} else {
		//gestisco gli errori
		$(".message-body p").html("Inserisci un testo");
		$(".modal").addClass("is-active");
	}

}

function modificacommento(id_commento) {
	// recupera il testo del commento che sto modificando e l'id del commento
	var testo = $("#testo-" + id_commento).html();

	////stampo nell'html una textarea con il testo del commento già inserito 
	var html = '<input type="text" id="nuovotesto" maxlength="250" value="' + testo + '"/> ';


	//prendo elemento con id testo e con il metodo. html sostituisco l'html all'interno di quel contenitore con quello nuovo
	$("#testo-" + id_commento).html(html);
	//nascondo il bottone con id modfica
	$("#modifica-" + id_commento).hide();
	//mostro il bottone con id salva
	$("#salva-" + id_commento).show();
}

function salvacommento(id_commento) {
	// recupero il valore del testo che è stato inserito nel commento
	var testo = $("#nuovotesto").val();

	//creo richiesta
	$.ajax({
		//passo i dati in post al file che richiamo
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		// a sinistra dei due punti ho il nome della var che mi ritroverò nel file php, mentre a destra ho il valore che assegno alla variabile
		data: {
			id_commento: id_commento,
			testo: testo
		},
		//percorso al file
		url: "../html/aggiornacommento.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di aggiornacommento.php
		success: function(datirisposta) {

			//prendo elemento con id testo (che racchiude l'input) e con il metodo. html sostituisco l'html all'interno di quel contenitore con quello nuovo salvando il nuovo testo del commento
			$("#testo-" + id_commento).html(testo);
			//mostro il bottone con id modfica
			$("#modifica-" + id_commento).show();
			//nascondo il bottone con id salva
			$("#salva-" + id_commento).hide();




		},
		//nel caso di errore
		error: function(datierrore) {}
	});
}

function eliminacommento(id_commento) {
	//creo richiesta
	$.ajax({
		//passo i dati in post al file che richiamo
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		// a sinistra dei due punti ho il nome della var che mi ritroverò nel file php, mentre a destra ho il valore che assegno alla variabile
		data: {
			id_commento: id_commento
		},
		//percorso al file
		url: "../html/eliminacommento.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di eliminacommento.php
		success: function(datirisposta) {
			//ricarico la pagina dopo aver eliminato il commento
			window.location.reload();

		},
		//nel caso di errore
		error: function(datierrore) {}
	});
}

function logout() {

	//creo richiesta
	$.ajax({
		type: "POST",
		//definisco il tipo di dato che mi ritorna dal file php
		dataType: "json",
		data: {

		},
		//percorso al file
		url: "../html/logout.php",
		//se la chiamata ha successo salvo la risposta della richiesta nella vra datirisposta, datirisposta conterrà il risultato di logout.php
		success: function(datirisposta) {
			//ricarico la pagina dopo aver fatto il logout
			window.location = "index.php";

		},
		//nel caso di errore
		error: function(datierrore) {

		}
	});
}

$(function(){
	$('#datiUtente input').on('click',function(){
		$('#modifica-dati-utente').removeClass('disableClick');
	});
})