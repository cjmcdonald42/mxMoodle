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
 * Updates a select element with courses from a specified department for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_department_courses
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    return function() {
        $('.mx-form select#id_department').change(function() {
            var promises = ajax.call([{
                methodname: 'local_peertutoring_get_department_courses',
                args: {
                    department: $('.mx-form select#id_department').val()
                }
            }]);
            promises[0].done(function(data) {
                var courseSelect = $('.mx-form select#id_course');
                var courseSelected = courseSelect.val();
                courseSelect.empty();
                $.each(data, function(index, course) {
                    courseSelect.append($('<option></option>').attr('value', course.id).text(course.name));
                });
                if ($('.mx-form select#id_course > option[value=' + courseSelected + ']').length) {
                    courseSelect.val(courseSelected);
                } else {
                    courseSelect.change();
                }
            }).fail(notification.exception);
        });
    };
});
