const diapositivasContainer = document.getElementById('diapositivas');
const diapositivaTitulo = document.getElementById('d_titulo_template');
const diapositivaTituloTexto = document.getElementById('d_titulo_texto_template');
const diapositivaTituloTextoImagen = document.getElementById('d_titulo_texto_imagen_template');
const diapositivaPregunta = document.getElementById('d_pregunta_template');

// Este contador incrementará con cada nueva diapositiva
let newDiapositivaId = 1;

// Función para generar un ID único para nuevas diapositivas
const generateNewDiapositivaId = () => `new-${newDiapositivaId++}`;

// Crea y añade una nueva diapositiva de tipo "título" al contenedor principal.
const newTipoTitulo = (tipo) => {
    const diapositiva = diapositivaTitulo.content.cloneNode(true);
    const diapositivaContainer = diapositiva.querySelector('.d-container');
    const tempId = generateNewDiapositivaId();
    const idTemporalConTipo = `${tempId}-${tipo}`; // Añade el tipo después del ID temporal
    diapositivaContainer.setAttribute('data-id', idTemporalConTipo);
    diapositivaContainer.querySelector('input[type="text"]').name = `d_titulo_${idTemporalConTipo}`;

    diapositivasContainer.append(diapositivaContainer);

    ocultarTodasLasDiapositivas();
    mostrarDiapositiva(idTemporalConTipo);
    ordenLista.push(idTemporalConTipo);
    actualizarListaDiapositivas();

    mostrarDiapositiva(numDiapositivas - 1);
};

let numDiapositivas = document.getElementById('diapositivas');
numDiapositivas = numDiapositivas.getAttribute('lastDiapositivaId');
// Comprobamos si estamos creando una diapositiva nueva o editando una existente.
if (numDiapositivas === '0') {
    newTipoTitulo('titulo');
    numDiapositivas = 1;
} else {
    numDiapositivas = parseInt(numDiapositivas) - 1;
}

// Crea y añade una nueva diapositiva que tiene un título y un área de texto.
const newTipoContenido = (tipo) => {
    const diapositiva = diapositivaTituloTexto.content.cloneNode(true);
    const diapositivaContainer = diapositiva.querySelector('.d-container');
    const tempId = generateNewDiapositivaId();
    const idTemporalConTipo = `${tempId}-${tipo}`; // Añade el tipo después del ID temporal
    diapositivaContainer.setAttribute('data-id', idTemporalConTipo);
    diapositivaContainer.querySelector('input[type="text"]').name = `d_titulo_${idTemporalConTipo}`;
    diapositivaContainer.querySelector('textarea').name = `d_contenido_${idTemporalConTipo}`;

    diapositivasContainer.append(diapositivaContainer);

    ocultarTodasLasDiapositivas();
    mostrarDiapositiva(idTemporalConTipo);
    ordenLista.push(idTemporalConTipo);
    actualizarListaDiapositivas();

    mostrarDiapositiva(numDiapositivas - 1);
};

// Crea y añade una nueva diapositiva que tiene un título, un área de texto y una imagen.
const newTipoImagen = (tipo) => {
    const diapositiva = diapositivaTituloTextoImagen.content.cloneNode(true);
    const diapositivaContainer = diapositiva.querySelector('.d-containerImagen');
    const tempId = generateNewDiapositivaId();
    const idTemporalConTipo = `${tempId}-${tipo}`; // Añade el tipo después del ID temporal
    diapositivaContainer.setAttribute('data-id', idTemporalConTipo);

    diapositivaContainer.querySelector('input[type="text"]').name = `d_titulo_${idTemporalConTipo}`;
    diapositivaContainer.querySelector('textarea').name = `d_contenido_${idTemporalConTipo}`;
    diapositivaContainer.querySelector('input[type="file"]').name = `d_imagen_${idTemporalConTipo}`;

    diapositivasContainer.append(diapositivaContainer);

    ocultarTodasLasDiapositivas();
    mostrarDiapositiva(idTemporalConTipo);
    ordenLista.push(idTemporalConTipo);
    actualizarListaDiapositivas();

    mostrarDiapositiva(numDiapositivas - 1);
};

// Crea y añade una nueva diapositiva de tipoPregunta.
const newTipoPregunta = (tipo) => {
    const diapositiva = document.getElementById('d_pregunta_template').content.cloneNode(true);
    const diapositivaContainer = diapositiva.querySelector('.d-container');
    const tempId = generateNewDiapositivaId();
    const idTemporalConTipo = `${tempId}-${tipo}`; // Añade el tipo después del ID temporal

    diapositivaContainer.setAttribute('data-id', idTemporalConTipo);
    diapositivaContainer.querySelector('input[placeholder="Haz click para añadir un título..."]').name = `d_titulo_${idTemporalConTipo}`;
    diapositivaContainer.querySelector('textarea[placeholder="Introduce tu pregunta:"]').name = `d_pregunta_${idTemporalConTipo}`;

    const respuestaInputs = diapositivaContainer.querySelectorAll('input[placeholder^="Respuesta"]');
    respuestaInputs.forEach((input, index) => {
        input.name = `d_respuesta_${String.fromCharCode(65 + index)}_${idTemporalConTipo}`;
    });

    diapositivaContainer.querySelector('select').name = `d_respuesta_correcta_${idTemporalConTipo}`;

    diapositivasContainer.append(diapositivaContainer);

    ocultarTodasLasDiapositivas();
    mostrarDiapositiva(idTemporalConTipo);
    ordenLista.push(idTemporalConTipo);
    actualizarListaDiapositivas();

    mostrarDiapositiva(idTemporalConTipo);
};

//Funcion para mostrar la confirmación del feedback
const mostrarConfirmacionNuevaDiapositiva = (event, tipo) => {
    event.preventDefault();
    const dialog = document.getElementById('confirmarGuardar');
    const overlay = document.getElementById('overlay');

    // Recordamos el tipo de diapositiva que se va a agregar
    tipoDiapositiva = tipo;

    // Muestra el diálogo
    dialog.style.display = 'block';
    overlay.style.display = 'block';

    // Agrega un event listener al botón "Aceptar" en el diálogo
    const btnAceptar = document.getElementById('btn-aceptar');
    btnAceptar.addEventListener('click', () => {
        // Oculta el diálogo
        dialog.style.display = 'none';
        overlay.style.display = 'none';

        // Agregamos la Diapositiva seleccionada según el tipo recordado
        if (tipoDiapositiva === "titulo") {
            newTipoTitulo('titulo');
        } else if (tipoDiapositiva === "tituloTexto") {
            newTipoContenido('contenido');
        } else if (tipoDiapositiva === "tituloTextoImagen") {
            newTipoImagen('imagen');
        } else if (tipoDiapositiva === "pregunta") {
            newTipoPregunta('pregunta');
        }

        // Reseteamos el tipo de diapositiva
        tipoDiapositiva = '';
    });

    // Agrega un event listener al botón "Cancelar" en el diálogo
    const btnCancelar = document.getElementById('btn-cancelar');
    btnCancelar.addEventListener('click', () => {
        // Oculta el diálogo sin llamar a la función
        dialog.style.display = 'none';
        overlay.style.display = 'none';

        if (tipoDiapositiva) {
            // Si hay un tipo de diapositiva recordado, inserta en la base de datos pero sin guardar los cambios
            if (tipoDiapositiva === 'titulo') {
                newTipoTitulo('titulo');
            } else if (tipoDiapositiva === 'tituloTexto') {
                newTipoContenido('contenido');
            } else if (tipoDiapositiva === 'tituloTextoImagen') {
                newTipoImagen('imagen');
            } else if (tipoDiapositiva === 'pregunta') {
                newTipoPregunta('pregunta');
            }
        }

        // Reseteamos el tipo de diapositiva
        tipoDiapositiva = '';
    });

    return false; // Evita que el evento del enlace se propague
};

//Llamada a la función que muestra el feedback pasando el tipo de diapositiva "Titulo"
const mostrarConfirmacionNuevaDiapositivaTitulo = (event) => {
    mostrarConfirmacionNuevaDiapositiva(event, 'titulo');
};

//Llamada a la función que muestra el feedback pasando el tipo de diapositiva "Titulo + Texto"
const mostrarConfirmacionNuevaDiapositivaTituloTexto = (event) => {
    mostrarConfirmacionNuevaDiapositiva(event, 'tituloTexto');
};

//Llamada a la función que muestra el feedback pasando el tipo de diapositiva "Titulo + Texto + Imagen"
const mostrarConfirmacionNuevaDiapositivaTituloTextoImagen = (event) => {
    mostrarConfirmacionNuevaDiapositiva(event, "tituloTextoImagen");
};

//Llamada a la función que muestra el feedback pasando el tipo de diapositiva "Pregunta + Respuesta"
const mostrarConfirmacionNuevaDiapositivaPregunta = (event) => {
    mostrarConfirmacionNuevaDiapositiva(event, "pregunta");
};


// Evento para cerrar desplegables al hacer click fuera del mismo.
document.addEventListener('click', (event) => {
    const isClickInsideDropdown = !!event.target.closest('.dropdown');
    const isClickOnDropdownContent = !!event.target.closest('.dropdown-content');

    if (!isClickInsideDropdown || isClickOnDropdownContent) {
        closeAllDropdowns();
    }
});

// Cierra todos los desplegables.
const closeAllDropdowns = () => {
    const allDropdownContents = document.querySelectorAll('.dropdown-content');
    allDropdownContents.forEach((content) => {
        content.style.display = 'none';
    });
};

// Abre el desplegable cerrando los demás.
const showDropdown = (event) => {
    const dropdownButton = event.target.closest('.dropdown');
    const dropdownContent = dropdownButton.querySelector('.dropdown-content');

    closeAllDropdowns();
    dropdownContent.style.display = 'block';

    event.stopPropagation();
};

// Funcion que aplica el tema claro.
inputTema = document.querySelector('input[name="tema"]');
const setClaro = () => {
    diapositivasContainer.setAttribute('tema', 'claro');
    inputTema.value = 'claro';

    const whiteListItems = document.querySelector('.white-list-items');
    if (whiteListItems) {
        whiteListItems.style.backgroundColor = 'white';
        whiteListItems.style.color = 'black';
        const listItems = whiteListItems.querySelectorAll('li');
        listItems.forEach(item => {
            item.style.color = 'black';
        });
    }
};

// Funcion que aplica el tema oscuro.
const setOscuro = () => {
    diapositivasContainer.setAttribute('tema', 'oscuro');
    inputTema.value = 'oscuro';

    const whiteListItems = document.querySelector('.white-list-items');
    if (whiteListItems) {
        whiteListItems.style.backgroundColor = '';
        whiteListItems.style.color = '';
        const listItems = whiteListItems.querySelectorAll('li');
        listItems.forEach(item => {
            item.style.color = '';
        });
    }
};

const updateList = () => {
    document.querySelector('#ordenDiapositivas').value = ordenLista; 
}

const previewForm = document.querySelector('#preview_form');
previewForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const inputDiapositivaId = previewForm.querySelector('input[name="diapositiva_id"]');
    let diapositivaActual = document.querySelector('.d-container[style*="display: flex"] input');
    diapositivaActual = diapositivaActual.name.split('_');
    diapositivaActual = diapositivaActual[diapositivaActual.length - 1];
    inputDiapositivaId.value = diapositivaActual;
    e.target.submit();
});

const generalForm = document.querySelector('#data_p');
const publicarButton = document.querySelector('#publicar_button button[form="data_p"]');
publicarButton.addEventListener('click', (e) => {
    e.preventDefault();
    const inputUrl = publicarButton.parentElement.querySelector('input');
    inputUrl.value = 'true';
    updateList();
    generalForm.submit();
});

const copyButton = document.querySelector('#copyUrlButton');

copyButton.addEventListener('click', (e) => {
    let url = document.querySelector('input[name="url"]').value;
    url = 'http://localhost:4200/src/views/preview.php?url=' + url;
    window.open(url, '_blank')
    e.target.src = '../assets/icons/checkCopy.svg';
    setTimeout(() => {
        e.target.src = '../assets/icons/copy.svg';
    }, 1000);
});

// Evento que establece true el input modifyPin, para indicar que se quiere modificar el PIN.
const pin_button = document.querySelector('#pin_button');
pin_button.addEventListener('click', (e) => {
    e.preventDefault();
    const modifyPin = pin_button.querySelector('input');
    modifyPin.value = 'true';
    updateList();
    generalForm.submit();
});

// Función para cerrar los dialog de PIN.
const closePinDialog = (e) => {
    const dialog = e.target.parentElement;
    dialog.style.display = 'none';
}