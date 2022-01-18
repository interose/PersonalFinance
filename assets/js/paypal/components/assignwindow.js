import * as ModalHandler from "../../_modalHandler";

export const assignwindow = Ext.create('Ext.window.Window', {
    title: 'PayPal Transaktion zuordnen',
    id: 'assignWindow',
    height: 400,
    width: 700,
    layout: {
        type: 'vbox',
        align: 'stretch',
        pack: 'start'
    },
    payPalTransactionId: null,
    closeAction: 'method-hide',
    listeners: {
        close: {
            fn: function() {
                const store = Ext.data.StoreManager.lookup('PayPalTransactionStore');
                store.reload();
            }
        },
        scope: this
    },
    items: [{
        xtype: 'container',
        height: 40,
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
        }],
    }, {
        xtype: 'grid',
        id: 'assignGrid',
        border: false,
        flex: 1,
        columns: [{
            xtype: 'datecolumn',
            dataIndex: 'valuta_date',
            header: 'Datum',
            width: 200,
            format: 'd.m.Y'
        }, {
            header: 'Verwendungszweck',
            flex: 2,
            dataIndex: 'description'
        }, {
            header: 'Betrag',
            flex: 1,
            dataIndex: 'amount',
            align: 'right',
            renderer: function (value, metadata, record) {
                if (value) {
                    return Ext.util.Format.number(value, '0,000.00')+' €';
                }
            }
        }],
        listeners: {
            itemdblclick: function(el, record, item, index, e, eOpts) {
                let parentWindow = Ext.getCmp('assignWindow');

                Ext.Ajax.request({
                    url: paypal_assign_transaction,
                    params: {
                        idPayPalTransaction: parentWindow.idPayPalTransaction,
                        idTransaction: record.get('id')
                    },
                    method: 'POST',
                    success: function (response, opts) {
                        const responseObject = JSON.parse(response.responseText);
                        const status = responseObject?.success ?? false;

                        if (status === false) {
                            ModalHandler.showErrorModal({
                                message: responseObject?.message ?? translation.genericErrorMessage
                            });
                        } else {
                            assignwindow.close();
                        }
                    },
                    failure: function(response, opts) {
                        const responseObject = JSON.parse(response.responseText);
                        ModalHandler.showErrorModal({
                            message: responseObject?.message ?? translation.genericErrorMessage
                        });
                    }
                });
            }
        },
        store: Ext.data.StoreManager.lookup('TransactionStore'),
    }]
});