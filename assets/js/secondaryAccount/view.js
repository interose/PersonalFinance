import '../grid/components/monthpicker';

export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('SecondaryAccountTransactionStore'),
    minHeight: 500,
    tbar: [{
        xtype: 'combo',
        reference: 'subaccount_combo',
        store: Ext.data.StoreManager.lookup('SecondaryAccountStore'),
        displayField: 'name',
        valueField: 'id',
        queryMode: 'local',
        width: 250,
        listeners: {
            change: function(el) {
                const grid = el.up('grid');
                let value = el.getValue();
                let textfield = Ext.ComponentQuery.query('textfield[reference=year_input]')[0];

                grid.store.load({
                    scope: this,
                    params: {
                        year: textfield.getValue(),
                        subAccountId: value
                    },
                    callback: function(records, operation, success) {
                        grid.setLoading(false);
                    }
                });
            }
        }
    },'-', {
        xtype: 'button',
        text: translation.subAccountBtnBack,
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
                let combo = Ext.ComponentQuery.query('combo[reference=subaccount_combo]')[0];

                grid.store.load({
                    scope: this,
                    params: {
                        year: value,
                        subAccountId: combo.getValue()
                    },
                    callback: function(records, operation, success) {
                        grid.setLoading(false);
                    }
                });
            }
        }
    }, {
        xtype: 'button',
        text: translation.subAccountBtnNext,
        listeners: {
            click: function(el){
                let textfield = Ext.ComponentQuery.query('textfield[reference=year_input]')[0];
                let year = parseInt(textfield.getValue());
                textfield.setValue(year + 1);
            }
        }
    }],
    columns: [{
        text: translation.subAccountGridColValuta,
        dataIndex: 'valuta_date',
        xtype: 'datecolumn',
        format: 'd.m.Y',
        sortable: true,
        hideable: false,
        groupable: false,
        menuDisabled: false,
        width: 150
    }, {
        header: translation.subAccountGridColBooking,
        dataIndex: 'booking_text',
        sortable: false,
        hideable: false,
        groupable: false,
        width: 150
    }, {
        header: translation.subAccountGridColDescription,
        dataIndex: 'description_raw',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.subAccountGridColAmount,
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

            return Ext.util.Format.number(value, '0,000.00')+' €';
        }
    }]
});