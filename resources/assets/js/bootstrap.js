window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
moment = require('moment')
require('eonasdan-bootstrap-datetimepicker');
require('bootstrap-sass');
require('bootstrap-material-design');
require('bootstrap-notify');
require('./material-dashboard');
swal = require('sweetalert2');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
// // see https://laracasts.com/discuss/channels/vue/vuejs-error-cannot-read-property-csrftoken-of-undefined
window.axios.defaults.headers.common = {
    'Authorization': 'Bearer ' + document.querySelector('meta[name="yap-token"]').getAttribute('content'),
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json'
};

window.$.notifyDefaults({
    delay: 4000,
    timer: 1000,
    showProgressbar: true,
    mouse_over: 'pause',
    icon_type: 'class',
    placement: {
        from: 'top',
        align: 'center'
    },
    template: '<div class="col-xs-10 col-sm-4 alert alert-{0} alert-with-icon" data-notify="container">' +
    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss"><i class="fa fa-close"></i></button>' +
    '<i data-notify="icon" class="fa fa-lg fa-{0}"></i>' +
    '<span data-notify="message">{2}</span>' +
    '<div class="progress" data-notify="progressbar">' +
    '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
    '</div>' +
    '<a href="{3}" target="{4}" data-notify="url"></a></div>'
})
