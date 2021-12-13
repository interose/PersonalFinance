Ext.Date.monthNames = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
Ext.Date.monthNumbers = {Januar: 0, Jan: 0, Februar: 1, Feb: 1, März: 2, Mär: 2, April: 3, Apr: 3, Mai: 4, Mai: 4 , Juni: 5, Jun: 5, Juli: 6, Jul: 6, August: 7, Aug: 7, September: 8, Sep: 8, Oktober: 9, Okt: 9, November: 10, Nov: 10, Dezember: 11, Dez: 11};
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

        console.log(m);
        console.log(d);

        me.selectMonth = new Date(( d[0]+1 ) +'/1/'+d[1]);
        me.setValue(new Date(( d[0]+1 ) +'/1/'+d[1]));
    }
});