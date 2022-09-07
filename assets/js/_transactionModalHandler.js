const transactionTitleEl = document.getElementById('modal_transaction_title');
const transactionBodyEl = document.getElementById('modal_transaction_body');

const transactionModal = $('#modal_transaction');
const currentBalance = document.getElementById('current_balance');

transactionModal.on('hidden.bs.modal', function (event) {
    fetch(get_current_balance)
        .then((res) => res.json())
        .then((json) => {
            currentBalance.innerHTML = json;
        });

    if (typeof window.onTransactionSuccessAndFinished === "function") {
        window.onTransactionSuccessAndFinished();
    }
})

export function showSuccessModal(title, body) {
    transactionTitleEl.innerText = title;
    transactionBodyEl.innerHTML = body;

    transactionModal.modal('show');
}