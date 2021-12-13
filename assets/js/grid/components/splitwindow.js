export const splitwindow = Ext.create('Ext.window.Window', {
    title: 'Buchung splitten',
    id: 'splitWindow',
    height: 400,
    width: 700,
    layout: {
        type: 'vbox',
        align: 'stretch',
        pack: 'start'
    },
    closeAction: 'method-hide',
    listeners: {
        close: {
            fn: function() {
                const store = Ext.data.StoreManager.lookup('TransactionStore');
                store.reload();
            }
        },
        scope: this
    },
    items: [{
        xtype: 'container',
        height: 80,
        layout: {
            type: 'hbox',
            align: 'stretch',
            pack: 'start'
        },
        items: [{
            xtype: 'panel',
            id: 'panelDescription',
            layout: 'fit',
            bodyPadding: '10 15 10 15',
            html: '',
            border: false,
            style: {
                'word-wrap': 'anywhere',
                'border-bottom': '1px solid #d0d0d0',
                'border-top': '1px solid #d0d0d0',
            },
            flex: 9
        }, {
            xtype: 'panel',
            layout: 'fit',
            id: 'panelAmount',
            bodyPadding: '10 15 10 15',
            border: false,
            flex: 2,
            html: '',
            style: {
                'word-wrap': 'anywhere',
                'border-bottom': '1px solid #d0d0d0',
                'border-top': '1px solid #d0d0d0',
            },
        }]
    },{
        xtype: 'grid',
        id: 'detailGrid',
        border: false,
        flex: 1,
        listeners: {
            edit: {
                fn: function(editor, context) {

                    let parentWindow = Ext.getCmp('splitWindow');
                    parentWindow.splitAmount = parentWindow.splitAmount - context.record.get('amount');
                    Ext.getCmp('panelAmount').body.update(Ext.util.Format.number(parentWindow.splitAmount, '0,000.00')+' €');

                    let store = Ext.data.StoreManager.lookup('CategoryStore');
                    let category = store.getById(context.record.get('category_id'));
                    context.record.set('category_name', category.get('name'));

                    store = Ext.data.StoreManager.lookup('TransactionSplitStore');
                    store.sync();
                }
            },
            scope: this
        },
        columns: [{
            xtype: 'datecolumn',
            dataIndex: 'valuta_date',
            header: 'Datum',
            width: 200,
            format: 'd.m.Y',
            editor: {
                xtype: 'datefield',
                format: 'd.m.Y'
            }
        }, {
            header: 'Verwendungszweck',
            flex: 2,
            dataIndex: 'description',
            editor: {
                xtype: 'textfield'
            }
        }, {
            header: 'Kategorie',
            dataIndex: 'category_id',
            sortable: false,
            hideable: false,
            groupable: false,
            width: 200,
            editor: {
                xtype: 'combo',
                completeOnEnter: true,
                store: Ext.data.StoreManager.lookup('CategoryDetailStore'),
                typeAhead: true,
                triggerAction: 'all',
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local'
            },
            renderer: function(value, metadata, record) {
                return record.get('category_name');
            }
        }, {
            header: 'Betrag',
            flex: 1,
            dataIndex: 'amount',
            editor: {
                xtype: 'numberfield'
            },
            renderer: function (value, metadata, record) {
                if (value) {
                    return Ext.util.Format.number(value, '0,000.00')+' €';
                }
            }
        }, {
            dataIndex: 'transaction',
            hidden: true
        }, {
            dataIndex: 'idSplitTransaction',
            hidden: true
        }],
        store: Ext.data.StoreManager.lookup('TransactionSplitStore'),
        plugins: {
            ptype: 'rowediting',
            clicksToEdit: 1,
            pluginId: 'roweditingId',
            autoCancel: false,
            listeners: {
                canceledit: function(editor, context, eOpts ){
                    context.store.remove(context.record);
                    context.store.sync();
                }
            }
        },
        tbar: [{
            text: 'Buchung hinzufügen',
            cls: 'btnUpdateTransactions',
            handler: function() {
                let grid = Ext.getCmp('detailGrid');
                let parentWindow = Ext.getCmp('splitWindow');
                let rowediting = grid.getPlugin('roweditingId');
                rowediting.cancelEdit();
                grid.store.insert(0, {
                    transaction: parentWindow.idTransaction,
                    idSplitTransaction: 0,
                    description: '',
                    amount: '',
                    valuta_date: parentWindow.valutaDate
                });
                rowediting.startEdit(0, 0);
            }
        }]
    }]
});