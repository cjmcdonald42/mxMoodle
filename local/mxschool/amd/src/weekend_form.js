// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Updates the options of the weekend form for Middlesex's Dorm and Student Functions Plugin.
 *
 * @module     local_mxschool/weekend_form
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'local_mxschool/locallib'], function($, ajax, notification, lib) {
    function updateStudents() {
        var promises = ajax.call([{
            methodname: 'local_mxschool_get_dorm_students',
            args: {
                dorm: $('.mx-form select#id_dorm').val()
            }
        }]);
        promises[0].done(function(data) {
            lib.updateSelect($('.mx-form select#id_student'), data);
        }).fail(notification.exception);
    }
    function updateWarning() {
        var promises = ajax.call([{
            methodname: 'local_mxschool_get_weekend_type',
            args: {
                datetime: {
                    hour: $('.mx-form select#id_departure_time_hour').val(),
                    minute: $('.mx-form select#id_departure_time_minute').val(),
                    ampm: $('.mx-form select#id_departure_time_ampm').val(),
                    day: $('.mx-form select#id_departure_date_day').val(),
                    month: $('.mx-form select#id_departure_date_month').val(),
                    year: $('.mx-form select#id_departure_date_year').val()
                }
            }
        }]);
        promises[0].done(function(data) {
            var warningDiv = $('.mx-form div[data-groupname="departure"]').next();
            if (data === 'Closed') {
                warningDiv.show();
            } else {
                warningDiv.hide();
            }
        }).fail(notification.exception);
    }
    return function() {
        $(document).ready(function() {
            updateStudents();
            updateWarning();
        });
        $('.mx-form select#id_dorm').change(updateStudents);
        $('.mx-form div[data-groupname="departure"]').change(updateWarning);
    };
});
