import '../../css/tree.css';

import './model.js';
import './store.js';
import {tree} from './view.js';

Ext.scopeCss = true;

Ext.onReady(function() {
    Ext.create('Ext.container.Container', {
        renderTo: 'ext-container',
        layout: 'fit',
        items: [tree]
    });

    tree.setLoading('Laden');

    Ext.Ajax.request({
        url: get_columns_full_year,
        method: 'GET',
        success: function (response, opts) {
            let obj = Ext.decode(response.responseText);
            if (obj.success) {
                obj.data.forEach(function (value, key ) {
                    tree.columns[key+1].setText(value);
                });
            }
        }
    });

    const store = Ext.data.StoreManager.lookup('TreeStore');
    store.load({
        scope: this,
        params: {
            year: new Date().getFullYear()
        },
        callback: function() {
            tree.setLoading(false);
        }
    });

    Ext.QuickTips.init();

    window.panel = tree;
});


