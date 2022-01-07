import 'javascript-autocomplete/auto-complete.css';

import Inputmask from "inputmask";
import AutoComplete from "javascript-autocomplete";
import * as ModalHandler from "./_modalHandler";
import {handleFetchErrors, removeLoadingOverlay, showLoadingOverlay} from "./_common";

Inputmask.extendAliases({
    'myCurrency': {
        alias: "currency",
        prefix: "",
        groupSeparator: ".",
        radixPoint: ","
    }
});

export default function initTransferHandler() {
    /**
     * Event handler if the user hits the button "Neue Überweisung"
     */
    function onBtnNewTransferClick(e) {
        e.preventDefault();

        showLoadingOverlay();

        let url = transfer_index;
        // check if we have a transfer id
        const id = e.target.dataset.transferId;
        if (id !== undefined) {
            url += '?id='+id;
            e.target.removeAttribute('data-transfer-id');
        }

        fetch(url)
            .then(handleFetchErrors)
            .then((res) => res.text())
            .then((text) => {
                ModalHandler.showDefaultModal(translation.newTransferModalTitle, text);
                initNewTransferListeners();
                removeLoadingOverlay();
            })
            .catch(function(error) {
                removeLoadingOverlay();
                ModalHandler.showErrorModal(error);
            });
    }

    /**
     * Event handler if the user hits the submit button within the transfer modal window
     */
    function onFormTransferSubmit(e) {
        e.preventDefault();

        ModalHandler.showModalLoader();

        fetch(transfer_index, {
            'method': 'POST',
            'body': new FormData(document.getElementById("form_transfer")),
        })
            .then(handleFetchErrors)
            .then((res) => res.text())
            .then((text) => {
                    ModalHandler.updateModalBody(text);
                    initNewTransferListeners();
                    ModalHandler.hideModalLoader();
            })
            .catch(function(error) {
                ModalHandler.updateModalBody(error);
                ModalHandler.hideModalLoader();
            });

        return false;
    }

    function onFormTransferTanSubmit(e) {
        e.preventDefault();

        ModalHandler.showModalLoader();

        fetch(transfer_tan, {
            'method': 'POST',
            'body': new FormData(document.getElementById("form_tan_transfer")),
        })
            .then(handleFetchErrors)
            .then((res) => res.text())
            .then((text) => {
                ModalHandler.updateModalBody(text);
                ModalHandler.hideModalLoader();
            })
            .catch(function(error) {
                ModalHandler.updateModalBody(error);
                ModalHandler.hideModalLoader();
            });

        return false;
    }

    /**
     * Event handler for the IBAN input field within the transfere modal window
     */
    function onHandleIbanInput(e) {
        const ibanLength = 22;
        const parentDiv = e.target.parentElement.parentElement;
        parentDiv.classList.remove('has-success');
        parentDiv.classList.remove('has-error');

        let value = e.target.value;
        value = value.replace(/_/g, '');
        value = value.replace(/\s/g, '');

        if (value.length === ibanLength) {
            const inputBic = document.getElementById('transfer_bic');
            const inputBankname = document.getElementById('transfer_bankName');

            fetch(`https://openiban.com/validate/${value}?getBIC=true&validateBankCode=true`, {
                method: 'GET'
            })
                .then(handleFetchErrors)
                .then((res) => res.json())
                .then((json) => {
                    if (json.valid === true) {
                        parentDiv.classList.remove('has-error');
                        parentDiv.classList.add('has-success');

                        inputBankname.value = json.bankData.name;
                        inputBic.value = json.bankData.bic;
                    } else {
                        parentDiv.classList.remove('has-success');
                        parentDiv.classList.add('has-error');

                        inputBankname.value = '';
                        inputBic.value = '';
                    }
                });
        }
    }

    /**
     * function to init all the event listener and plugins for the form
     */
    function initNewTransferListeners() {
        const inputIban = document.getElementById("transfer_iban");

        if (inputIban !== null) {
            Inputmask({
                mask: "AA 9{2} 9{4} 9{4} 9{4} 9{4} 9{2}",
                casing: "upper",
            }).mask(inputIban);
            inputIban.addEventListener('input', onHandleIbanInput);
            inputIban.addEventListener('change', onHandleIbanInput);
        }

        const inputAmount = document.getElementById('transfer_amount');
        if (inputAmount !== null) {
            Inputmask("myCurrency").mask(inputAmount);
        }

        initAutocomplete();
    }

    /**
     * init the autocomplete plugin at the money receiver field
     */
    function initAutocomplete() {
        let xhr;
        new AutoComplete({
            selector: 'input[id="transfer_name"]',
            source: function(term, response){
                try { xhr.abort(); } catch(e){}
                xhr = $.getJSON(transfer_autocomplete, { q: term }, function(data){ response(data); });
            },
            renderItem: function (item, search) {
                return '<div class="autocomplete-suggestion" data-bic="'+item.bic+'" data-bankname="'+item.bankName+'" data-iban="'+item.iban+'" data-name="'+item.name+'">'+item.iban+' - '+item.name+'</br><span class="subtext">'+item.bankName+' - '+item.bic+'</span></div>';
            },
            onSelect: function(e, term, item){
                const inputIban = document.getElementById("transfer_iban");
                const inputName = document.getElementById("transfer_name");

                inputIban.value = item.dataset.iban;
                inputName.value = item.dataset.name;

                if ("createEvent" in document) {
                    let evt = document.createEvent("HTMLEvents");
                    evt.initEvent("change", false, true);
                    inputIban.dispatchEvent(evt);
                }
                else {
                    inputIban.fireEvent("onchange");
                }
            }
        });
    }

    document.getElementById('btn_new_transfer').addEventListener("click", onBtnNewTransferClick);
    window.onTransferSubmit = onFormTransferSubmit;
    window.onTransferTanSubmit = onFormTransferTanSubmit;
}
