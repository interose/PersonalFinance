export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('CategoryGroupStore'),
    minHeight: 500,
    columns: [{
        header: translation.settingsColName,
        dataIndex: 'name',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 3,
    }, {
        header: translation.settingsColChildren,
        dataIndex: 'children',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1,
    }, {
        xtype: 'actioncolumn',
        width: 25,
        menuDisabled: true,
        sortable: false,
        hideable: false,
        groupable: false,
        items: [{
            iconCls: 'fas fa-pen my-action-column',
            align: 'center',
            handler: function(grid, rowIndex, colIndex) {
                const rec = grid.getStore().getAt(rowIndex);
                window.location = settings_category_group_edit+'?id='+rec.get('id');
            }
        }]
    }]
});