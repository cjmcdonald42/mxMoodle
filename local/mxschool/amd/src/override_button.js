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
 * Edits a comment in the Healthpass report table.
 *
 * @module      local_mxschool/comment
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
	function update_override(event) {
		var button = $(event.target);
		var userid = button.attr('name');
		var override_text = $('.mx-override-text'+userid);
		var health_status = $('.mx-changeable-text'+'status'+userid);
		var contact_info = $('.mx-changeable-text'+'contact_info'+userid);
		var new_health_status = health_status.text()=='Approved' ? 'Denied' : 'Approved';
		var old_health_status = health_status.text();
		var old_override_status = override_text.text();
		if(old_override_status == '') {
			old_override_status = 'Not Overridden';
		}
		if(override_text.text() == '') {
			contact_info.show();
			override_text.text('Under Review');
			override_text.css('color', 'cadetblue');
			button.text('Override');
			button.css('background-color', 'lightsalmon');
			button.css('border-color', 'lightsalmon');
		}
		else if(override_text.text() == 'Under Review') {
			contact_info.hide();
			override_text.text('Overridden');
			override_text.css('color', 'lightsalmon');
			button.text('Undo');
			button.css('background-color', 'dimgray');
			button.css('border-color', 'dimgray');
			health_status.text(new_health_status);
			if(new_health_status == 'Approved') {
				health_status.css('color', 'green');
			}
			else if(new_health_status == 'Denied') {
				health_status.css('color', 'red');
			}
		}
		else if(override_text.text() == 'Overridden') {
			contact_info.hide();
			override_text.text('');
			button.text('Review');
			button.css('background-color', 'dodgerblue');
			button.css('border-color', 'dodgerblue');
			health_status.text(new_health_status);
			if(new_health_status == 'Approved') {
				health_status.css('color', 'green');
			}
			else if(new_health_status == 'Denied') {
				health_status.css('color', 'red');
			}
		}
		else {
			throw "Unrecognized override text: "+override_text.text();
		}
		var promises = ajax.call([{
			methodname: 'local_mxschool_update_healthform_override_status',
			args: {
				userid: userid,
				status: String(old_health_status),
				override_status: String(old_override_status)
			}
		}]);
	     promises[0].done().fail(notification.exception);
	}
    return function(userid) {
	    var button = $('.mx-override-button'+userid);
	    var override_text = $('.mx-override-text'+userid);
	    var override_status = button.attr('value');
	    var health_status = $('.mx-changeable-text'+'status'+userid);
	    var contact_info = $('.mx-changeable-text'+'contact_info'+userid);
	    // Set default value for override_status column
	    if(override_status == 'Not Overridden') {
		    contact_info.hide();
		    override_text.text('');
		    button.text('Review');
		    button.css('background-color', 'dodgerblue');
		    button.css('border-color', 'dodgerblue');
	    }
	    else if(override_status == 'Under Review') {
		    contact_info.show();
		    override_text.text('Under Review');
		    override_text.css('color', 'cadetblue');
		    button.text('Override');
		    button.css('background-color', 'lightsalmon');
		    button.css('border-color', 'lightsalmon');
	    }
	    else if(override_status == 'Overridden') {
		    contact_info.hide();
		    override_text.text('Overridden');
		    override_text.css('color', 'lightsalmon');
		    button.text('Undo');
		    button.css('background-color', 'dimgray');
		    button.css('border-color', 'dimgray');
	    }
	    else {
		    throw "Unrecognized override_status value in database: "+override_status;
	    }
	    // Set color value for status column
	    if(health_status.text() == 'Approved') {
		    health_status.css('color', 'green');
	    }
	    else if(health_status.text() == 'Denied') {
		    health_status.css('color', 'red');
	    }
	    else {
		    throw "Should not be overriding unsubmitted form of userid: "+userid;
	    }
	    button.click(update_override);
    };
});
