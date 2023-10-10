const fs = require('fs');

// Leggi il contenuto del file input.txt
fs.readFile('output_definitivoConSpazi.txt', 'utf8', (err, data) => {
  if (err) {
    console.error('Errore nella lettura del file:', err);
    return;
  }

  // Rimuovi eventuali linee vuote dal testo
  const lines = data.split('\n').filter(line => line.trim() !== '');

  // Crea un array di oggetti per i dati delle righe
  const tableData = [];
  lines.forEach(line => {
    if (line.startsWith('//')) {
      // Crea un oggetto con solo il campo Commento
      const commento = line.substring(2).trim();
      tableData.push({
        Commento: commento
      });
    } else {
      const startsWithDoubleSpace = line.startsWith('  ');
      const columns = line.split(/\s{2,}/); // Dividi la riga in colonne utilizzando 2 o più spazi come separatore

      let codice = startsWithDoubleSpace ? previousCodice : columns[0].trim();
      let descrizione = startsWithDoubleSpace ? previousDescrizione : columns[1].trim();
      let UM = startsWithDoubleSpace ? previousUM : columns[2].trim();
      const otherColumns = startsWithDoubleSpace ? columns.slice(1) : columns.slice(3);

      if (!startsWithDoubleSpace) {
        previousCodice = codice;
        previousDescrizione = descrizione;
        previousUM = UM;
      }

      const [anno, giacProg, giacAnno, costoMedio, valoreAnno, valoreProg] = otherColumns;

      tableData.push({
        Codice: codice,
        Descrizione: descrizione,
        UM: UM,
        Anno: anno,
        'Giac. Prog.': giacProg,
        'Giac. Anno': giacAnno,
        'Costo Medio': costoMedio,
        'Valore Anno': valoreAnno,
        'Valore Prog.': valoreProg
      });
    }
  });

  // Converti l'array di oggetti in formato JSON
  const jsonData = JSON.stringify(tableData, null, 2);

  // Scrivi il contenuto nel file dati.json
  fs.writeFile('dati.json', jsonData, 'utf8', (err) => {
    if (err) {
      console.error('Errore nella scrittura del file:', err);
      return;
    }

    console.log('Il file dati.json è stato creato correttamente.');
  });
});
