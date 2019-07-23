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
 * Updates the options of the off-campus signout form for Middlesex's eSignout Subplugin.
 *
 * @module      local_signout/off_campus_form
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    ['jquery', 'core/ajax', 'core/str', 'core/notification', 'local_mxschool/locallib'], function($, ajax, str, notification, lib) {
        function updateTypeSubfields() {
            var passengersDiv = $('.mx-form select#id_passengers').parent().parent();
            var driverDiv = $('.mx-form select#id_driver').parent().parent();
            var otherDiv = $('.mx-form input#id_type_other').parent().parent();
            if (!$('.mx-form input#id_type_select_Driver').prop('checked')) {
                passengersDiv.hide();
                passengersDiv.next().hide();
            }
            if ($('.mx-form input#id_type_select_Passenger').prop('checked')) {
                driverDiv.prev().show();
                driverDiv.show();
            } else {
                driverDiv.prev().hide();
                driverDiv.hide();
            }
            if ($('.mx-form input#id_type_select_Other').prop('checked')) {
                otherDiv.show();
            } else {
                otherDiv.hide();
            }
            var isPassenger = $('.mx-form input#id_type_select_Passenger').prop('checked');
            $('.mx-form input#id_destination').prop('disabled', isPassenger);
            $('.mx-form select#id_departure_time_hour').prop('disabled', isPassenger);
            $('.mx-form select#id_departure_time_minute').prop('disabled', isPassenger);
            $('.mx-form select#id_departure_time_ampm').prop('disabled', isPassenger);
        }
        function updateStudentOptions() {
            var promises = ajax.call([{
                methodname: 'local_signout_get_off_campus_student_options',
                args: {
                    userid: $('.mx-form select#id_student').val()
                }
            }]);
            promises[0].done(function(data) {
                lib.updateRadio($('.mx-form div[data-groupname="type_select"] input'), data.types);
                var passengersDiv = $('.mx-form select#id_passengers').parent().parent();
                if ($('.mx-form input#id_type_select_Driver').prop('checked')) {
                    if(data.maydrivepassengers) {
                        passengersDiv.show();
                        passengersDiv.next().hide();
                    } else {
                        passengersDiv.hide();
                        passengersDiv.next().show();
                    }
                }
                var permissionsFieldset = $('.mx-form fieldset#id_permissions');
                if ($('.mx-form input#id_type_select_Passenger').prop('checked') && data.mayridewith !== 'Any Driver') {
                    permissionsFieldset.next().hide();
                    permissionsFieldset.show();
                    var parentPermissionDiv = permissionsFieldset.children().eq(1).children().eq(0);
                    var specificDriversDiv = permissionsFieldset.children().eq(1).children().eq(1);
                    if (data.mayridewith === 'Parent Permission') {
                        parentPermissionDiv.show();
                        specificDriversDiv.hide();
                    } else if (data.mayridewith === 'Specific Drivers') {
                        parentPermissionDiv.hide();
                        var specificDriversStatic = specificDriversDiv.children().eq(1).children().eq(0);
                        specificDriversStatic.contents().slice(1).remove();
                        specificDriversStatic.append(data.specificdrivers);
                        specificDriversDiv.show();
                    }
                } else {
                    permissionsFieldset.next().show();
                    permissionsFieldset.hide();
                }
                lib.updateMultiSelect($('.mx-form select#id_passengers'), data.passengers);
                $.when(str.get_string('form_select_default', 'local_mxschool')).done(function(text) {
                    data.drivers.unshift({
                        value: 0,
                        text: text
                    });
                    lib.updateSelect($('.mx-form select#id_driver'), data.drivers, true);
                });
            }).fail(notification.exception);
        }
        function updateDriverDetails() {
            var promises = ajax.call([{
                methodname: 'local_signout_get_off_campus_driver_details',
                args: {
                    offcampusid: parseInt($('.mx-form select#id_driver').val())
                }
            }]);
            promises[0].done(function(data) {
                $('.mx-form input#id_destination').val(data.destination);
                $('.mx-form select#id_departure_time_hour').val(data.departurehour);
                $('.mx-form select#id_departure_time_minute').val(data.departureminute);
                $('.mx-form select#id_departure_time_ampm').val(data.departureampm ? 1 : 0);
            }).fail(function() {
                $('.mx-form input#id_destination').val(function() {return this.defaultValue;});
                $('.mx-form select#id_departure_time_hour > option').prop('selected', function() {return this.defaultSelected;});
                $('.mx-form select#id_departure_time_minute > option').prop('selected', function() {return this.defaultSelected;});
                $('.mx-form select#id_departure_time_ampm > option').prop('selected', function() {return this.defaultSelected;});
            });
        }
        return function() {
            $(document).ready(function() {
                updateTypeSubfields();
                updateStudentOptions();
                updateDriverDetails();
            });
            $('.mx-form select#id_student').change(updateStudentOptions);
            $('.mx-form div[data-groupname="type_select"]').change(function() {
                updateTypeSubfields();
                updateStudentOptions();
            });
            $('.mx-form select#id_driver').change(updateDriverDetails);
        };
    }
);
