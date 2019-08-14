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
 * Confirms an on-campus signout record for Middlesex's eSignout Subplugin.
 *
 * @module      local_signout/confirmation_button
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
            if (element.data('state') === 'confirm') {
                set_state(element, 'undo');
                setTimeout(function() {
                    if (element.data('state') === 'undo') {
                        element.hide('slow', function() {
                            element.parent().html('&#x2705;');
                        });
                        $.when(
                            str.get_string('duty_report:cell:confirmation_text', 'local_signout', data)
                        ).done(function(text) {
                            element.parent().parent().find('td.confirmation').text(text);
                        });
                    }
                }, data.undowindow * 1000);
            } else {
                set_state(element, 'confirm');
            }
        }).fail(notification.exception);
    }
    function set_state(element, state) {
        element.data('state', state);
        $.when(str.get_string('confirmation_button:' + state, 'local_signout')).done(function(text) {
            element.text(text);
            element.addClass(state === 'confirm' ? 'btn-primary' : 'btn-secondary');
            element.removeClass(state === 'confirm' ? 'btn-secondary' : 'btn-primary');
        });
    }
    return function(value) {
        var element = $('.mx-confirmation-button[value="' + value + '"]');
        element.data('state', 'confirm');
        element.click(confirm_signout);
    };
});
