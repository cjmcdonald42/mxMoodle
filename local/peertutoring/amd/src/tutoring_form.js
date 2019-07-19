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
 * Updates the options of the tutoring form for Middlesex's Peer Tutoring Subplugin.
 *
 * @module     local_peertutoring/tutoring_form
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'local_mxschool/locallib'], function($, ajax, notification, lib) {
    function updateTutorOptions() {
        var promises = ajax.call([{
            methodname: 'local_peertutoring_get_tutor_options',
            args: {
                userid: $('.mx-form select#id_tutor').val()
            }
        }]);
        promises[0].done(function(data) {
            lib.updateSelect($('.mx-form select#id_student'), data.students);
            lib.updateSelect($('.mx-form select#id_department'), data.departments);
        }).fail(notification.exception);
    }
    function updateCourses() {
        var promises = ajax.call([{
            methodname: 'local_peertutoring_get_department_courses',
            args: {
                department: $('.mx-form select#id_department').val()
            }
        }]);
        promises[0].done(function(data) {
            lib.updateSelect($('.mx-form select#id_course'), data);
        }).fail(notification.exception);
    }
    return function() {
        $(document).ready(function() {
            updateTutorOptions();
            updateCourses();
        });
        $('.mx-form select#id_tutor').change(updateTutorOptions);
        $('.mx-form select#id_department').change(updateCourses);
    };
});
