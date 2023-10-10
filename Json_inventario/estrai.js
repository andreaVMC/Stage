const fs = require('fs');

function filterFile(inputFile, outputFile) {
  try {
    const data = fs.readFileSync(inputFile, 'utf8');
    const lines = data.split('\n');

    const filteredLines = lines.filter((line) => {
      const firstChar = line.trim().charAt(0);
      const isDigit = !isNaN(firstChar);
      const isGroup = line.trim().startsWith('Gruppo');
      const hasOnlyValidCharacters = /^[0-9a-zA-Z\s]+$/.test(line.trim());
      return isDigit || isGroup || hasOnlyValidCharacters;
    });

    const outputData = filteredLines.join('\n');
    fs.writeFileSync(outputFile, outputData);
    console.log('File filtrato con successo!');
  } catch (err) {
    console.error('Si è verificato un errore:', err);
  }
}

// Utilizzo del programma
const inputFile = 'inventario.txt';
const outputFile = 'input.txt';
filterFile(inputFile, outputFile);
