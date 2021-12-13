Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Store',
    model: 'modelSecondaryAccountTransaction',
    storeId: 'SecondaryAccountTransactionStore',
    autoLoad: false,
    remoteSort: false,
    remoteFilter: false,
    proxy: {
        type: 'ajax',
        url: get_secondary_account_data,
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

Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Store',
    model: 'modelSecondaryAccount',
    storeId: 'SecondaryAccountStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    proxy: {
        type: 'ajax',
        url: get_secondary_subaccounts,
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