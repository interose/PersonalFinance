Ext.define('TransferModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'info', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'iban', type: 'string'},
        {name: 'bic', type: 'string'},
        {name: 'amount', type: 'float'},
        {name: 'executionDate', type: 'date'},
        {name: 'bankName', type: 'string'},
    ]
});