export function handleFetchErrors(response) {
    if (!response.ok) {
        throw Error(response.statusText);
    }
    return response;
}

export function showLoadingOverlay() {
    let overlay = document.createElement('div');
    overlay.setAttribute('id', 'my_overlay');
    overlay.classList.add('overlay-wrapper');
    overlay.innerHTML = '<div class="overlay"><i class="spinner-border"></i></div>';
    document.body.appendChild(overlay);
}

export function removeLoadingOverlay() {
    let overlay = document.getElementById('my_overlay');
    document.body.removeChild(overlay);
}