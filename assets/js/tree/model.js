Ext.define('TreeModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'month01', type: 'float'},
        {name: 'month02', type: 'float'},
        {name: 'month03', type: 'float'},
        {name: 'month04', type: 'float'},
        {name: 'month05', type: 'float'},
        {name: 'month06', type: 'float'},
        {name: 'month07', type: 'float'},
        {name: 'month08', type: 'float'},
        {name: 'month09', type: 'float'},
        {name: 'month10', type: 'float'},
        {name: 'month11', type: 'float'},
        {name: 'month12', type: 'float'}
    ]
});

Ext.define('TransactionModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id_transaction', type: 'int'},
        {name: 'id_splittransaction', type: 'int'},
        {name: 'valuta_date', type: 'date'},
        {name: 'name', type: 'string'},
        {name: 'description', type: 'string'},
        {name: 'description_raw', type: 'string'},
        {name: 'amount', type: 'float'},
        {name: 'category_name', type: 'string'},
        {name: 'booking_text', type: 'string'},
        {name: 'credit_debit', type: 'string'},
        {name: 'split', type: 'boolean'}
    ]
});