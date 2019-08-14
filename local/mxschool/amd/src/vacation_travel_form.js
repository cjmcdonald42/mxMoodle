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
 * Updates the options of the vacation travel form for Middlesex's Dorm and Student Functions Plugin.
 *
 * @module      local_mxschool/vacation_travel_form
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
                    },
                    return: {
                        mxtransportation: returnEnabled && retMXTransportationChecked.length ? retMXTransportationChecked.val() == 1
                            : undefined,
                        type: returnEnabled && retTypeChecked.length ? retTypeChecked.val() : undefined,
                        site: retSiteChecked.length ? retSiteChecked.val() : undefined
                    }
                }
            }]);
            promises[0].done(function(data) {
                lib.updateSelect($('.mx-form select#id_student'), data.students, $('.mx-form input[name="id"]').val() !== '0');
                updateSection('departure', data.departure);
                if (returnEnabled) {
                    updateSection('return', data.return);
                }
            }).fail(notification.exception);
        }
        function updateSection(section, data) {
            var abbreviation = section.substring(0, 3);
            var stringPrefix = 'vacation_travel:form:' + section + ':' + abbreviation + '_';
            var mxTransportationChecked = $('.mx-form div[data-groupname="' + abbreviation + '_mxtransportation"] input:checked');
            var typeChecked = $('.mx-form div[data-groupname="' + abbreviation + '_type"] input:checked');
            if (!mxTransportationChecked.length) {
                $('.mx-form div[data-groupname="' + abbreviation + '_type"]').hide();
            } else {
                lib.updateRadio($('.mx-form div[data-groupname="' + abbreviation + '_type"] input'), data.types);
                $('.mx-form div[data-groupname="' + abbreviation + '_type"]').show();
            }
            var siteDiv = $('.mx-form div[data-groupname="' + abbreviation + '_site"]');
            var detailsDiv = $('.mx-form input#id_' + abbreviation + '_details').parent().parent();
            var carrierDiv = $('.mx-form input#id_' + abbreviation + '_carrier').parent().parent();
            var numberDiv = $('.mx-form input#id_' + abbreviation + '_number').parent().parent();
            var timeDiv = $('.mx-form div[data-groupname="' + abbreviation + '_variable"]');
            var internationalDiv = $('.mx-form div[data-groupname="' + abbreviation + '_international"]');
            if (!typeChecked.length) {
                siteDiv.hide();
                detailsDiv.hide();
                carrierDiv.hide();
                numberDiv.hide();
                timeDiv.hide();
                internationalDiv.hide();
            } else {
                var mxTransportation = mxTransportationChecked.val();
                var type = typeChecked.val();
                if(mxTransportation === '1') {
                    lib.updateRadio($('.mx-form div[data-groupname="' + abbreviation + '_site"] input'), data.sites);
                    var suffix = type === 'Plane' || type === 'Train' ? ':' + type : '';
                    $.when(str.get_string(stringPrefix + 'site' + suffix, 'local_mxschool')).done(function(text) {
                        siteDiv.children().eq(0).text(text);
                        siteDiv.show();
                    });
                } else {
                    siteDiv.hide();
                }
                var site = $('.mx-form div[data-groupname="' + abbreviation + '_site"] input:checked').val();
                if (type === 'Car' || type === 'Non-MX Bus' || (mxTransportation === '1' && site === '0')) {
                    var suffix = type === 'Car' ? ':' + type : '';
                    $.when(str.get_string(stringPrefix + 'details' + suffix, 'local_mxschool')).done(function(text) {
                        detailsDiv.children().eq(0).text(text);
                        detailsDiv.show();
                    });
                } else {
                    detailsDiv.hide();
                }
                if (type === 'Plane' || type === 'Train' || type === 'Bus') {
                    $.when(str.get_string(stringPrefix + 'carrier:' + type, 'local_mxschool'))
                    .done(function(text) {
                        carrierDiv.children().eq(0).text(text);
                        carrierDiv.show();
                    });
                    $.when(str.get_string(stringPrefix + 'number:' + type, 'local_mxschool'))
                    .done(function(text) {
                        numberDiv.children().eq(0).text(text);
                        numberDiv.show();
                    });
                } else {
                    carrierDiv.hide();
                    numberDiv.hide();
                }
                var suffix = mxTransportation === '1' && (type === 'Plane' || type === 'Train' || type === 'Bus') ? ':' + type : '';
                $.when(str.get_string(stringPrefix + 'variable' + suffix, 'local_mxschool')).done(function(text) {
                    timeDiv.children().eq(0).text(text);
                    var hourSelect = $('.mx-form select#id_' + abbreviation + '_variable_time_hour');
                    var minuteSelect = $('.mx-form select#id_' + abbreviation + '_variable_time_minute');
                    var ampmSelect = $('.mx-form select#id_' + abbreviation + '_variable_time_ampm');
                    var daySelect = $('.mx-form select#id_' + abbreviation + '_variable_date_day');
                    var monthSelect = $('.mx-form select#id_' + abbreviation + '_variable_date_month');
                    var yearSelect = $('.mx-form select#id_' + abbreviation + '_variable_date_year');
                    var calendar = $('.mx-form a#id_' + abbreviation + '_variable_date_calendar');
                    if (data.default.year) {
                        hourSelect.val(data.default.hour);
                        minuteSelect.val(data.default.minute);
                        ampmSelect.val(data.default.ampm === 'PM' ? 1 : 0);
                        daySelect.val(data.default.day);
                        monthSelect.val(data.default.month);
                        yearSelect.val(data.default.year);
                        hourSelect.prop('disabled', true);
                        minuteSelect.prop('disabled', true);
                        ampmSelect.prop('disabled', true);
                        daySelect.prop('disabled', true);
                        monthSelect.prop('disabled', true);
                        yearSelect.prop('disabled', true);
                        calendar.hide();
                        setDayOfWeek(abbreviation);
                    } else {
                        hourSelect.prop('disabled', false);
                        minuteSelect.prop('disabled', false);
                        ampmSelect.prop('disabled', false);
                        daySelect.prop('disabled', false);
                        monthSelect.prop('disabled', false);
                        yearSelect.prop('disabled', false);
                        calendar.show();
                    }
                    timeDiv.show();
                });
                if (type === 'Plane' && mxTransportation === '1') {
                    internationalDiv.show();
                } else {
                    internationalDiv.hide();
                }
            }
        }
        function setDayOfWeek(abbreviation) {
            var yearSelect = $('.mx-form select#id_' + abbreviation + '_variable_date_year');
            var monthSelect = $('.mx-form select#id_' + abbreviation + '_variable_date_month');
            var daySelect = $('.mx-form select#id_' + abbreviation + '_variable_date_day');
            var date = new Date(yearSelect.val(), monthSelect.val() - 1, daySelect.val());
            var dayOfWeek = date.toLocaleDateString('en-US', {weekday: 'long'});
            var dateDiv = $('.mx-form div[data-groupname="' + abbreviation + '_variable_date"]');
            if ($(dateDiv[0].previousSibling).text().trim() !== '') {
                dateDiv[0].previousSibling.remove();
            }
            dateDiv.before('&nbsp;' + dayOfWeek + '&nbsp;');
        }
        return {
            setup: function() {
                $(document).ready(function() {
                    update();
                    setDayOfWeek('dep');
                    if ($('.mx-form fieldset#id_return').length) {
                        setDayOfWeek('ret');
                    }
                });
                $('div[data-groupname="dep_mxtransportation"]').change(update);
                $('div[data-groupname="dep_type"]').change(update);
                $('div[data-groupname="dep_site"]').change(update);
                $('div[data-groupname="dep_variable_date"]').change(function() {
                    setDayOfWeek('dep');
                });
                $('a#id_dep_variable_date_calendar').on('click', function() {
                    setTimeout(function() {
                        $('div#dateselector-calendar-panel td').on('click', function() {
                            setTimeout(function() {
                                setDayOfWeek('dep');
                            }, 100);
                        });
                    }, 100);
                });
                if ($('.mx-form fieldset#id_return').length) {
                    $('div[data-groupname="ret_mxtransportation"]').change(update);
                    $('div[data-groupname="ret_type"]').change(update);
                    $('div[data-groupname="ret_site"]').change(update);
                    $('div[data-groupname="ret_variable_date"]').change(function() {
                        setDayOfWeek('ret');
                    });
                    $('a#id_ret_variable_date_calendar').on('click', function() {
                        setTimeout(function() {
                            $('div#dateselector-calendar-panel td').on('click', function() {
                                setTimeout(function() {
                                    setDayOfWeek('ret');
                                }, 100);
                            });
                        }, 100);
                    });
                }
            }
        };
    }
);
