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
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    ['jquery', 'core/ajax', 'core/str', 'core/notification', 'local_mxschool/locallib'], function($, ajax, str, notification, lib) {
        function addClasses() {
            $('.mx-form select#id_passengers').parent().parent().next().children().eq(1).children().eq(0).addClass('text-info');
            $('.mx-form select#id_driver').parent().parent().prev().children().eq(1).children().eq(0).addClass('text-info');
            var permissionsContainer = $('.mx-form fieldset#id_permissions').children().eq(1);
            permissionsContainer.children().eq(0).children().eq(1).children().eq(0).addClass('text-warning');
            permissionsContainer.children().eq(1).children().eq(1).children().eq(0).addClass('text-warning');
            permissionsContainer.children().eq(2).children().eq(1).children().eq(0).addClass('text-warning');
        }
        function updateStudentOptions() {
            var promises = ajax.call([{
                methodname: 'local_signout_get_off_campus_student_options',
                args: {
                    userid: $('.mx-form select#id_student').val(),
                    typeid: $('.mx-form select#id_type_select').val()
                }
            }]);
            promises[0].done(function(data) {
                $.when(
                    str.get_string('form:select:default', 'local_mxschool'),
                    str.get_string('off_campus_form_type_select_other', 'local_signout')
                ).done(function(select, other) {
                    data.types.unshift({
                        value: 0,
                        text: select
                    });
                    data.types.push({
                        value: -1,
                        text: other
                    });
                    lib.updateSelect($('.mx-form select#id_type_select'), data.types);
                });
                var permissionsFieldset = $('.mx-form fieldset#id_permissions');
                permissionsFieldset.hide();
                permissionsFieldset.next().show();
                var passengerWarningDiv = permissionsFieldset.children().eq(1).children().eq(0);
                if (data.passengerwarning && (data.type === 'passenger' || $('.mx-form select#id_type_select').val() == -1)) {
                    permissionsFieldset.next().hide();
                    passengerWarningDiv.children().eq(1).children().eq(0).text(data.passengerwarning);
                    passengerWarningDiv.show();
                    permissionsFieldset.show();
                } else {
                    passengerWarningDiv.hide();
                }
                var rideshareWarningDiv = permissionsFieldset.children().eq(1).children().eq(1);
                if (data.ridesharewarning && (data.type === 'rideshare' || $('.mx-form select#id_type_select').val() == -1)) {
                    permissionsFieldset.next().hide();
                    rideshareWarningDiv.children().eq(1).children().eq(0).text(data.ridesharewarning);
                    rideshareWarningDiv.show();
                    permissionsFieldset.show();
                } else {
                    rideshareWarningDiv.hide();
                }
                var typeWarningDiv = permissionsFieldset.children().eq(1).children().eq(2);
                if (data.typewarning) {
                    permissionsFieldset.next().hide();
                    typeWarningDiv.children().eq(1).children().eq(0).text(data.typewarning);
                    typeWarningDiv.show();
                    permissionsFieldset.show();
                } else {
                    typeWarningDiv.hide();
                }

                var passengersDiv = $('.mx-form select#id_passengers').parent().parent();
                if (data.type === 'driver') {
                    if(data.maydrivepassengers) {
                        passengersDiv.next().hide();
                        passengersDiv.show();
                        lib.updateMultiSelect($('.mx-form select#id_passengers'), data.passengers);
                    } else {
                        passengersDiv.hide();
                        passengersDiv.next().show();
                    }
                } else {
                    passengersDiv.hide();
                    passengersDiv.next().hide();
                }

                var driverDiv = $('.mx-form select#id_driver').parent().parent();
                if (data.type === 'passenger') {
                    driverDiv.prev().show();
                    driverDiv.show();
                    $.when(str.get_string('form:select:default', 'local_mxschool')).done(function(text) {
                        data.drivers.unshift({
                            value: 0,
                            text: text
                        });
                        lib.updateSelect($('.mx-form select#id_driver'), data.drivers, true);
                    });
                    $('.mx-form input#id_destination').prop('disabled', true);
                    $('.mx-form select#id_departure_time_hour').prop('disabled', true);
                    $('.mx-form select#id_departure_time_minute').prop('disabled', true);
                    $('.mx-form select#id_departure_time_ampm').prop('disabled', true);
                } else {
                    driverDiv.prev().hide();
                    driverDiv.hide();
                    $('.mx-form input#id_destination').prop('disabled', false);
                    $('.mx-form select#id_departure_time_hour').prop('disabled', false);
                    $('.mx-form select#id_departure_time_minute').prop('disabled', false);
                    $('.mx-form select#id_departure_time_ampm').prop('disabled', false);
                }

                var approverDiv = $('.mx-form select#id_approver').parent().parent();
                if (data.type || $('.mx-form select#id_type_select').val() == -1) {
                    approverDiv.show();
                } else {
                    approverDiv.hide();
                }
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
                $('.mx-form select#id_departure_time_ampm').val(data.departureampm === 'PM' ? 1 : 0);
            }).fail(function() {
                $('.mx-form input#id_destination').val(function() {return this.defaultValue;});
                $('.mx-form select#id_departure_time_hour > option').prop('selected', function() {return this.defaultSelected;});
                $('.mx-form select#id_departure_time_minute > option').prop('selected', function() {return this.defaultSelected;});
                $('.mx-form select#id_departure_time_ampm > option').prop('selected', function() {return this.defaultSelected;});
            });
        }
        return {
            setup: function() {
                $(document).ready(function() {
                    addClasses();
                    updateStudentOptions();
                    updateDriverDetails();
                });
                $('.mx-form select#id_student').change(updateStudentOptions);
                $('.mx-form select#id_type_select').change(updateStudentOptions);
                $('.mx-form select#id_driver').change(updateDriverDetails);
            }
        };
    }
);
