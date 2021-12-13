Ext.create('Ext.data.Store', {
    model: 'AssignmentRuleModel',
    storeId: 'AssignmentRuleStore',
    autoLoad: true,
    remoteSort: false,
    remoteFilter: false,
    autoSync: true,
    proxy: {
        type: 'ajax',
        url: settings_assignment_data,
        reader: {
            type: 'json',
            rootProperty: 'data',
            successProperty: 'success'
        }
    }
});