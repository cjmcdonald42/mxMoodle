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
 * Updates the options of the vacation travel form for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_vacation_travel_options
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    function update() {
        var depMXTransportationChecked = $('.mx-form div[data-groupname="dep_mxtransportation"] input:checked');
        var depTypeChecked = $('.mx-form div[data-groupname="dep_type"] input:checked');
        var retMXTransportationChecked = $('.mx-form div[data-groupname="ret_mxtransportation"] input:checked');
        var retTypeChecked = $('.mx-form div[data-groupname="ret_type"] input:checked');
        var promises = ajax.call([{
            methodname: 'local_mxschool_get_vacation_travel_options',
            args: {
                departure: {
                    mxtransportation: depMXTransportationChecked.length ? depMXTransportationChecked.val() == 1 : undefined,
                    type: depTypeChecked.length ? depTypeChecked.val() : undefined
                }, return: {
                    mxtransportation: retMXTransportationChecked.length ? retMXTransportationChecked.val() == 1 : undefined,
                    type: retTypeChecked.length ? retTypeChecked.val() : undefined
                }
            }
        }]);
        promises[0].done(function(data) {
            var studentSelect = $('.mx-form select#id_student');
            var studentSelected = studentSelect.val();
            var studentSelectedName = $('.mx-form select#id_student > option[value=' + studentSelected + ']').text();
            studentSelect.empty();
            $.each(data.students, function(index, student) {
                studentSelect.append($('<option></option>').attr('value', student.userid).text(student.name));
            });
            if ($('.mx-form input[name="id"]').val() !== '0') {
                if (!$('.mx-form select#id_student > option[value=' + studentSelected + ']').length) {
                    studentSelect.prepend($('<option></option>').attr('value', studentSelected).text(studentSelectedName));
                }
            }
            if ($('.mx-form select#id_student > option[value=' + studentSelected + ']').length) {
                studentSelect.val(studentSelected);
            } else {
                studentSelect.change();
            }

            if (!depMXTransportationChecked.length) {
                $('.mx-form div[data-groupname="dep_type"]').hide();
            } else {
                var types = $('.mx-form div[data-groupname="dep_type"] input');
                types.each(function() {
                    var typeInput = $(this);
                    var spanContents = typeInput.parent().parent().contents();
                    if (data.departure.types.includes(typeInput.val())) {
                        typeInput.parent().show();
                        spanContents.eq(spanContents.index(typeInput.parent()) + 3).get(0).nodeValue = '\u2003';
                    } else {
                        if (typeInput.prop('checked')) {
                            typeInput.prop('checked', false);
                            typeInput.change();
                        }
                        typeInput.parent().hide();
                        spanContents.eq(spanContents.index(typeInput.parent()) + 3).get(0).nodeValue = '';
                    }
                });
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
                    var sites = $('.mx-form div[data-groupname="dep_site"] input');
                    sites.each(function() {
                        var siteInput = $(this);
                        var spanContents = siteInput.parent().parent().contents();
                        if (data.departure.sites.includes(siteInput.val())) {
                            siteInput.parent().show();
                            spanContents.eq(spanContents.index(siteInput.parent()) + 3).get(0).nodeValue = '\u2003';
                        } else {
                            if (siteInput.prop('checked')) {
                                siteInput.prop('checked', false);
                                siteInput.change();
                            }
                            siteInput.parent().hide();
                            spanContents.eq(spanContents.index(siteInput.parent()) + 3).get(0).nodeValue = '';
                        }
                    });
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
                var suffix = depMXTransportation === '1' && (depType === 'Plane' || depType === 'Train' || depType === 'Bus')
                    ? '_' + depType : '';
                $.when(str.get_string('vacation_travel_form_departure_dep_variable' + suffix, 'local_mxschool'))
                .done(function(text) {
                    depTimeDiv.children().eq(0).text(text);
                    depTimeDiv.show();
                });
                if (depType === 'Plane' && depMXTransportation === '1') {
                    depInternationalDiv.show();
                } else {
                    depInternationalDiv.hide();
                }
            }

            if (!retMXTransportationChecked.length) {
                $('.mx-form div[data-groupname="ret_type"]').hide();
            } else {
                var types = $('.mx-form div[data-groupname="ret_type"] input');
                types.each(function() {
                    var typeInput = $(this);
                    var spanContents = typeInput.parent().parent().contents();
                    if (data.return.types.includes(typeInput.val())) {
                        typeInput.parent().show();
                        spanContents.eq(spanContents.index(typeInput.parent()) + 3).get(0).nodeValue = '\u2003';
                    } else {
                        if (typeInput.prop('checked')) {
                            typeInput.prop('checked', false);
                            typeInput.change();
                        }
                        typeInput.parent().hide();
                        spanContents.eq(spanContents.index(typeInput.parent()) + 3).get(0).nodeValue = '';
                    }
                });
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
                    var sites = $('.mx-form div[data-groupname="ret_site"] input');
                    sites.each(function() {
                        var siteInput = $(this);
                        var spanContents = siteInput.parent().parent().contents();
                        if (data.return.sites.includes(siteInput.val())) {
                            siteInput.parent().show();
                            spanContents.eq(spanContents.index(siteInput.parent()) + 3).get(0).nodeValue = '\u2003';
                        } else {
                            if (siteInput.prop('checked')) {
                                siteInput.prop('checked', false);
                                siteInput.change();
                            }
                            siteInput.parent().hide();
                            spanContents.eq(spanContents.index(siteInput.parent()) + 3).get(0).nodeValue = '';
                        }
                    });
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
                var suffix = retMXTransportation === '1' && (retType === 'Plane' || retType === 'Train' || retType === 'Bus')
                    ? '_' + retType : '';
                $.when(str.get_string('vacation_travel_form_return_ret_variable' + suffix, 'local_mxschool'))
                .done(function(text) {
                    retTimeDiv.children().eq(0).text(text);
                    retTimeDiv.show();
                });
                if (retType === 'Plane' && retMXTransportation === '1') {
                    retInternationalDiv.show();
                } else {
                    retInternationalDiv.hide();
                }
            }
        }).fail(notification.exception);
    }
    return function() {
        $(document).ready(update);
        $('div[data-groupname="dep_mxtransportation"]').change(update);
        $('div[data-groupname="dep_type"]').change(update);
        $('div[data-groupname="dep_site"]').change(update);
        $('div[data-groupname="ret_mxtransportation"]').change(update);
        $('div[data-groupname="ret_type"]').change(update);
        $('div[data-groupname="ret_site"]').change(update);
    };
});
