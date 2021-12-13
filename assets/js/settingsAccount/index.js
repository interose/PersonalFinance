import '../../css/settingsAccount.css';

import Inputmask from "inputmask";
import {handleFetchErrors} from "../_common";

(function(){
    const inputBackground = document.getElementById("account_backgroundColor");
    const inputEditBackground = document.getElementById("account_edit_backgroundColor");
    const inputForeground = document.getElementById("account_foregroundColor");
    const inputEditForeground = document.getElementById("account_edit_foregroundColor");
    const updateButton = document.getElementById("updateSubaccountsButton");
    const subAccountBox = document.getElementById('subaccounts');

    if (inputBackground) {
        Inputmask({
            mask: "\\#######",
        }).mask(inputBackground);
    }

    if (inputEditBackground) {
        Inputmask({
            mask: "\\#######",
        }).mask(inputEditBackground);
    }

    if (inputEditForeground) {
        Inputmask({
            mask: "\\#######",
        }).mask(inputEditForeground);
    }

    if (inputForeground) {
        Inputmask({
            mask: "\\#######",
        }).mask(inputForeground);
    }

    if (updateButton) {
        updateButton.addEventListener('click', function(e) {
            e.preventDefault();

            const div = document.createElement('div');
            div.classList.add('overlay');
            div.innerHTML = '<i class="fa fas fa-sync fa-spin"></i>';
            subAccountBox.appendChild(div);

            const url = e.target.getAttribute('href');

            fetch(url)
                .then(handleFetchErrors)
                .then((res) => res.json())
                .then((json) => {
                    const boxBody = subAccountBox.getElementsByClassName('card-body')
                    boxBody[0].innerHTML = json.subAccountsView;

                    div.remove();
                });

            return false;
        });
    }
})();