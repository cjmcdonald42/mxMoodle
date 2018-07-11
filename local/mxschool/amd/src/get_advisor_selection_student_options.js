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
 * Updates the options of the advisor selection form for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_advisor_selection_student_options
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    function update() {
        var keepcurrent0 = $('.mx-form input#id_keepcurrent_0');
        var optionsFieldset = $('.mx-form fieldset#id_options');
        var deansFieldset = $('.mx-form fieldset#id_deans');
        if (keepcurrent0.prop('checked')) {
            optionsFieldset.show();
        } else {
            optionsFieldset.hide();
        }
        if ($('.mx-form input[name="isstudent"]').val() === '0') {
            deansFieldset.show();
        } else {
            deansFieldset.hide();
        }
        var promises = ajax.call([{
            methodname: 'local_mxschool_get_advisor_selection_student_options',
            args: {
                userid: $('.mx-form select#id_student').val()
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

            $('.mx-form fieldset#id_info div.form-control-static').eq(0).text(data.current.name);

            var keepcurrentDiv = $('.mx-form div[data-groupname="keepcurrent"]');
            var warningDiv = $('.mx-form fieldset#id_info div.form-control-static').eq(1).parent().parent();
            if (data.closing) {
                keepcurrentDiv.hide();
                if (!keepcurrent0.prop('checked')) {
                    keepcurrent0.prop('checked', true);
                    keepcurrentDiv.change();
                }
                warningDiv.show();
            } else {
                warningDiv.hide();
                keepcurrentDiv.show();
            }

            $.when(str.get_string('advisor_form_faculty_default', 'local_mxschool')).done(function(text) {
                data.available.splice(data.available.findIndex(function(advisor) {
                    return advisor.userid === data.current.userid;
                }), 1);
                data.available.unshift({
                    userid: 0,
                    name: text
                });
                var show = true;
                function appendAdvisor(index, advisor) {
                    select.append($('<option></option>').attr('value', advisor.userid).text(advisor.name));
                }
                function findSelected(advisor) {
                    return advisor.userid == selected;
                }
                for (var i = 1; i <= 5; i++) {
                    var select = $('.mx-form select#id_option' + i);
                    var selected = select.val();
                    select.empty();
                    $.each(data.available, appendAdvisor);
                    if ($('.mx-form select#id_option' + i + '> option[value=' + selected + ']').length) {
                        select.val(selected);
                    } else {
                        select.change();
                    }
                    var selectDiv = select.parent().parent();
                    if (show) {
                        selectDiv.show();
                    } else {
                        selectDiv.hide();
                    }
                    if (selected == 0) {
                        show = false;
                    } else {
                        data.available.splice(data.available.findIndex(findSelected), 1);
                        if (selected == data.current.userid) {
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
    return function() {
        $(document).ready(update);
        $('.mx-form select#id_student').change(update);
        $('.mx-form div[data-groupname="keepcurrent"]').change(update);
        $('.mx-form select#id_option1').change(update);
        $('.mx-form select#id_option2').change(update);
        $('.mx-form select#id_option3').change(update);
        $('.mx-form select#id_option4').change(update);
        $('.mx-form select#id_option5').change(update);
    };
});
