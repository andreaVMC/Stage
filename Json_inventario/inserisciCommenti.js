const fs = require('fs');

function rewriteFile(inputFile, outputFile) {
  try {
    const data = fs.readFileSync(inputFile, 'utf8');
    const lines = data.split('\n');

    const rewrittenLines = lines.map((line) => {
      if (line.trim().startsWith('Gruppo')) {
        return `//${line}`;
      }
      return line;
    });

    const outputData = rewrittenLines.join('\n');
    fs.writeFileSync(outputFile, outputData);
    console.log('File riscritto con successo!');
  } catch (err) {
    console.error('Si è verificato un errore:', err);
  }
}

// Utilizzo del programma
const inputFile = 'output_senzaCaratteriSpeciali.txt';
const outputFile = 'output_definitivo.txt';
rewriteFile(inputFile, outputFile);
