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
 * Sets a boolean field in the database for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/get_dorm_students
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/ajax', 'core/notification'], function($, str, ajax, notification) {
    return  {
        update_field: function(table, name, value) {
            var element = $('.mx-checkbox[name="' + name + '"][value="' + value + '"]');
            element.change(function() {
                var promises = ajax.call([{
                    methodname: 'local_mxschool_set_boolean_field',
                    args: {
                        table: table,
                        field: name,
                        id: value,
                        value: element.prop("checked")
                    }
                }]);
                promises[0].done(function() {
                    var saved = $('<span></span>').text(' saved').attr('class', 'green');
                    $(element).after(saved);
                    setTimeout(function() {
                        saved.fadeOut('slow', function() {
                            saved.remove();
                            var button = element.parent().children('button');
                            if(button) {
                                if(element.prop('checked')) {
                                    $.when(str.get_string('email_button_default', 'local_mxschool')).done(function(defaultString) {
                                        button.text(defaultString);
                                        button.show('slow');
                                    });
                                } else {
                                    button.hide('slow', function() {
                                        button.text('');
                                    });
                                }
                            }
                        });
                    }, 1000);
                }).fail(notification.exception);
            });
        }
    };
});
