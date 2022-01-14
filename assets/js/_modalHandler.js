const titleEl = document.getElementById('modal_default_title');
const contentEl = document.getElementById('modal_default_content'); // used to set the color classes
const bodyEl = document.getElementById('modal_default_body');
const loader = document.getElementById('modal_loading');

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
    loader.classList.remove('hidden');
}

export function hideModalLoader() {
    loader.classList.add('hidden');
}

export function updateModalBody(body) {
    bodyEl.innerHTML = body;
}

function showModal(title, body) {
    titleEl.innerText = title;
    bodyEl.innerHTML = body;

    $('#modal_default').modal('show');
}
function setSuccessStyle() {
    clearStyle();
    contentEl.classList.add('bg-success');
}
function setErrorStyle() {
    clearStyle();
    contentEl.classList.add('bg-danger');
}
function clearStyle() {
    contentEl.classList.remove('bg-danger');
    contentEl.classList.remove('bg-success');
}