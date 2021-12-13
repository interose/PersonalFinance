import '../../css/grid.css';

import './model';
import './store';

import {grid} from './view.js';

Ext.scopeCss = true;

Ext.onReady(function() {
    Ext.create('Ext.container.Container', {
        renderTo: 'ext-container',
        layout: 'fit',
        items: [grid]
    });

    const combo = Ext.ComponentQuery.query('combo[reference=subaccount_combo]')[0];
    combo.store.load({
        scope: this,
        callback: function (records, operation, success) {
            combo.select(records[0]);
        }
    });

    window.panel = grid;
});