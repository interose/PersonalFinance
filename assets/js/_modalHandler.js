const defaultTitleEl = document.getElementById('modal_default_title');
const defaultContentEl = document.getElementById('modal_default_content'); // used to set the color classes
const defaultBodyEl = document.getElementById('modal_default_body');
const defaultLoader = document.getElementById('modal_default_loading');

export function showErrorModal(response) {
    setErrorStyle();

    let title = translation.genericErrorTitle;
    if (response.hasOwnProperty('modalTitle')) {
        title = response.modalTitle;
    }

    let body = '<p>'+translation.genericErrorMessage+'</p>';
    if (response.hasOwnProperty('message')) {
        body = `<p>${response.message}</p>`
    }

    showModal(title, body);
}

export function showSuccessModal(title, body) {
    setSuccessStyle();
    showModal(title, body);
}

export function showDefaultModal(title, body) {
    clearStyle();
    showModal(title, body);
}

export function showModalLoader() {
    defaultLoader.classList.remove('hidden');
}

export function hideModalLoader() {
    defaultLoader.classList.add('hidden');
}

export function updateModalBody(body) {
    defaultBodyEl.innerHTML = body;
}

function showModal(title, body) {
    defaultTitleEl.innerText = title;
    defaultBodyEl.innerHTML = body;

    $('#modal_default').modal('show');
}
function setSuccessStyle() {
    clearStyle();
    defaultContentEl.classList.add('bg-success');
}
function setErrorStyle() {
    clearStyle();
    defaultContentEl.classList.add('bg-danger');
}
function clearStyle() {
    defaultContentEl.classList.remove('bg-danger');
    defaultContentEl.classList.remove('bg-success');
}