import '../../css/grid.css';

import './model.js';
import './store.js';
import {grid} from './view.js';

Ext.scopeCss = true;

Ext.onReady(function() {
    Ext.create('Ext.container.Container', {
        renderTo: 'ext-container',
        layout: 'fit',
        items: [grid]
    });

    window.panel = grid;
});