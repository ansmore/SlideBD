let ordenLista = []; // Esta lista mantendrá el orden actualizado de los elementos

// Función para ocultar todas las diapositivas
const ocultarTodasLasDiapositivas = () => {
  const todasLasDiapositivas = document.querySelectorAll('.d-container, .d-containerImagen');
  todasLasDiapositivas.forEach(diapositiva => {
    diapositiva.style.display = 'none';
  });
};

// Función para mostrar una diapositiva específica por ID
const mostrarDiapositiva = (id) => {
  // Selecciona ambas clases con una coma separando los selectores
  const diapositivaParaMostrar = document.querySelector(`.d-container[data-id="${id}"], .d-containerImagen[data-id="${id}"]`);
  if (diapositivaParaMostrar) {
    diapositivaParaMostrar.style.display = 'flex'; // Asegúrate de que esta sea tu clase para mostrar elementos
  }
};

// Función para hacer los elementos de la lista arrastrables
const hacerElementosArrastrables = () => {
  const listaDiapositivas = document.querySelector('.white-list-items');
  let elementoArrastrado = null;

  listaDiapositivas.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  });

  listaDiapositivas.addEventListener('drop', (e) => {
    e.preventDefault();
    if (elementoArrastrado && e.target.tagName === 'LI') {
      const targetIndex = [...listaDiapositivas.children].indexOf(e.target);
      const draggedIndex = [...listaDiapositivas.children].indexOf(elementoArrastrado);
      if (draggedIndex < targetIndex) {
        listaDiapositivas.insertBefore(elementoArrastrado, e.target.nextSibling);
      } else {
        listaDiapositivas.insertBefore(elementoArrastrado, e.target);
      }
      ordenLista = [...listaDiapositivas.children].map(li => li.getAttribute('data-id'));

      // Después de agregar todos los elementos li a la lista, verifica si hay más de 11 elementos
      if (ordenLista.length > 11) {
        const listaContainer = document.querySelector('.white-list-items-container');
        listaContainer.style.maxHeight = '400px';
        listaContainer.style.overflowY = 'auto';
      }
    }
  });

  listaDiapositivas.querySelectorAll('li').forEach(li => {
    li.setAttribute('draggable', true);

    li.addEventListener('dragstart', (e) => {
      elementoArrastrado = li;
      e.dataTransfer.setData('text/plain', li.getAttribute('data-id'));
    });

    li.addEventListener('dragend', () => {
      elementoArrastrado = null;
    });
  });
};

// Función para actualizar la lista de diapositivas
const actualizarListaDiapositivas = () => {
  const listaDiapositivas = document.querySelector('.white-list-items');
  listaDiapositivas.innerHTML = '';

  // Recorremos todas las diapositivas y les asignamos un ID único
  const todasLasDiapositivas = document.querySelectorAll('.d-container input[type="text"].focus, .d-containerImagen input[type="text"].focus');
  todasLasDiapositivas.forEach(input => {
    const id = input.closest('.d-container, .d-containerImagen').getAttribute('data-id');
    
    // Añadimos un condicional para verificar si el input es el primer input dentro de su respectivo div .respuesta
    if (input === input.closest('.d-container, .d-containerImagen').querySelector('input[type="text"]')) {
      const nuevoLi = document.createElement('li');
      nuevoLi.textContent = input.value.trim() || `Título`;
      nuevoLi.setAttribute('data-id', id); // Asignamos la ID generada directamente
      listaDiapositivas.appendChild(nuevoLi);
    }
  });

  hacerElementosArrastrables(); // Hace que los nuevos elementos li sean arrastrables

  // Asigna eventos de clic a los elementos de la lista
  listaDiapositivas.querySelectorAll('li').forEach(li => {
    li.addEventListener('click', () => {
      const id = li.getAttribute('data-id');
      ocultarTodasLasDiapositivas();
      mostrarDiapositiva(id);
      actualizarListaDiapositivas();
    });
  });
};

// Función para inicializar la vista de diapositivas
const inicializarDiapositivas = () => {
  ocultarTodasLasDiapositivas();
  actualizarListaDiapositivas();

  // Muestra la primera diapositiva por defecto
  const firstSlideId = document.querySelector('.d-container').getAttribute('data-id');
  mostrarDiapositiva(firstSlideId);
};

// Evento para inicializar la vista cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
  // Aquí recuperas el valor del tema desde el atributo 'tema' del contenedor 'diapositivas'
  const temaActual = diapositivasContainer.getAttribute('tema');
  if (temaActual === 'claro') {
    setClaro(); // Llama a la función que ajusta los estilos para el tema claro
  }
  
  inicializarDiapositivas();
  // Actualiza el orden inicial de la lista
  ordenLista = [...document.querySelectorAll('.white-list-items li')].map(li => li.getAttribute('data-id'));

  document.getElementById('data_p').addEventListener('submit', function() {
    document.getElementById('ordenDiapositivas').value = ordenLista.join(',');
});
});

