<?php
// Percorso di destinazione per il salvataggio del file
$percorsoDestinazione = 'nome_cartella/';

// Ottieni i dati del file caricato
$nomeFile = $_FILES['file']['name'];
$percorsoTemporaneo = $_FILES['file']['tmp_name'];

// Salva il file nel percorso di destinazione
if (move_uploaded_file($percorsoTemporaneo, $percorsoDestinazione . $nomeFile)) {
    echo "Il file è stato caricato con successo.";
} else {
    echo "Si è verificato un errore durante il caricamento del file.";
}
?>
