Ext.define('CategoryGroupModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'name', type: 'string'},
        {name: 'color', type: 'string'},
        {name: 'children', type: 'int'}
    ]
});