Ext.define('AssignmentRuleModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'integer'},
        {name: 'rule', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'typeId', type: 'integer'},
        {name: 'transactionField', type: 'string'},
        {name: 'transactionFieldId', type: 'integer'},
        {name: 'category', type: 'string'},
        {name: 'categoryId', type: 'integer'}
    ]
});