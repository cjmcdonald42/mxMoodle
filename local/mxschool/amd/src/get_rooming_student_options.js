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
 * Updates the options of the rooming form for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_rooming_student_options
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    function update() {
        var promises = ajax.call([{
            methodname: 'local_mxschool_get_rooming_student_options',
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

            $('.mx-form fieldset#id_info div.form-control-static').text(data.dorm);

            $.when(str.get_string('form_select_default', 'local_mxschool')).done(function(text) {
                data.roomtypes.unshift({
                    internalname: 0,
                    localizedname: text
                });
                var roomtypeSelect = $('.mx-form select#id_roomtype');
                var roomtypeSelected = roomtypeSelect.val();
                roomtypeSelect.empty();
                $.each(data.roomtypes, function(index, option) {
                    roomtypeSelect.append($('<option></option>').attr('value', option.internalname).text(option.localizedname));
                });
                if ($('.mx-form select#id_roomtype > option[value=' + roomtypeSelected + ']').length) {
                    roomtypeSelect.val(roomtypeSelected);
                } else {
                    roomtypeSelect.change();
                }
                var dormmates = [{
                    userid: 0,
                    name: text
                }];
                data.gradedormmates.unshift(dormmates[0]);
                data.dormmates.unshift(dormmates[0]);
                var show = true;
                function append(index, student) {
                    select.append($('<option></option>').attr('value', student.userid).text(student.name));
                }
                function findSelected(student) {
                    return student.userid == selected;
                }
                for (var i = 1; i <= 6; i++) {
                    var select = $('.mx-form select#id_dormmate' + i);
                    var selected = select.val();
                    select.empty();
                    $.each(i <= 3 ? data.gradedormmates : data.dormmates, append);
                    if ($('.mx-form select#id_dormmate' + i + ' > option[value=' + selected + ']').length) {
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
                        if (data.gradedormmates.findIndex(findSelected) >= 0) {
                            dormmates.push(data.gradedormmates.splice(data.gradedormmates.findIndex(findSelected), 1)[0]);
                        }
                        data.dormmates.splice(data.dormmates.findIndex(findSelected), 1);
                    }
                }
                var select = $('.mx-form select#id_roommate');
                var selected = select.val();
                var selectDiv = select.parent().parent();
                select.empty();
                $.each(dormmates, append);
                if ($('.mx-form select#id_roommate > option[value=' + selected + ']').length) {
                    select.val(selected);
                } else {
                    select.change();
                }
                if (show) {
                    selectDiv.prev().show();
                    selectDiv.show();
                } else {
                    selectDiv.prev().hide();
                    selectDiv.hide();
                }
            });
        }).fail(notification.exception);
    }
    return function() {
        $(document).ready(update);
        $('.mx-form select#id_student').change(update);
        $('.mx-form select#id_dormmate1').change(update);
        $('.mx-form select#id_dormmate2').change(update);
        $('.mx-form select#id_dormmate3').change(update);
        $('.mx-form select#id_dormmate4').change(update);
        $('.mx-form select#id_dormmate5').change(update);
        $('.mx-form select#id_dormmate6').change(update);
    };
});
