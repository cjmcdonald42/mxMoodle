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
 * Updates the options of the on-campus signout form for Middlesex's eSignout Subplugin.
 *
 * @module     local_signout/on_campus_form
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    ['jquery', 'core/ajax', 'core/str', 'core/notification', 'local_mxschool/locallib'], function($, ajax, str, notification, lib) {
        function updateStudentOptions() {
            var promises = ajax.call([{
                methodname: 'local_signout_get_on_campus_student_options',
                args: {
                    userid: $('.mx-form select#id_student').val()
                }
            }]);
            promises[0].done(function(data) {
                $.when(
                    str.get_string('form_select_default', 'local_mxschool'),
                    str.get_string('on_campus_form_location_select_other', 'local_signout')
                ).done(function(select, other) {
                    data.locations.unshift({
                        value: 0,
                        text: select
                    });
                    data.locations.push({
                        value: -1,
                        text: other
                    });
                    lib.updateSelect($('.mx-form select#id_location_select'), data.locations);
                });
                var permissionsFieldset = $('.mx-form fieldset#id_permissions');
                if ($('.mx-form select#id_location_select').val() === '-1' && data.grade === 11) {
                    permissionsFieldset.next().hide();
                    permissionsFieldset.show();
                } else {
                    permissionsFieldset.next().show();
                    permissionsFieldset.hide();
                }
            }).fail(notification.exception);
        }
        return function() {
            $(document).ready(updateStudentOptions);
            $('.mx-form select#id_student').change(updateStudentOptions);
            $('.mx-form select#id_location_select').change(updateStudentOptions);
        };
    }
);
