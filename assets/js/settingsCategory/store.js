Ext.create('Ext.data.Store', {
    model: 'CategoryModel',
    storeId: 'CategoryStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    autoSync: true,
    proxy: {
        type: 'ajax',
        url: settings_category_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    }
});