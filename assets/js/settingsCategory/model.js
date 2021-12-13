Ext.define('CategoryModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int'},
        {name: 'groupId', type: 'int'},
        {name: 'name', type: 'string'},
        {name: 'groupName', type: 'string'},
        {name: 'treeIgnore', type: 'boolean'},
        {name: 'dashboardIgnore', type: 'boolean'},
    ]
});