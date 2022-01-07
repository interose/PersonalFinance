import 'bootstrap-toggle';
import 'bootstrap-toggle/css/bootstrap-toggle.min.css';

(function() {
    $('#category_dashboardIgnore').bootstrapToggle({
        size: 'small',
        on: translation.toggleYes,
        off: translation.toggleNo,
        onstyle: 'my-toggle',
        width: 60
    });

    $('#category_treeIgnore').bootstrapToggle({
        size: 'small',
        on: translation.toggleYes,
        off: translation.toggleNo,
        onstyle: 'my-toggle',
        width: 60
    });
})();