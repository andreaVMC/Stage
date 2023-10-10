function checkInputs() {
    const nameInput = document.getElementById('nome');
    const cognomeInput = document.getElementById('cognome');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const emailInput = document.getElementById('email');
    const submitBtn = document.getElementById('sign_in');
    if (nameInput.value !== '' && emailInput.value!=''&& cognomeInput.value !== '' && usernameInput.value !== '' && passwordInput.value !== '') {
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