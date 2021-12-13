import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'admin-lte/dist/css/AdminLTE.min.css';
import '../css/index.css';

import 'jquery';
import 'admin-lte/dist/js/adminlte.min.js';
import 'bootstrap';

import initTransferHandler from './_transferHandler'; // aka "Neu Überweisung"
import initTransactionHandler from './_transactionHandler'; // aka "Buchungen importieren"

(function(){
    initTransferHandler();
    initTransactionHandler();

    let resizeTimer;
    function onResize() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            updateExtjsLayout();
        }, 250);
    }

    function onPushMenuToggle() {
        setTimeout(function() {
            updateExtjsLayout();
        }, 350);
    }

    function updateExtjsLayout() {
        if (window.hasOwnProperty('panel')) {
            window.panel.updateLayout();
        }
    }

    window.addEventListener("resize", onResize);
    $(document).on('shown.lte.pushmenu', onPushMenuToggle);
    $(document).on('collapsed.lte.pushmenu', onPushMenuToggle);
})();
