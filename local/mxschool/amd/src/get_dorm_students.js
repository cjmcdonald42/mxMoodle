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
 * Updates a select element with students from a specified dorm for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_dorm_students
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    function update() {
        var promises = ajax.call([{
            methodname: 'local_mxschool_get_dorm_students',
            args: {
                dorm: $('.mx-form select#id_dorm').val()
            }
        }]);
        promises[0].done(function(data) {
            var studentSelect = $('.mx-form select#id_student');
            var studentSelected = studentSelect.val();
            studentSelect.empty();
            $.each(data, function(index, student) {
                studentSelect.append($('<option></option>').attr('value', student.userid).text(student.name));
            });
            if ($('.mx-form select#id_student > option[value=' + studentSelected + ']').length) {
                studentSelect.val(studentSelected);
            } else {
                studentSelect.change();
            }
        }).fail(notification.exception);
    }
    return function() {
        $(document).ready(update);
        $('.mx-form select#id_dorm').change(update);
    };
});
