import {splitwindow} from "./components/splitwindow";

export default function createView() {
    defineMonthpicker();

    const splitwindow = createSplitWindow();

    return Ext.create('Ext.grid.Panel', {
        title: '',
        store: Ext.data.StoreManager.lookup('TransactionStore'),
        minHeight: 500,
        tbar: [{
            xtype: 'button',
            text: translation.gridButtonBack,
            listeners: {
                click: function(el){
                    var picker = Ext.ComponentQuery.query('monthfield')[0];
                    var dt = Ext.Date.subtract(picker.getValue(), Ext.Date.MONTH, 1);

                    picker.setValue(dt);
                    picker.fireEvent('select', picker, picker.selectMonth);
                }
            }
        },{
            xtype: 'monthfield',
            format: 'F, Y',
            value: new Date(),
            listeners: {
                select: function(el) {
                    var value = el.getValue();
                    var year = value.getFullYear();
                    var month = value.getMonth() + 1;

                    var grid = el.up('grid');

                    grid.store.load({
                        scope: this,
                        params: {
                            year: year,
                            month: month
                        },
                        callback: function(records, operation, success) {
                            grid.setLoading(false);
                        }
                    });
                }
            }
        }, {
            xtype: 'button',
            text: translation.gridButtonNext,
            listeners: {
                click: function(el){
                    var picker = Ext.ComponentQuery.query('monthfield')[0];
                    var dt = Ext.Date.add(picker.getValue(), Ext.Date.MONTH, 1);
                    picker.setValue(dt);
                    picker.fireEvent('select', picker, picker.selectMonth);
                }
            }
        }, '->', {
            xtype: 'tbtext',
            text: translation.gridFilterRecipient
        }, {
            xtype:'textfield',
            reference: 'filter_name'
        }, {
            xtype: 'tbtext',
            text: translation.gridFilterDescription
        }, {
            xtype:'textfield',
            reference: 'filter_description'
        }, {
            text: translation.gridFilterBtn,
            cls: 'btnMyDefault',
            iconCls: 'button-filter-small',
            listeners: {
                click: function() {
                    let filterName = Ext.ComponentQuery.query('textfield[reference=filter_name]')[0].getValue();
                    let filterDescription = Ext.ComponentQuery.query('textfield[reference=filter_description]')[0].getValue();

                    const store = Ext.data.StoreManager.lookup('TransactionStore');

                    store.load({
                        params: {
                            name: filterName,
                            description: filterDescription
                        }
                    });
                }
            }
        }, {
            text: translation.gridFilterBtnClear,
            cls: 'btnMyDefault',
            iconCls: 'button-filter-clear',
            listeners: {
                click: function() {
                    Ext.ComponentQuery.query('textfield[reference=filter_name]')[0].setValue('');
                    Ext.ComponentQuery.query('textfield[reference=filter_description]')[0].setValue('');

                    var picker = Ext.ComponentQuery.query('monthfield')[0];
                    picker.fireEvent('select', picker, picker.selectMonth);
                }
            }
        }],
        viewConfig: {
            getRowClass: function(record, rowIndex, rowParams, store) {
                return record.get('split') ? 'row-split' : '';
            }
        },
        columns: [{
            text: '',
            dataIndex: 'split',
            sortable: false,
            hideable: false,
            groupable: false,
            align: 'center',
            width: 25,
            renderer: function(value, metadata, record) {
                if (value === true) {
                    return '<i class="fas fa-code-branch"></i>';
                } else {
                    return '';
                }
            }
        },{
            text: translation.gridColValuta,
            dataIndex: 'valuta_date',
            xtype: 'datecolumn',
            format: 'd.m.Y',
            sortable: true,
            hideable: false,
            groupable: false,
            menuDisabled: false,
            width: 200
        }, {
            header: translation.gridColRecipient,
            dataIndex: 'name',
            sortable: false,
            hideable: false,
            groupable: false,
            flex: 1,
            renderer: function (value, meta, record) {
                meta.tdAttr = 'data-qtip="' + record.get('description_raw') + '"';
                return value;
            }
        }, {
            header: translation.gridColCategory,
            dataIndex: 'category_id',
            sortable: false,
            hideable: false,
            groupable: false,
            width: 300,
            editor: {
                xtype: 'combo',
                completeOnEnter: true,
                store: Ext.data.StoreManager.lookup('CategoryStore'),
                typeAhead: true,
                anyMatch: true,
                triggerAction: 'all',
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local'
            },
            renderer: function(value, metadata, record) {
                return record.get('category_name');
            }
        }, {
            header: translation.gridColAmount,
            dataIndex: 'amount',
            sortable: false,
            hideable: false,
            groupable: false,
            align: 'right',
            width: 100,
            renderer: function(value, metadata, record) {
                if(record.get('credit_debit') === 'credit') {
                    return '<span class="positiv">'+Ext.util.Format.number(value, '0,000.00')+' €</span>';
                }
                else if(record.get('credit_debit') === 'debit') {
                    return '<span class="negativ">-'+Ext.util.Format.number(value, '0,000.00')+' €</span>';
                }

                return Ext.util.Format.number(v, '0,000.00')+' €';
            }
        }],
        plugins: {
            ptype: 'cellediting',
            clicksToEdit: 1
        },
        listeners: {
            edit: function(editor, e) {
                let store = Ext.data.StoreManager.lookup('CategoryStore');
                let category = store.getById(e.value);
                e.record.set('category_name', category.get('name'));

                store = Ext.data.StoreManager.lookup('TransactionStore');
                store.sync();
            },
            itemdblclick: function(el, record, item, index, e, eOpts) {
                const _self = this;
                const idTransaction = record.get('id_transaction');
                const valutaDate = record.get('valuta_date');
                let splitAmount = 0;

                splitwindow.show();

                const store = Ext.data.StoreManager.lookup('TransactionSplitStore');
                store.load({
                    params: {
                        idTransaction: idTransaction
                    },
                    callback: function(records, operation, success) {
                        let response = Ext.JSON.decode(operation.getResponse().responseText);
                        Ext.getCmp('panelDescription').body.update(response.transaction.description);
                        splitAmount = response.transaction.amount;
                        Ext.getCmp('panelAmount').body.update(Ext.util.Format.number(splitAmount, '0,000.00')+' €');

                        splitwindow.idTransaction = idTransaction;
                        splitwindow.valutaDate = valutaDate;
                        splitwindow.splitAmount = splitAmount;
                    }
                });
            }
        }
    });
}

function defineMonthpicker() {
    Ext.define('extFinance.view.main.Monthfield', {
        extend:'Ext.form.field.Date',
        alias: 'widget.monthfield',
        requires: ['Ext.picker.Month'],
        alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
        selectMonth: null,
        createPicker: function() {
            var me = this,
                format = Ext.String.format;
            return Ext.create('Ext.picker.Month', {
                pickerField: me,
                cancelText: translation.gridPickerBtnCancel,
                okText: translation.gridPickerBtnOk,
                ownerCt: me.ownerCt,
                renderTo: document.body,
                floating: true,
                hidden: true,
                focusOnShow: true,
                minDate: me.minValue,
                maxDate: me.maxValue,
                disabledDatesRE: me.disabledDatesRE,
                disabledDatesText: me.disabledDatesText,
                disabledDays: me.disabledDays,
                disabledDaysText: me.disabledDaysText,
                format: me.format,
                showToday: me.showToday,
                startDay: me.startDay,
                minText: format(me.minText, me.formatDate(me.minValue)),
                maxText: format(me.maxText, me.formatDate(me.maxValue)),
                listeners: {
                    select:        { scope: me,   fn: me.onSelect     },
                    monthdblclick: { scope: me,   fn: me.onOKClick     },
                    yeardblclick:  { scope: me,   fn: me.onOKClick     },
                    OkClick:       { scope: me,   fn: me.onOKClick     },
                    CancelClick:   { scope: me,   fn: me.onCancelClick },
                    afterrender : {
                        scope : me,
                        fn : function(c) {
                            var me = c;
                            me.el.on("mousedown", function(e) {
                                e.preventDefault();
                            }, c);
                        }
                    }
                },
                keyNavConfig: {
                    esc: function() {
                        me.collapse();
                    }
                }
            });
        },
        onCancelClick: function() {
            var me = this;
            me.selectMonth = null;
            me.collapse();
        },
        onOKClick: function() {
            var me = this;
            if( me.selectMonth ) {
                me.setValue(me.selectMonth);
                me.fireEvent('select', me, me.selectMonth);
            }
            me.collapse();
        },
        onSelect: function(m, d) {
            var me = this;
            me.selectMonth = new Date(( d[0]+1 ) +'/1/'+d[1]);
            me.setValue(new Date(( d[0]+1 ) +'/1/'+d[1]));
        }
    });
}

function createSplitWindow() {
    return Ext.create('Ext.window.Window', {
        title: 'Buchung splitten',
        id: 'splitWindow',
        height: 400,
        width: 700,
        layout: {
            type: 'vbox',
            align: 'stretch',
            pack: 'start'
        },
        closeAction: 'method-hide',
        listeners: {
            close: {
                fn: function() {
                    const store = Ext.data.StoreManager.lookup('TransactionStore');
                    store.reload();
                }
            },
            scope: this
        },
        items: [{
            xtype: 'container',
            height: 80,
            layout: {
                type: 'hbox',
                align: 'stretch',
                pack: 'start'
            },
            items: [{
                xtype: 'panel',
                id: 'panelDescription',
                layout: 'fit',
                bodyPadding: '10 15 10 15',
                html: '',
                border: false,
                style: {
                    'word-wrap': 'anywhere',
                    'border-bottom': '1px solid #d0d0d0',
                    'border-top': '1px solid #d0d0d0',
                },
                flex: 9
            }, {
                xtype: 'panel',
                layout: 'fit',
                id: 'panelAmount',
                bodyPadding: '10 15 10 15',
                border: false,
                flex: 2,
                html: '',
                style: {
                    'word-wrap': 'anywhere',
                    'border-bottom': '1px solid #d0d0d0',
                    'border-top': '1px solid #d0d0d0',
                },
            }]
        },{
            xtype: 'grid',
            id: 'detailGrid',
            border: false,
            flex: 1,
            listeners: {
                edit: {
                    fn: function(editor, context) {

                        let parentWindow = Ext.getCmp('splitWindow');
                        parentWindow.splitAmount = parentWindow.splitAmount - context.record.get('amount');
                        Ext.getCmp('panelAmount').body.update(Ext.util.Format.number(parentWindow.splitAmount, '0,000.00')+' €');

                        let store = Ext.data.StoreManager.lookup('CategoryStore');
                        let category = store.getById(context.record.get('category_id'));
                        context.record.set('category_name', category.get('name'));

                        store = Ext.data.StoreManager.lookup('TransactionSplitStore');
                        store.sync();
                    }
                },
                scope: this
            },
            columns: [{
                xtype: 'datecolumn',
                dataIndex: 'valuta_date',
                header: 'Datum',
                width: 200,
                format: 'd.m.Y',
                editor: {
                    xtype: 'datefield',
                    format: 'd.m.Y'
                }
            }, {
                header: 'Verwendungszweck',
                flex: 2,
                dataIndex: 'description',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                header: 'Kategorie',
                dataIndex: 'category_id',
                sortable: false,
                hideable: false,
                groupable: false,
                width: 200,
                editor: {
                    xtype: 'combo',
                    completeOnEnter: true,
                    store: Ext.data.StoreManager.lookup('CategoryDetailStore'),
                    typeAhead: true,
                    triggerAction: 'all',
                    displayField: 'name',
                    valueField: 'id',
                    queryMode: 'local'
                },
                renderer: function(value, metadata, record) {
                    return record.get('category_name');
                }
            }, {
                header: 'Betrag',
                flex: 1,
                dataIndex: 'amount',
                editor: {
                    xtype: 'numberfield'
                },
                renderer: function (value, metadata, record) {
                    if (value) {
                        return Ext.util.Format.number(value, '0,000.00')+' €';
                    }
                }
            }, {
                dataIndex: 'transaction',
                hidden: true
            }, {
                dataIndex: 'idSplitTransaction',
                hidden: true
            }],
            store: Ext.data.StoreManager.lookup('TransactionSplitStore'),
            plugins: {
                ptype: 'rowediting',
                clicksToEdit: 1,
                pluginId: 'roweditingId',
                autoCancel: false,
                listeners: {
                    canceledit: function(editor, context, eOpts ){
                        context.store.remove(context.record);
                        context.store.sync();
                    }
                }
            },
            tbar: [{
                text: 'Buchung hinzufügen',
                cls: 'btnUpdateTransactions',
                handler: function() {
                    let grid = Ext.getCmp('detailGrid');
                    let parentWindow = Ext.getCmp('splitWindow');
                    let rowediting = grid.getPlugin('roweditingId');
                    rowediting.cancelEdit();
                    grid.store.insert(0, {
                        transaction: parentWindow.idTransaction,
                        idSplitTransaction: 0,
                        description: '',
                        amount: '',
                        valuta_date: parentWindow.valutaDate
                    });
                    rowediting.startEdit(0, 0);
                }
            }]
        }]
    });
}