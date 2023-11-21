const backButton = document.querySelector('#diapositivaAnterior');
const nextButton = document.querySelector('#diapositivaPosterior');
let diapositivaActual;

const siguiente = () => {
    if (diapositivaActual.nextElementSibling.className.includes('d-container')) {
        try {
            diapositivaActual.style.display = 'none';

            diapositivaActual = diapositivaActual.nextElementSibling;
            diapositivaActual.style.display = 'flex';

            backButton.style.display = 'flex';
        } catch (error) {}
    }
};

const anterior = () => {
    if (diapositivaActual.previousElementSibling.className.includes('d-container')) {
        try {
            diapositivaActual.style.display = 'none';

            diapositivaActual = diapositivaActual.previousElementSibling;
            diapositivaActual.style.display = 'flex';
        } catch (error) {}
    }
};

const activarPantallaCompleta = () => {
    const elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    }
};

const firstDiapositiva = document.querySelector('#diapositivas').getAttribute('firstDiapositiva');
document.addEventListener('DOMContentLoaded', () => {
    if (firstDiapositiva) {
        const primeraDiapositiva = document.querySelector(
            '.d-container h1.d_titulo_' + firstDiapositiva
        ).parentElement;
        diapositivaActual = primeraDiapositiva;
        primeraDiapositiva.style.display = 'flex';
        backButton.style.display = 'flex';
    } else {
        const primeraDiapositiva = document.querySelector('.d-container');
        diapositivaActual = primeraDiapositiva;
        primeraDiapositiva.style.display = 'flex';
    }
});

document.addEventListener('fullscreenchange', (event) => {
    if (document.fullscreenElement) {
        document.querySelector('#diapositivaPosterior img').style.display = 'none';
    } else {
        document.querySelector('#diapositivaPosterior img').style.display = '';
    }
});

const miniaturasContainer = document.querySelector('#miniaturas');
miniaturasContainer.addEventListener('click', (e) => {
    let diapositivaToShow = e.target;
    while (!diapositivaToShow.className.includes('d-miniatura')) {
        diapositivaToShow = diapositivaToShow.parentElement;
    }
    diapositivaToShow = diapositivaToShow.getAttribute('id_diapositiva');
    diapositivaActual.style.display = 'none';
    diapositivaActual = document.querySelector(`.d-container:has(h1.d_titulo_${diapositivaToShow})`);
    diapositivaActual.style.display = 'flex';
})

const checkAnswer = (event) => { 
    const answers = event.target.parentElement;
    const correctAnswer = answers.querySelector('h2').getAttribute('rp');
    const answer = answers.querySelector('input:checked');
    if (answer.value === correctAnswer.toLowerCase()) {
        answer.parentElement.style.backgroundColor = 'green';
    } else {
        answer.parentElement.style.backgroundColor = 'red';
    }
}

