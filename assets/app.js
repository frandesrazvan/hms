import './css/app.css';
import './js/lib/jquery-1.11.1.min.js';
import './js/lib/bootstrap.min.js';
import './js/lib/bootstrap-select.js';
import './js/lib/countries.js';
import './js/lib/dropzone.js';
import './js/lib/bootstrap-datepicker.js';
import './js/lib/jquery.autocomplete.js';
import './js/lib/jasny-bootstrap.min.js';
import './js/lib/jquery.validate.min.js';
import './js/scripts.js';
import './js/extra.js';


// start the Stimulus application
import './bootstrap';

const $ = require('jquery');
global.$ = global.jQuery = $


$(function () {
    $('a.nav-link').on('click', function (e) {
        if ($(this).closest('li.nav-item').hasClass('active')) {
            e.preventDefault();
        }
    })

    if (localStorage.getItem('hotelsDropDown')) {
        $("#hotelsDropDown option").eq(localStorage.getItem('hotelsDropDown')).prop('selected', true);
    }

    $("#hotelsDropDown").on('change', function () {
        localStorage.setItem('hotelsDropDown', $('option:selected', this).index());
    });

});

$(document).ready(function () {
    $('#hotelsDropDown').change(function () {
        var inputValue = $(this).val();

        window.location.href = $('.table-items').data('userManagement') + '/' + inputValue;
    });

    $('.fa-trash').on('click', function (e) {
        if (confirm("Are you sure you want to delete this?")) {
            $.ajax({
                type: "DELETE",
                url: $('.table-items').data('userManagementDelete') + this.id,
            })
        }
    })
});
