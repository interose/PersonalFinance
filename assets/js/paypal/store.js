Ext.create('Ext.data.Store', {
    model: 'PayPalTransactionModel',
    storeId: 'PayPalTransactionStore',
    autoLoad: false,
    remoteSort: false,
    remoteFilter: false,
    autoSync: true,
    proxy: {
        type: 'ajax',
        url: paypal_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    },
    sorters: [{
        property: 'bookingDate',
        direction: 'DESC'
    }]
});