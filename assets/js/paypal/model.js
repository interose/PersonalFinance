Ext.define('PayPalTransactionModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'booking_date', type: 'date', format: 'Y-m-d H:i:s'},
        {name: 'name', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'amount', type: 'float'},
        {name: 'recipient', type: 'string'},
        {name: 'transaction_code', type: 'string'},
        {name: 'article_description', type: 'string'},
        {name: 'article_number', type: 'string'},
        {name: 'associated_transaction_code', type: 'string'},
        {name: 'invoice_number', type: 'string'}
    ]
});