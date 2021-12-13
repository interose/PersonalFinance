Ext.create('Ext.data.Store', {
    model: 'TransferModel',
    storeId: 'TransferStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    proxy: {
        type: 'ajax',
        url: transfer_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    }
});