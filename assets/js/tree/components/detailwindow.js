export const detailwindow = Ext.create('Ext.window.Window', {
    title: 'Detailtransaktionen',
    id: 'assignWindow',
    height: 400,
    width: 700,
    modal: true,
    layout: {
        type: 'vbox',
        align: 'stretch',
        pack: 'start'
    },
    payPalTransactionId: null,
    closeAction: 'method-hide',
    items: [{
        xtype: 'grid',
        id: 'detailGrid',
        border: false,
        flex: 1,
        columns: [{
            xtype: 'datecolumn',
            dataIndex: 'valuta_date',
            header: 'Datum',
            width: 100,
            format: 'd.m.Y'
        }, {
            header: 'Empfänger',
            flex: 1,
            dataIndex: 'name',
            renderer: function (value, meta, record) {
                meta.tdAttr = 'data-qtip="' + record.get('description_raw') + '"';
                return value;
            }
        }, {
            header: 'Kategorie',
            flex: 1,
            dataIndex: 'category_name'
        }, {
            header: 'Betrag',
            dataIndex: 'amount',
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
        store: Ext.data.StoreManager.lookup('TransactionStore'),
    }]
});