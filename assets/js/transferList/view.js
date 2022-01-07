export const grid = Ext.create('Ext.grid.Panel', {
    title: '',
    store: Ext.data.StoreManager.lookup('TransferStore'),
    minHeight: 500,
    columns: [{
        header: translation.transferGridColDate,
        dataIndex: 'executionDate',
        xtype: 'datecolumn',
        format: 'd.m.Y',
        sortable: false,
        hideable: false,
        groupable: false,
        width: 100
    }, {
        header: translation.transferGridColAmount,
        dataIndex: 'amount',
        sortable: false,
        hideable: false,
        groupable: false,
        align: 'right',
        width: 100,
        renderer: function (value, metadata, record) {
            return Ext.util.Format.number(value, '0,000.00')+' €';
        }
    }, {
        header: translation.transferGridColName,
        dataIndex: 'name',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.transferGridColInfo,
        dataIndex: 'info',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.transferGridColBank,
        dataIndex: 'bankName',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        header: translation.transferGridColIban,
        dataIndex: 'iban',
        sortable: false,
        hideable: false,
        groupable: false,
        flex: 1
    }, {
        xtype: 'actioncolumn',
        width: 25,
        menuDisabled: true,
        sortable: false,
        hideable: false,
        groupable: false,
        items: [{
            iconCls: 'fas fa-euro-sign my-action-column',
            align: 'center',
            handler: function(grid, rowIndex, colIndex) {
                const record = grid.getStore().getAt(rowIndex);
                const event = new Event('click');
                const button = document.getElementById('btn_new_transfer');
                button.dataset.transferId = record.get('id');
                button.dispatchEvent(event);
            }
        }]
    }],
});