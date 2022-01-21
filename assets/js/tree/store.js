Ext.create('Ext.data.TreeStore', {
    model: 'TreeModel',
    storeId: 'TreeStore',
    autoLoad: false,
    remoteSort: false,
    remoteFilter: false,
    root: { expanded: false },
    proxy: {
        type: 'ajax',
        url: get_tree_data_full_year,
        reader: {
            type: 'json'
        }
    },
    listeners: {
        load: function(store, records, successful, eOpts) {
            store.getRootNode().expand();
        },
        scope: this
    }
});

Ext.create('Ext.data.Store', {
    model: 'TransactionModel',
    storeId: 'TransactionStore',
    autoLoad: false,
    remoteSort: false,
    remoteFilter: false,
    autoSync: true,
    proxy: {
        type: 'ajax',
        url: tree_get_detail_transactions,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    },
    sorters: [{
        property: 'valuta_date',
        direction: 'ASC'
    }]
});