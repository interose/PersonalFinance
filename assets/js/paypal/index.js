import '../../css/grid.css';
import 'dropzone/dist/dropzone.css';
import '../../css/paypal.css';

import './model.js';
import './store.js';
import {grid} from './view.js';

import Dropzone from "dropzone";
import * as ModalHandler from "../_modalHandler";

Ext.scopeCss = true;

Ext.onReady(function() {
    Ext.create('Ext.container.Container', {
        renderTo: 'ext-container',
        layout: 'fit',
        items: [grid]
    });

    window.panel = grid;

    let myDropzone = new Dropzone("div#myDropzone", {
        url: paypal_import,
        parallelUploads: 1,
        createImageThumbnails: false,
        acceptedFiles: 'text/csv',
        disablePreviews: true,
        success(file, response) {
            myDropzone.removeAllFiles();

            if (response.hasOwnProperty('success') && response.success === true) {
                ModalHandler.showSuccessModal(translation.paypalImportSuccess, response.message);
                grid.store.reload();
            } else {
                ModalHandler.showErrorModal(response);
            }
        },
        error(file, message) {
            myDropzone.removeAllFiles();
            ModalHandler.showErrorModal({
                message: message
            });
        }
    });

    grid.store.load({
        scope: this,
        params: {
            year: Ext.Date.format(new Date(),'Y')
        },
        callback: function(records, operation, success) {
            grid.setLoading(false);
        }
    });
});