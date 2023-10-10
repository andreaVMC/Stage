const fs = require('fs');

function filterFile(inputFile, outputFile) {
  try {
    const data = fs.readFileSync(inputFile, 'utf8');
    const lines = data.split('\n');

    const filteredLines = lines.filter((line) => {
      const filteredLine = line.trim().replace(/[^a-zA-Z0-9\s.,;?!]/g, '');
      return filteredLine !== '';
    });

    const outputData = filteredLines.join('\n');
    fs.writeFileSync(outputFile, outputData);
    console.log('File filtrato con successo!');
  } catch (err) {
    console.error('Si è verificato un errore:', err);
  }
}

// Utilizzo del programma
const inputFile = 'input.txt';
const outputFile = 'output_senzaCaratteriSpeciali.txt';
filterFile(inputFile, outputFile);
