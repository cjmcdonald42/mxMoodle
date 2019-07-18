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
 * Sets a boolean field in the database for Middlesex's Dorm and Student Functions Plugin.
 *
 * @module     local_mxschool/set_boolean_field
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    function update(event) {
        var element = $(event.target);
        var promises = ajax.call([{
            methodname: 'local_mxschool_set_boolean_field',
            args: {
                table: element.attr('name').substring(0, element.attr('name').indexOf(':')),
                field: element.attr('name').substring(element.attr('name').indexOf(':') + 1),
                id: element.val(),
                value: element.prop("checked")
            }
        }]);
        promises[0].done(function() {
            var saved = element.next();
            saved.show();
            setTimeout(function() {
                saved.hide('slow', function() {
                    element.trigger(element.prop('checked') ? 'checkboxEnabled' : 'checkboxDisabled');
                });
            }, 1000);
        }).fail(notification.exception);
    }
    return function(name, value) {
        var element = $('.mx-checkbox[value="' + value + '"][name="' + name + '"]');
        element.change(update);
    };
});
