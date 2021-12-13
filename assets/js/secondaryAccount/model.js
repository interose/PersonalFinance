Ext.define('modelSecondaryAccountTransaction', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id_transaction', type: 'int'},
        {name: 'valuta_date', type: 'date'},
        {name: 'name', type: 'string'},
        {name: 'description', type: 'string'},
        {name: 'description_raw', type: 'string'},
        {name: 'amount', type: 'float'},
        {name: 'booking_text', type: 'string'},
        {name: 'credit_debit', type: 'string'},
    ]
});

Ext.define('modelSecondaryAccount', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'name', type: 'string'}
    ]
});