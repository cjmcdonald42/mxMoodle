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
 * Updates the options of the vacation travel form for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @module     local_mxschool/vacation_travel_form
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    ['jquery', 'core/ajax', 'core/str', 'core/notification', 'local_mxschool/locallib'], function($, ajax, str, notification, lib) {
        function update() {
            var returnEnabled = $('.mx-form fieldset#id_return').length;
            var depMXTransportationChecked = $('.mx-form div[data-groupname="dep_mxtransportation"] input:checked');
            var depTypeChecked = $('.mx-form div[data-groupname="dep_type"] input:checked');
            var depSiteChecked = $('.mx-form div[data-groupname="dep_site"] input:checked');
            var retMXTransportationChecked = $('.mx-form div[data-groupname="ret_mxtransportation"] input:checked');
            var retTypeChecked = $('.mx-form div[data-groupname="ret_type"] input:checked');
            var retSiteChecked = $('.mx-form div[data-groupname="ret_site"] input:checked');
            var promises = ajax.call([{
                methodname: 'local_mxschool_get_vacation_travel_options',
                args: {
                    departure: {
                        mxtransportation: depMXTransportationChecked.length ? depMXTransportationChecked.val() == 1 : undefined,
                        type: depTypeChecked.length ? depTypeChecked.val() : undefined,
                        site: depSiteChecked.length ? depSiteChecked.val() : undefined
                    }, return: {
                        mxtransportation: returnEnabled && retMXTransportationChecked.length
                            ? retMXTransportationChecked.val() == 1 : undefined,
                        type: returnEnabled && retTypeChecked.length ? retTypeChecked.val() : undefined,
                        site: retSiteChecked.length ? retSiteChecked.val() : undefined
                    }
                }
            }]);
            promises[0].done(function(data) {
                lib.updateSelect($('.mx-form select#id_student'), data.students, $('.mx-form input[name="id"]').val() !== '0');
                if (!depMXTransportationChecked.length) {
                    $('.mx-form div[data-groupname="dep_type"]').hide();
                } else {
                    lib.updateRadio($('.mx-form div[data-groupname="dep_type"] input'), data.departure.types);
                    $('.mx-form div[data-groupname="dep_type"]').show();
                }
                var depSiteDiv = $('.mx-form div[data-groupname="dep_site"]');
                var depDetailsDiv = $('.mx-form input#id_dep_details').parent().parent();
                var depCarrierDiv = $('.mx-form input#id_dep_carrier').parent().parent();
                var depNumberDiv = $('.mx-form input#id_dep_number').parent().parent();
                var depTimeDiv = $('.mx-form div[data-groupname="dep_variable"]');
                var depInternationalDiv = $('.mx-form div[data-groupname="dep_international"]');
                if (!depTypeChecked.length) {
                    depSiteDiv.hide();
                    depDetailsDiv.hide();
                    depCarrierDiv.hide();
                    depNumberDiv.hide();
                    depTimeDiv.hide();
                    depInternationalDiv.hide();
                } else {
                    var depMXTransportation = depMXTransportationChecked.val();
                    var depType = depTypeChecked.val();
                    if(depMXTransportation === '1') {
                        lib.updateRadio($('.mx-form div[data-groupname="dep_site"] input'), data.departure.sites);
                        var unlocalized = 'vacation_travel_form_departure_dep_site' + (
                            depType === 'Plane' || depType === 'Train' ? '_' + depType : ''
                        );
                        $.when(str.get_string(unlocalized, 'local_mxschool')).done(function(text) {
                            depSiteDiv.children().eq(0).text(text);
                            depSiteDiv.show();
                        });
                    } else {
                        depSiteDiv.hide();
                    }
                    var depSite = $('.mx-form div[data-groupname="dep_site"] input:checked').val();
                    if (depType === 'Car' || depType === 'Non-MX Bus' || (depMXTransportation === '1' && depSite === '0')) {
                        var unlocalized = 'vacation_travel_form_departure_dep_details' + (depType === 'Car' ? '_' + depType : '');
                        $.when(str.get_string(unlocalized, 'local_mxschool')).done(function(text) {
                            depDetailsDiv.children().eq(0).text(text);
                            depDetailsDiv.show();
                        });
                    } else {
                        depDetailsDiv.hide();
                    }
                    if (depType === 'Plane' || depType === 'Train' || depType === 'Bus') {
                        $.when(str.get_string('vacation_travel_form_departure_dep_carrier_' + depType, 'local_mxschool'))
                        .done(function(text) {
                            depCarrierDiv.children().eq(0).text(text);
                            depCarrierDiv.show();
                        });
                        $.when(str.get_string('vacation_travel_form_departure_dep_number_' + depType, 'local_mxschool'))
                        .done(function(text) {
                            depNumberDiv.children().eq(0).text(text);
                            depNumberDiv.show();
                        });
                    } else {
                        depCarrierDiv.hide();
                        depNumberDiv.hide();
                    }
                    var suffix = depMXTransportation === '1' && (
                        depType === 'Plane' || depType === 'Train' || depType === 'Bus'
                    ) ? '_' + depType : '';
                    $.when(str.get_string('vacation_travel_form_departure_dep_variable' + suffix, 'local_mxschool'))
                    .done(function(text) {
                        depTimeDiv.children().eq(0).text(text);
                        var depHourSelect = $('.mx-form select#id_dep_variable_time_hour');
                        var depMinuteSelect = $('.mx-form select#id_dep_variable_time_minute');
                        var depAMPMSelect = $('.mx-form select#id_dep_variable_time_ampm');
                        var depDaySelect = $('.mx-form select#id_dep_variable_date_day');
                        var depMonthSelect = $('.mx-form select#id_dep_variable_date_month');
                        var depYearSelect = $('.mx-form select#id_dep_variable_date_year');
                        var depCalendar = $('.mx-form a#id_dep_variable_date_calendar');
                        if (data.departure.default.year) {
                            depHourSelect.val(data.departure.default.hour);
                            depMinuteSelect.val(data.departure.default.minute);
                            depAMPMSelect.val(data.departure.default.ampm ? 1 : 0);
                            depDaySelect.val(data.departure.default.day);
                            depMonthSelect.val(data.departure.default.month);
                            depYearSelect.val(data.departure.default.year);
                            depHourSelect.prop('disabled', true);
                            depMinuteSelect.prop('disabled', true);
                            depAMPMSelect.prop('disabled', true);
                            depDaySelect.prop('disabled', true);
                            depMonthSelect.prop('disabled', true);
                            depYearSelect.prop('disabled', true);
                            depCalendar.hide();
                            dayOfWeek();
                        } else {
                            depHourSelect.prop('disabled', false);
                            depMinuteSelect.prop('disabled', false);
                            depAMPMSelect.prop('disabled', false);
                            depDaySelect.prop('disabled', false);
                            depMonthSelect.prop('disabled', false);
                            depYearSelect.prop('disabled', false);
                            depCalendar.show();
                        }
                        depTimeDiv.show();
                    });
                    if (depType === 'Plane' && depMXTransportation === '1') {
                        depInternationalDiv.show();
                    } else {
                        depInternationalDiv.hide();
                    }
                }
                if (returnEnabled) {
                    if (!retMXTransportationChecked.length) {
                        $('.mx-form div[data-groupname="ret_type"]').hide();
                    } else {
                        lib.updateRadio($('.mx-form div[data-groupname="ret_type"] input'), data.return.types);
                        $('.mx-form div[data-groupname="ret_type"]').show();
                    }
                    var retSiteDiv = $('.mx-form div[data-groupname="ret_site"]');
                    var retDetailsDiv = $('.mx-form input#id_ret_details').parent().parent();
                    var retCarrierDiv = $('.mx-form input#id_ret_carrier').parent().parent();
                    var retNumberDiv = $('.mx-form input#id_ret_number').parent().parent();
                    var retTimeDiv = $('.mx-form div[data-groupname="ret_variable"]');
                    var retInternationalDiv = $('.mx-form div[data-groupname="ret_international"]');
                    if (!retTypeChecked.length) {
                        retSiteDiv.hide();
                        retDetailsDiv.hide();
                        retCarrierDiv.hide();
                        retNumberDiv.hide();
                        retTimeDiv.hide();
                        retInternationalDiv.hide();
                    } else {
                        var retMXTransportation = retMXTransportationChecked.val();
                        var retType = retTypeChecked.val();
                        if(retMXTransportation === '1') {
                            lib.updateRadio($('.mx-form div[data-groupname="ret_site"] input'), data.return.sites);
                            var unlocalized = 'vacation_travel_form_return_ret_site' + (
                                retType === 'Plane' || retType === 'Train' ? '_' + retType : ''
                            );
                            $.when(str.get_string(unlocalized, 'local_mxschool')).done(function(text) {
                                retSiteDiv.children().eq(0).text(text);
                                retSiteDiv.show();
                            });
                        } else {
                            retSiteDiv.hide();
                        }
                        var retSite = $('.mx-form div[data-groupname="ret_site"] input:checked').val();
                        if (retType === 'Car' || retType === 'Non-MX Bus' || (retMXTransportation === '1' && retSite === '0')) {
                            var unlocalized = 'vacation_travel_form_return_ret_details' + (retType === 'Car' ? '_' + retType : '');
                            $.when(str.get_string(unlocalized, 'local_mxschool')).done(function(text) {
                                retDetailsDiv.children().eq(0).text(text);
                                retDetailsDiv.show();
                            });
                        } else {
                            retDetailsDiv.hide();
                        }
                        if (retType === 'Plane' || retType === 'Train' || retType === 'Bus') {
                            $.when(str.get_string('vacation_travel_form_return_ret_carrier_' + retType, 'local_mxschool'))
                            .done(function(text) {
                                retCarrierDiv.children().eq(0).text(text);
                                retCarrierDiv.show();
                            });
                            $.when(str.get_string('vacation_travel_form_return_ret_number_' + retType, 'local_mxschool'))
                            .done(function(text) {
                                retNumberDiv.children().eq(0).text(text);
                                retNumberDiv.show();
                            });
                        } else {
                            retCarrierDiv.hide();
                            retNumberDiv.hide();
                        }
                        var suffix = retMXTransportation === '1' && (
                            retType === 'Plane' || retType === 'Train' || retType === 'Bus'
                        ) ? '_' + retType : '';
                        $.when(str.get_string('vacation_travel_form_return_ret_variable' + suffix, 'local_mxschool'))
                        .done(function(text) {
                            retTimeDiv.children().eq(0).text(text);
                            var retHourSelect = $('.mx-form select#id_ret_variable_time_hour');
                            var retMinuteSelect = $('.mx-form select#id_ret_variable_time_minute');
                            var retAMPMSelect = $('.mx-form select#id_ret_variable_time_ampm');
                            var retDaySelect = $('.mx-form select#id_ret_variable_date_day');
                            var retMonthSelect = $('.mx-form select#id_ret_variable_date_month');
                            var retYearSelect = $('.mx-form select#id_ret_variable_date_year');
                            var retCalendar = $('.mx-form a#id_ret_variable_date_calendar');
                            if (data.return.default.year) {
                                retHourSelect.val(data.return.default.hour);
                                retMinuteSelect.val(data.return.default.minute);
                                retAMPMSelect.val(data.return.default.ampm ? 1 : 0);
                                retDaySelect.val(data.return.default.day);
                                retMonthSelect.val(data.return.default.month);
                                retYearSelect.val(data.return.default.year);
                                retHourSelect.prop('disabled', true);
                                retMinuteSelect.prop('disabled', true);
                                retAMPMSelect.prop('disabled', true);
                                retDaySelect.prop('disabled', true);
                                retMonthSelect.prop('disabled', true);
                                retYearSelect.prop('disabled', true);
                                retCalendar.hide();
                                dayOfWeek();
                            } else {
                                retHourSelect.prop('disabled', false);
                                retMinuteSelect.prop('disabled', false);
                                retAMPMSelect.prop('disabled', false);
                                retDaySelect.prop('disabled', false);
                                retMonthSelect.prop('disabled', false);
                                retYearSelect.prop('disabled', false);
                                retCalendar.show();
                            }
                            retTimeDiv.show();
                        });
                        if (retType === 'Plane' && retMXTransportation === '1') {
                            retInternationalDiv.show();
                        } else {
                            retInternationalDiv.hide();
                        }
                    }
                }
            }).fail(notification.exception);
        }
        function dayOfWeek() {
            var depYearSelect = $('.mx-form select#id_dep_variable_date_year');
            var depMonthSelect = $('.mx-form select#id_dep_variable_date_month');
            var depDaySelect = $('.mx-form select#id_dep_variable_date_day');
            var depDate = new Date(depYearSelect.val(), depMonthSelect.val() - 1, depDaySelect.val());
            var depDayOfWeek = depDate.toLocaleDateString('en-US', {weekday: 'long'});
            var depDateDiv = $('.mx-form div[data-groupname="dep_variable_date"]');
            if ($(depDateDiv[0].previousSibling).text().trim() !== '') {
                depDateDiv[0].previousSibling.remove();
            }
            depDateDiv.before('&nbsp;' + depDayOfWeek + '&nbsp;');

            var retYearSelect = $('.mx-form select#id_ret_variable_date_year');
            var retMonthSelect = $('.mx-form select#id_ret_variable_date_month');
            var retDaySelect = $('.mx-form select#id_ret_variable_date_day');
            var retDate = new Date(retYearSelect.val(), retMonthSelect.val() - 1, retDaySelect.val());
            var retDayOfWeek = retDate.toLocaleDateString('en-US', {weekday: 'long'});
            var retDateDiv = $('.mx-form div[data-groupname="ret_variable_date"]');
            if ($(retDateDiv[0].previousSibling).text().trim() !== '') {
                retDateDiv[0].previousSibling.remove();
            }
            retDateDiv.before('&nbsp;' + retDayOfWeek + '&nbsp;');
        }
        return function() {
            $(document).ready(function() {
                update();
                dayOfWeek();
            });
            $('div[data-groupname="dep_mxtransportation"]').change(update);
            $('div[data-groupname="dep_type"]').change(update);
            $('div[data-groupname="dep_site"]').change(update);
            $('div[data-groupname="dep_variable_date"]').change(dayOfWeek);
            $('a#id_dep_variable_date_calendar').on('click', function(){
                setTimeout(function(){
                    $('div#dateselector-calendar-panel td').on('click', function(){
                        setTimeout(dayOfWeek, 100);
                    });
                }, 100);
            });
            if ($('.mx-form fieldset#id_return').length) {
                $('div[data-groupname="ret_mxtransportation"]').change(update);
                $('div[data-groupname="ret_type"]').change(update);
                $('div[data-groupname="ret_site"]').change(update);
                $('div[data-groupname="ret_variable_date"]').change(dayOfWeek);
                $('a#id_ret_variable_date_calendar').on('click', function(){
                    setTimeout(function(){
                        $('div#dateselector-calendar-panel td').on('click', function(){
                            setTimeout(dayOfWeek, 100);
                        });
                    }, 100);
                });
            }
        };
    }
);
