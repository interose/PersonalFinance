export default function defineStores() {

Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Store',
    model: 'modelGridByMonth',
    storeId: 'TransactionStore',
    autoLoad: false,
    autSync: true,
    remoteSort: false,
    remoteFilter: false,
    proxy: {
        type: 'ajax',
        api: {
            read: get_grid_data,
            update: update_category
        },
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        },
        writer: {
            type: 'json',
            writeAllFields: true
        }
    },
    sorters: [{
        property: 'valuta_date',
        direction: 'ASC'
    }]
});

Ext.create('Ext.data.Store', {
    model: 'CategoryModel',
    storeId: 'CategoryStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    proxy: {
        type: 'ajax',
        url: get_category_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    },
    sorters: [{
        property: 'name',
        direction: 'ASC'
    }]
});

Ext.create('Ext.data.Store', {
    model: 'CategoryModel',
    storeId: 'CategoryDetailStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    proxy: {
        type: 'ajax',
        url: get_category_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    },
    sorters: [{
        property: 'name',
        direction: 'ASC'
    }]
});

Ext.create('Ext.data.Store', {
    storeId: 'TransactionSplitStore',
    fields:['transaction', 'idSplitTransaction', 'description', 'amount', 'category_name', 'category_id', 'valuta_date'],
    data: [],
    autoSync: false,
    proxy: {
        type: 'ajax',
        actionMethods: {
            create: 'POST',
            read: 'GET',
            update: 'PATCH',
            destroy: 'DELETE'
        },
        url: split_transaction_get,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        },
        writer: {
            type: 'json',
            writeAllFields : true,
            root: 'data'
        },
    },
});

}