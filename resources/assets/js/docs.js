window.$ = window.jQuery = require('jquery');

require('./plugins/prism');
require('./plugins/scotch');

$(function() {
    //responsive panel
    var scotchPanel = $('#slide-menu').scotchPanel({
        containerSelector: 'body',
        direction: 'left',
        duration: 200,
        transition: 'ease',
        distanceX: '70%',
        forceMinHeight: true,
        minHeight: '2500px',
        enableEscapeKey: true
    }).show();

    $('.toggle-slide').click(function() {
        scotchPanel.css('overflow', 'scroll');
        scotchPanel.toggle();
        return false;
    });

    $('.overlay').click(function() {
        scotchPanel.close();
    });

    // Add links to h2 headings
    $('.docs-wrapper').find('a[name]').each(function () {
        var anchor = $('<a href="#' + this.name + '"/>');
        $(this).parent().next('h2').wrapInner(anchor);
    });

    //Make current nav bold
    if ($('.sidebar ul').length) {
        var current = $('.sidebar ul').find('li a[href="' + window.location.pathname + '"]');
        if (current.length) {
            current.parent().css('font-weight', 'bold');
        }
    }
});