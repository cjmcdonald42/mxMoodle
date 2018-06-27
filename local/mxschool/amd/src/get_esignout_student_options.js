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
 * Updates the types and passengers fields of the eSignout form for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_esignout_driver_details
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/ajax', 'core/notification'], function($, str, ajax, notification) {
    return  {
        updateWithPermissions: function() {
            $('.mx-form select#id_student').change(function() {
                var promises = ajax.call([{
                    methodname: 'local_mxschool_get_esignout_student_options',
                    args: {
                        userid: $('.mx-form select#id_student > option:selected').val()
                    }
                }]);
                promises[0].done(function(data) {
                    $('.mx-form input[name="maydrivepassengers"]').val(data.maydrivepassengers ? '1' : '0');
                    // console.log(data);
                    // var passengersSelect = $('.mx-form select#id_passengers');
                    // passengersSelect.empty();
                    // $.each(data.passengers, function(index, student) {
                    //     passengersSelect.append($('<option></option>').attr('value', student.userid).text(student.name));
                    // });
                    var driverSelect = $('.mx-form select#id_driver');
                    driverSelect.empty();
                    $.when(str.get_string('esignout_form_driver_default', 'local_mxschool')).done(function(text) {
                        driverSelect.append($('<option></option>').attr('value', 0).text(text));
                        $.each(data.drivers, function(index, student) {
                            driverSelect.append($('<option></option>').attr('value', student.esignoutid).text(student.name));
                        });
                    });
                }).fail(notification.exception);
            });
        }
    };
});
