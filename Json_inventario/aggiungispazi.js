const fs = require('fs');

function rewriteFile(inputFile, outputFile) {
  try {
    const data = fs.readFileSync(inputFile, 'utf8');
    const lines = data.split('\n');

    const rewrittenLines = lines.map((line) => {
      let rewrittenLine = line.slice(0, 59) + ' ' + line.slice(59, 62) + ' ' + line.slice(62);
      return rewrittenLine;
    });

    const outputData = rewrittenLines.join('\n');
    fs.writeFileSync(outputFile, outputData);
    console.log('File riscritto con successo!');
  } catch (err) {
    console.error('Si è verificato un errore:', err);
  }
}

// Utilizzo del programma
const inputFile = 'output_definitivo.txt';
const outputFile = 'output_definitivoConSpazi.txt';
rewriteFile(inputFile, outputFile);
