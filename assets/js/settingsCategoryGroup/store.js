Ext.create('Ext.data.Store', {
    model: 'CategoryGroupModel',
    storeId: 'CategoryGroupStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    autoSync: true,
    proxy: {
        type: 'ajax',
        url: settings_category_group_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    }
});