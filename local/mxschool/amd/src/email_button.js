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
 * Sends an email for Middlesex School's Dorm and Student functions plugin.
 *
 * @module     local_mxschool/email_button
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/ajax', 'core/notification'], function($, str, ajax, notification) {
    function sendEmail() {
        var element = $(this);
        var promises = ajax.call([{
            methodname: 'local_mxschool_send_email',
            args: {
                emailclass: element.attr('name'),
                emailparams: {
                    id: element.val()
                }
            }
        }]);
        promises[0].done(function() {
            $.when(str.get_string('email_button_sent', 'local_mxschool')).done(function(sentString) {
                element.text(sentString);
                setTimeout(function() {
                    element.trigger('hideButton');
                }, 1000);
            });
        }).fail(notification.exception);
    }
    function showButton() {
        $(this).show('slow');
        $(this).before($('<span></span>').text('\u2003'));
    }
    function hideButton() {
        $(this).hide('slow');
        $(this).parent().children('span:contains("\u2003")').remove();
    }
    return function(value, name, hidden) {
        var element = $('button.mx-email-button[value="' + value + '"][name="' + name + '"]');
        element.click(sendEmail);
        if (hidden) {
            element.on('showButton', showButton);
            element.on('hideButton', hideButton);
        } else {
            element.show();
        }
    };
});
