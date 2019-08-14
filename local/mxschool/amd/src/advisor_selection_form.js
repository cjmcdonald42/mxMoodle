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
 * Updates the options of the advisor selection form for Middlesex's Dorm and Student Functions Plugin.
 *
 * @module     local_mxschool/advisor_selection_form
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    ['jquery', 'core/ajax', 'core/str', 'core/notification', 'local_mxschool/locallib'], function($, ajax, str, notification, lib) {
        function addClasses() {
            $('.mx-form div#fitem_id_warning').children().eq(1).children().eq(0).addClass('text-warning');
            $('.mx-form div#fitem_id_instructions').children().eq(1).children().eq(0).addClass('text-info');
        }
        function update() {
            var keepCurrent0 = $('.mx-form input#id_keep_current_0');
            var optionsFieldset = $('.mx-form fieldset#id_options');
            if (keepCurrent0.prop('checked')) {
                optionsFieldset.show();
            } else {
                optionsFieldset.hide();
            }
            var promises = ajax.call([{
                methodname: 'local_mxschool_get_advisor_selection_student_options',
                args: {
                    userid: $('.mx-form select#id_student').val()
                }
            }]);
            promises[0].done(function(data) {
                lib.updateSelect($('.mx-form select#id_student'), data.students, $('.mx-form input[name="id"]').val() !== '0');
                $('.mx-form fieldset#id_info div.form-control-static').eq(0).text(data.current.text);
                var keepCurrentDiv = $('.mx-form div[data-groupname="keep_current"]');
                var keepCurrent0 = $('.mx-form input#id_keep_current_0');
                var warningDiv = $('.mx-form fieldset#id_info div.form-control-static').eq(1).parent().parent();
                if (data.closing) {
                    keepCurrentDiv.hide();
                    if (!keepCurrent0.prop('checked')) {
                        keepCurrent0.prop('checked', true);
                        keepCurrentDiv.change();
                    }
                    warningDiv.show();
                } else {
                    warningDiv.hide();
                    keepCurrentDiv.show();
                }
                $.when(str.get_string('form:select:default', 'local_mxschool')).done(function(text) {
                    var currentIndex = data.available.findIndex(function(faculty) {
                        return faculty.value === data.current.value;
                    });
                    if (currentIndex !== -1) {
                        data.available.splice(currentIndex, 1);
                    }
                    data.available.unshift({
                        value: 0,
                        text: text
                    });
                    var show = true;
                    function findSelected(faculty) {
                        return faculty.value == selected;
                    }
                    for (var i = 1; i <= 5; i++) {
                        var select = $('.mx-form select#id_option' + i);
                        lib.updateSelect(select, data.available);
                        var selectDiv = select.parent().parent();
                        if (show) {
                            selectDiv.show();
                        } else {
                            selectDiv.hide();
                        }
                        var selected = select.val();
                        if (selected == 0) {
                            show = false;
                        } else {
                            data.available.splice(data.available.findIndex(findSelected), 1);
                            if (selected == data.current.value) {
                                show = false;
                            }
                        }
                        if (i === 1 && !data.closing) {
                            data.available.splice(1, 0, data.current);
                        }
                    }
                });
            }).fail(notification.exception);
        }
        return {
            setup: function() {
                $(document).ready(function() {
                    addClasses();
                    var deansFieldset = $('.mx-form fieldset#id_deans');
                    if ($('.mx-form input[name="isstudent"]').val() === '0') {
                        deansFieldset.show();
                    } else {
                        deansFieldset.hide();
                    }
                    update();
                });
                $('.mx-form select#id_student').change(update);
                $('.mx-form div[data-groupname="keep_current"]').change(update);
                $('.mx-form select#id_option1').change(update);
                $('.mx-form select#id_option2').change(update);
                $('.mx-form select#id_option3').change(update);
                $('.mx-form select#id_option4').change(update);
                $('.mx-form select#id_option5').change(update);
            }
        };
    }
);
