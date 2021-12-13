import * as ModalHandler from "./_modalHandler";
import {handleFetchErrors, removeLoadingOverlay, showLoadingOverlay} from "./_common";

export default function initTransactionHandler() {
    function onBtnImportTransactionsClick(e) {
        e.preventDefault();
        doRequest('');
    }

    function onFormTanSubmit(e, tan) {
        e.preventDefault();
        doRequest(tan);

        return false;
    }

    function doRequest(tan) {
        showLoadingOverlay();

        const data = new URLSearchParams();
        if (tan.length > 0) {
            data.append('tan', tan);
        }

        fetch(update_transactions, {
                method: 'POST',
                body: data
            })
            .then(handleFetchErrors)
            .then((res) => res.json())
            .then((json) => {
                removeLoadingOverlay();

                if (false === json.success) {
                    if (json.hasOwnProperty('type') && json.type === 'tan') {
                        ModalHandler.showDefaultModal(json.modalTitle, json.modalBody);
                    } else {
                        ModalHandler.showErrorModal(json)
                    }
                } else {
                    let html = '';
                    json.data.forEach(function(item) {
                        html += `<b>${item.accountName}</b> (${item.accountNumber})<br>Fetched transactions: ${item.transactions}<br>New transactions: ${item.new}<br>Assigned transactions: ${item.assigned}<br><br>`;
                    });

                    ModalHandler.showSuccessModal(json.modalTitle, html);
                }
            })
            .catch(function(error) {
                removeLoadingOverlay();
                ModalHandler.showErrorModal(error);
            });
    }

    document.getElementById('btn_import_transactions').addEventListener("click", onBtnImportTransactionsClick);
    window.onFormTanSubmit = onFormTanSubmit;
}
