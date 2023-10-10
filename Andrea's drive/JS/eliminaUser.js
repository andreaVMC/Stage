function checkInputs() {
    const nameInput = document.getElementById('username');
    const submitBtn = document.getElementById('elimina');
    if (nameInput.value !== '') {
      submitBtn.classList.remove('disabled');
    } else {
      submitBtn.classList.add('disabled');
    }
}

// Aggiungi event listener agli input per verificare i campi
const inputs = document.querySelectorAll('input');
inputs.forEach(input => {
    input.addEventListener('input', checkInputs);
});
//------------