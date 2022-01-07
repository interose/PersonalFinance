export default function defineModels() {
    Ext.define('modelGridByMonth', {
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

    Ext.define('CategoryModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'name', type: 'string'},
            {name: 'tree_ignore', type: 'boolean'},
        ]
    });
};