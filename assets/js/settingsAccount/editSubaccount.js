import 'bootstrap-toggle';
import 'bootstrap-toggle/css/bootstrap-toggle.min.css';

(function() {
    $('#subaccount_isEnabled').bootstrapToggle({
        size: 'small',
        on: translation.toggleYes,
        off: translation.toggleNo,
        onstyle: 'my-toggle',
        width: 60
    });
})();