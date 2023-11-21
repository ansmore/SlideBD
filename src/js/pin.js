const pin_button = document.querySelector('#pin_button');
pin_button.addEventListener('click', (e) => {
    e.preventDefault();
    const modifyPin = pin_button.querySelector('input');
    modifyPin.value = 'true';
    generalForm.submit();
});

// Función para cerrar los dialog de PIN.
const closePinDialog = (e) => {
    const dialog = e.target.parentElement;
    dialog.style.display = 'none';
}