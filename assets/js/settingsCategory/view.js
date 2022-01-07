export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('CategoryStore'),
    minHeight: 500,
    columns: [{
        header: translation.settingsColGroup,
        dataIndex: 'groupName',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1,
    }, {
        header: translation.settingsColName,
        dataIndex: 'name',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1,
    }, {
        header: translation.settingsColTreeIgnore,
        sortable: false,
        hideable: false,
        groupable: false,
        dataIndex: 'treeIgnore',
        align: 'center',
        width: 120,
        renderer: function( value, metadata, record ) {
            if (value) {
                metadata.innerCls = 'check-column-green';
                return '<i class="fas fa-check"></i>';
            } else {
                return '';
            }
        }
    }, {
        header: translation.settingsColDashboardIgnore,
        sortable: false,
        hideable: false,
        groupable: false,
        dataIndex: 'dashboardIgnore',
        align: 'center',
        width: 140,
        renderer: function( value, metadata, record ) {
            if (value) {
                metadata.innerCls = 'check-column-green';
                return '<i class="fas fa-check"></i>';
            } else {
                return '';
            }
        }
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
                window.location = settings_category_edit+'?id='+rec.get('id');
            }
        }]
    }]
});