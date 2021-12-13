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

    const picker = Ext.ComponentQuery.query('monthfield')[0];
    picker.fireEvent('select', picker, picker.selectMonth);

    Ext.QuickTips.init();

    window.panel = grid;
});