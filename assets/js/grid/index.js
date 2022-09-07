import '../../css/grid.css';

import defineModels from './model';
import defineStores from './store';
import createView from './view.js';

Ext.scopeCss = true;

Ext.onReady(function() {
    defineModels();
    defineStores();
    const grid = createView();

    Ext.create('Ext.container.Container', {
        renderTo: 'ext-container',
        layout: 'fit',
        items: [grid]
    });

    const picker = Ext.ComponentQuery.query('monthfield')[0];
    picker.fireEvent('select', picker, picker.selectMonth);

    Ext.QuickTips.init();

    window.panel = grid;

    window.onTransactionSuccessAndFinished = function() {
        const picker = Ext.ComponentQuery.query('monthfield')[0];
        picker.fireEvent('select', picker, picker.selectMonth);
    }
});