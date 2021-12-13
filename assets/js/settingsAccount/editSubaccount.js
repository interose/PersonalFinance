import 'bootstrap-toggle';
import 'bootstrap-toggle/css/bootstrap-toggle.min.css';

(function() {
    $('#subaccount_isEnabled').bootstrapToggle({
        size: 'small',
        on: toggleYes,
        off: toggleNo,
        onstyle: 'my-toggle',
        width: 60
    });
})();