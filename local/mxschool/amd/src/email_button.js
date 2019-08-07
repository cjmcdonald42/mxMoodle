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
 * Sends an email for Middlesex's Dorm and Student Functions Plugin.
 *
 * @module      local_mxschool/email_button
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str', 'core/ajax', 'core/notification'], function($, str, ajax, notification) {
    function sendEmail(event) {
        var element = $(event.target);
        var text = element.text();
        $.when(str.get_string('email_button:sending', 'local_mxschool')).done(function(sendingString) {
            element.text(sendingString);
        });
        var promises = ajax.call([{
            methodname: 'local_mxschool_send_email',
            args: {
                emailclass: element.attr('name'),
                emailparams: {
                    id: element.val()
                }
            }
        }]);
        promises[0].done(function(result) {
            var unlocalized = 'email_button:' + (result ? 'success' : 'failure');
            $.when(str.get_string(unlocalized, 'local_mxschool')).done(function(sentString) {
                element.text(sentString);
                setTimeout(function() {
                    element.trigger('hideButton');
                    setTimeout(function() {
                        element.text(text);
                    }, 4000);
                }, 1000);
            });
        }).fail(notification.exception);
    }
    function confirmSend(event) {
        $.when(str.get_string('email_button:confirmation', 'local_mxschool')).done(function(cofirmationText) {
            if (confirm(cofirmationText)) {
                sendEmail(event);
            }
        });
    }
    return function(value, name, requireConfirmation, hidden) {
        var element = $('button.mx-email-button[value="' + value + '"][name="' + name + '"]');
        element.click(requireConfirmation ? confirmSend : sendEmail);
        if (hidden) {
            element.on('showButton', function(event) {
                $(event.target).show('slow');
            });
            element.on('hideButton', function(event) {
                $(event.target).hide('slow');
            });
            var checkbox = element.parent().children('div.mx-checkbox').children().eq(0);
            if (checkbox) {
                checkbox.on('checkboxEnabled', function() {
                    element.trigger('showButton');
                });
                checkbox.on('checkboxDisabled', function() {
                    element.trigger('hideButton');
                });
            }
        }
    };
});
