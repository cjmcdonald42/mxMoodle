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
 * Confirms an on-campus signout record for Middlesex School's eSignout Subplugin.
 *
 * @module     local_signout/confirmation_button
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, ajax, str, notification) {
    function confirm_signout(event) {
        var element = $(event.target);
        var promises = ajax.call([{
            methodname: 'local_signout_confirm_signout',
            args: {
                id: element.val()
            }
        }]);
        promises[0].done(function(data) {
            element.hide('slow', function() {
                element.parent().html('&#x2705;');
            });
            $.when(
                str.get_string('duty_report_column_confirmation_text', 'local_signout', data)
            ).done(function(text) {
                element.parent().parent().find('td.confirmation').text(text);
            });
        }).fail(notification.exception);
    }
    return function(value) {
        var element = $('.mx-confirmation-button[value="' + value + '"]');
        element.click(confirm_signout);
    };
});
