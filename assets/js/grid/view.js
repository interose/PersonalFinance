import './components/monthpicker';

import {splitwindow} from "./components/splitwindow";

export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('TransactionStore'),
    minHeight: 500,
    tbar: [{
        xtype: 'button',
        text: 'zurück',
        listeners: {
            click: function(el){
                var picker = Ext.ComponentQuery.query('monthfield')[0];
                var dt = Ext.Date.subtract(picker.getValue(), Ext.Date.MONTH, 1);
                picker.setValue(dt);
                picker.fireEvent('select', picker, picker.selectMonth);
            }
        }
    },{
        xtype: 'monthfield',
        format: 'F, Y',
        value: new Date(),
        listeners: {
            select: function(el) {
                var value = el.getValue();
                var year = value.getFullYear();
                var month = value.getMonth() + 1;

                var grid = el.up('grid');

                grid.store.load({
                    scope: this,
                    params: {
                        year: year,
                        month: month
                    },
                    callback: function(records, operation, success) {
                        grid.setLoading(false);
                    }
                });
            }
        }
    }, {
        xtype: 'button',
        text: 'vor',
        listeners: {
            click: function(el){
                var picker = Ext.ComponentQuery.query('monthfield')[0];
                var dt = Ext.Date.add(picker.getValue(), Ext.Date.MONTH, 1);
                picker.setValue(dt);
                picker.fireEvent('select', picker, picker.selectMonth);
            }
        }
    }, '->', {
        xtype: 'tbtext',
        text: 'Empfänger'
    }, {
        xtype:'textfield',
        reference: 'filter_name'
    }, {
        xtype: 'tbtext',
        text: 'Verwendungszweck'
    }, {
        xtype:'textfield',
        reference: 'filter_description'
    }, {
        text: 'Filtern',
        cls: 'btnMyDefault',
        iconCls: 'button-filter-small',
        listeners: {
            click: function() {
                let filterName = Ext.ComponentQuery.query('textfield[reference=filter_name]')[0].getValue();
                let filterDescription = Ext.ComponentQuery.query('textfield[reference=filter_description]')[0].getValue();

                const store = Ext.data.StoreManager.lookup('TransactionStore');

                store.load({
                    params: {
                        name: filterName,
                        description: filterDescription
                    }
                });
            }
        }
    }, {
        text: 'Filter löschen',
        cls: 'btnMyDefault',
        iconCls: 'button-filter-clear',
        listeners: {
            click: function() {
                Ext.ComponentQuery.query('textfield[reference=filter_name]')[0].setValue('');
                Ext.ComponentQuery.query('textfield[reference=filter_description]')[0].setValue('');

                var picker = Ext.ComponentQuery.query('monthfield')[0];
                picker.fireEvent('select', picker, picker.selectMonth);
            }
        }
    }],
    viewConfig: {
        getRowClass: function(record, rowIndex, rowParams, store) {
            return record.get('split') ? 'row-split' : '';
        }
    },
    columns: [{
        text: '',
        dataIndex: 'split',
        sortable: false,
        hideable: false,
        groupable: false,
        align: 'center',
        width: 25,
        renderer: function(value, metadata, record) {
            if (value === true) {
                return '<i class="fas fa-code-branch"></i>';
            } else {
                return '';
            }
        }
    },{
        text: 'Buchungstag',
        dataIndex: 'valuta_date',
        xtype: 'datecolumn',
        format: 'd.m.Y',
        sortable: true,
        hideable: false,
        groupable: false,
        menuDisabled: false,
        width: 200
    }, {
        header: 'Empfänger',
        dataIndex: 'name',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1,
        renderer: function (value, meta, record) {
            meta.tdAttr = 'data-qtip="' + record.get('description_raw') + '"';
            return value;
        }
    }, {
        header: 'Kategorie',
        dataIndex: 'category_id',
        sortable: false,
        hideable: false,
        groupable: false,
        width: 300,
        editor: {
            xtype: 'combo',
            completeOnEnter: true,
            store: Ext.data.StoreManager.lookup('CategoryStore'),
            typeAhead: true,
            anyMatch: true,
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
        dataIndex: 'amount',
        sortable: false,
        hideable: false,
        groupable: false,
        align: 'right',
        width: 100,
        renderer: function(value, metadata, record) {
            if(record.get('credit_debit') === 'credit') {
                return '<span class="positiv">'+Ext.util.Format.number(value, '0,000.00')+' €</span>';
            }
            else if(record.get('credit_debit') === 'debit') {
                return '<span class="negativ">-'+Ext.util.Format.number(value, '0,000.00')+' €</span>';
            }

            return Ext.util.Format.number(v, '0,000.00')+' €';
        }
    }],
    plugins: {
        ptype: 'cellediting',
        clicksToEdit: 1
    },
    listeners: {
        edit: function(editor, e) {
            let store = Ext.data.StoreManager.lookup('CategoryStore');
            let category = store.getById(e.value);
            e.record.set('category_name', category.get('name'));

            store = Ext.data.StoreManager.lookup('TransactionStore');
            store.sync();
        },
        itemdblclick: function(el, record, item, index, e, eOpts) {
            const _self = this;
            const idTransaction = record.get('id_transaction');
            const valutaDate = record.get('valuta_date');
            let splitAmount = 0;

            splitwindow.show();

            const store = Ext.data.StoreManager.lookup('TransactionSplitStore');
            store.load({
                params: {
                    idTransaction: idTransaction
                },
                callback: function(records, operation, success) {
                    let response = Ext.JSON.decode(operation.getResponse().responseText);
                    Ext.getCmp('panelDescription').body.update(response.transaction.description);
                    splitAmount = response.transaction.amount;
                    Ext.getCmp('panelAmount').body.update(Ext.util.Format.number(splitAmount, '0,000.00')+' €');

                    splitwindow.idTransaction = idTransaction;
                    splitwindow.valutaDate = valutaDate;
                    splitwindow.splitAmount = splitAmount;
                }
            });
        }
    }
});