import {detailwindow} from './components/detailwindow';

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
    tbar: [{
        xtype: 'button',
        text: translation.treeGridBtnBack,
        listeners: {
            click: function(el){
                const field = Ext.ComponentQuery.query('#yearField')[0];
                let year = field.getValue();
                year -= 1;
                field.setValue(year);

                const tree = el.up('treepanel');
                tree.setLoading();
                tree.store.load({
                    scope: this,
                    callback: function() {
                        tree.setLoading(false);
                    },
                    params: {
                        year: year
                    }
                });
            }
        }
    },{
        xtype: 'numberfield',
        value: new Date().getFullYear(),
        width: 75,
        itemId: 'yearField',
        readOnly: true,
        style: {
            textAlign: 'center'
        }
    }, {
        xtype: 'button',
        text: translation.treeGridBtnNext,
        listeners: {
            click: function(el){
                const field = Ext.ComponentQuery.query('#yearField')[0];
                let year = field.getValue();
                year += 1;
                field.setValue(year);

                const tree = el.up('treepanel');
                tree.setLoading();
                tree.store.load({
                    scope: this,
                    callback: function() {
                        tree.setLoading(false);
                    },
                    params: {
                        year: year
                    }
                });
            }
        }
    }],
    viewConfig: {
        toggleOnDblClick: false,
        getRowClass: function(rec, idx, rowPrms, ds) {
            if (rec.get('name') == 'Gesamt') {
                return 'summary'
            }
        }
    },
    columns: [{
        xtype: 'treecolumn',
        text: translation.treeGridColCategory,
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
    }, {
        text: '07',
        dataIndex: 'month07',
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
        text: '08',
        dataIndex: 'month08',
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
        text: '09',
        dataIndex: 'month09',
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
        text: '10',
        dataIndex: 'month10',
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
        text: '11',
        dataIndex: 'month11',
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
        text: '12',
        dataIndex: 'month12',
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
    }],
    listeners: {
        celldblclick: function(el, td, cellIndex, record, tr, rowIndex, e, eOpts ) {
            if (!(cellIndex >= 1 && cellIndex <= 12)) {
                return;
            }

            const field = Ext.ComponentQuery.query('#yearField')[0];

            detailwindow.show();

            const store = Ext.data.StoreManager.lookup('TransactionStore');
            store.load({
                params: {
                    month: cellIndex,
                    year: field.getValue(),
                    category: record.get('name')
                }
            });
        }
    }
});