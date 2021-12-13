export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('AssignmentRuleStore'),
    id: 'mainGrid',
    minHeight: 500,
    columns: [{
        header: 'Rule',
        dataIndex: 'rule',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: 'Type',
        dataIndex: 'type',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: 'Transaction Field',
        dataIndex: 'transactionField',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: 'Kategorie',
        dataIndex: 'category',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        xtype: 'actioncolumn',
        width: 50,
        menuDisabled: true,
        sortable: false,
        hideable: false,
        groupable: false,
        items: [{
            iconCls: 'fas fa-pen my-action-column icon-margin',
            align: 'center',
            handler: function(grid, rowIndex, colIndex) {
                const rec = grid.getStore().getAt(rowIndex);
                window.location = settings_assignment_edit+'?id='+rec.get('id');
            }
        }, {
            iconCls: 'fas fa-trash my-action-column',
            align: 'center',
            handler: function(grid, rowIndex, colIndex) {
                const rec = grid.getStore().getAt(rowIndex);

                if (confirm('Are you sure?')) {
                    window.location = settings_assignment_delete+'?id='+rec.get('id');
                }
            }
        }]
    }]
});
