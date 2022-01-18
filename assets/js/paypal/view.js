import {assignwindow} from './components/assignwindow';

export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('PayPalTransactionStore'),
    minHeight: 500,
    tbar: [{
        xtype: 'button',
        text: translation.paypalBtnBack,
        listeners: {
            click: function(el) {
                let textfield = Ext.ComponentQuery.query('textfield[reference=year_input]')[0];
                let year = parseInt(textfield.getValue());
                textfield.setValue(year - 1);
            }
        }
    }, {
        xtype: 'textfield',
        value: Ext.Date.format(new Date(),'Y'),
        reference: 'year_input',
        width: 100,
        listeners: {
            change: function(el) {
                const grid = el.up('grid');
                let value = el.getValue();

                grid.store.load({
                    scope: this,
                    params: {
                        year: value
                    },
                    callback: function(records, operation, success) {
                        grid.setLoading(false);
                    }
                });
            }
        }
    }, {
        xtype: 'button',
        text: translation.paypalBtnNext,
        listeners: {
            click: function(el){
                let textfield = Ext.ComponentQuery.query('textfield[reference=year_input]')[0];
                let year = parseInt(textfield.getValue());
                textfield.setValue(year + 1);
            }
        }
    }],
    columns: [{
        text: '',
        dataIndex: 'already_assigned',
        sortable: false,
        hideable: false,
        groupable: false,
        align: 'center',
        width: 25,
        renderer: function(value, metadata, record) {
            if (parseInt(value) === 1) {
                return '<i class="fas fa-link"></i>';
            } else {
                return '';
            }
        }
    },{
        text: translation.paypalColDate,
        dataIndex: 'booking_date',
        xtype: 'datecolumn',
        format: 'd.m.Y H:i',
        sortable: true,
        hideable: false,
        groupable: false,
        menuDisabled: false,
        width: 140
    }, {
        header: translation.paypalColName,
        dataIndex: 'name',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColType,
        dataIndex: 'type',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColRecipient,
        dataIndex: 'recipient',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColTransCode,
        dataIndex: 'transaction_code',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColArtDesc,
        dataIndex: 'article_description',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColArtNumb,
        dataIndex: 'article_number',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColAssocTrans,
        dataIndex: 'associated_transaction_code',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColInvoice,
        dataIndex: 'invoice_number',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.paypalColAmount,
        dataIndex: 'amount',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1,
        align: 'right',
        renderer: function(value, metadata, record) {
            if (value >= 0) {
                return '<span class="positiv">'+Ext.util.Format.number(value, '0,000.00')+' €</span>';
            }
            else {
                return '<span class="negativ">'+Ext.util.Format.number(value, '0,000.00')+' €</span>';
            }
        }
    }],
    listeners: {
        itemdblclick: function(el, record, item, index, e, eOpts) {
            const idPayPalTransaction = record.get('id');
            const alreadyAssigned = parseInt(record.get('already_assigned'));

            if (alreadyAssigned === 1) {
                return;
            }

            assignwindow.show();
            assignwindow.idPayPalTransaction = idPayPalTransaction;

            Ext.getCmp('panelDescription').body.update(record.get('name'));
            Ext.getCmp('panelAmount').body.update(Ext.util.Format.number(record.get('amount'), '0,000.00')+' €');
            const store = Ext.data.StoreManager.lookup('TransactionStore');
            store.load({
                params: {
                    idPayPalTransaction: idPayPalTransaction
                }
            });
        }
    }
});