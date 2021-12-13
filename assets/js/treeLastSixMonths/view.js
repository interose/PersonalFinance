export const tree = Ext.create('Ext.tree.Panel', {
    rootVisible: false,
    useArrows: true,
    border: false,
    flex: 1,
    store: Ext.data.StoreManager.lookup('TreeStore'),
    defaults: {
        menuDisabled: true,
        sortable: false,
        hideable: false,
        align: 'right'
    },
    viewConfig: {
        getRowClass: function(rec, idx, rowPrms, ds) {
            if (rec.get('name') == 'Gesamt') {
                return 'summary'
            }
        }
    },
    columns: [{
        xtype: 'treecolumn',
        text: 'Kategorie',
        dataIndex: 'name',
        menuDisabled: true,
        sortable: false,
        width: 250
    }, {
        text: '01',
        dataIndex: 'month01',
        sortable: false,
        hideable: false,
        menuDisabled: true,
        align: 'right',
        renderer: function (v) {
            if (v > 0) {
                return Ext.util.Format.number(v, '0,000.00') + ' €';
            } else {
                return '-' + Ext.util.Format.number(v * -1, '0,000.00') + ' €';
            }
        },
        flex: 1
    }, {
        text: '02',
        dataIndex: 'month02',
        sortable: false,
        hideable: false,
        menuDisabled: true,
        align: 'right',
        renderer: function (v) {
            if (v > 0) {
                return Ext.util.Format.number(v, '0,000.00') + ' €';
            } else {
                return '-' + Ext.util.Format.number(v * -1, '0,000.00') + ' €';
            }
        },
        flex: 1
    }, {
        text: '03',
        dataIndex: 'month03',
        sortable: false,
        hideable: false,
        menuDisabled: true,
        align: 'right',
        renderer: function (v) {
            if (v > 0) {
                return Ext.util.Format.number(v, '0,000.00') + ' €';
            } else {
                return '-' + Ext.util.Format.number(v * -1, '0,000.00') + ' €';
            }
        },
        flex: 1
    }, {
        text: '04',
        dataIndex: 'month04',
        sortable: false,
        hideable: false,
        menuDisabled: true,
        align: 'right',
        renderer: function (v) {
            if (v > 0) {
                return Ext.util.Format.number(v, '0,000.00') + ' €';
            } else {
                return '-' + Ext.util.Format.number(v * -1, '0,000.00') + ' €';
            }
        },
        flex: 1
    }, {
        text: '05',
        dataIndex: 'month05',
        sortable: false,
        hideable: false,
        menuDisabled: true,
        align: 'right',
        renderer: function (v) {
            if (v > 0) {
                return Ext.util.Format.number(v, '0,000.00') + ' €';
            } else {
                return '-' + Ext.util.Format.number(v * -1, '0,000.00') + ' €';
            }
        },
        flex: 1
    }, {
        text: '06',
        dataIndex: 'month06',
        sortable: false,
        hideable: false,
        menuDisabled: true,
        align: 'right',
        renderer: function (v) {
            if (v > 0) {
                return Ext.util.Format.number(v, '0,000.00') + ' €';
            } else {
                return '-' + Ext.util.Format.number(v * -1, '0,000.00') + ' €';
            }
        },
        flex: 1
    }]
});