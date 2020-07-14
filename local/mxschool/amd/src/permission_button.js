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
 * Edits permissions in a report table.
 *
 * @module      local_mxschool/comment
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    function update_permission(event) {
		var button = $(event.target);
		var id = button.attr('value');
		var name = button.attr('name');
		var text = $('.mx-permission-text'+name+id);
		var userid = text.attr('name');
		var package_name = text.attr('value');
		if(text.text() == '') {
			text.text('Under Review');
			text.css('color', 'cadetblue');
			button.text('Approve');
			button.css('background-color', 'mediumseagreen');
			button.css('border-color', 'mediumseagreen');
		}
		else if(text.text() == 'Under Review') {
			text.text('Approved');
			text.css('color', 'mediumseagreen');
			button.text('Undo');
			button.css('background-color', 'dimgray');
			button.css('border-color', 'dimgray');
		}
		else if(text.text() == 'Approved') {
			text.text('');
			button.text('Request Review');
			button.css('background-color', 'dodgerblue');
			button.css('border-color', 'dodgerblue');
		}
		else {
			throw "Unrecognized permission text: "+text.text();
		}
		// CALL METHOD HERE
    }
    return function(id, userid, name, current_status) {
	    var button = $('.mx-permission-button'+name+id);
	    var text = $('.mx-permission-text'+name+id);
	    if(current_status == 0) {
		    text.text('');
		    button.text('Request Review');
		    button.css('background-color', 'dodgerblue');
    		    button.css('border-color', 'dodgerblue');
	    }
	    else if(current_status == 1) {
		    text.text('Under Review');
		    text.css('color', 'cadetblue');
		    button.text('Approve');
		    button.css('background-color', 'mediumseagreen');
    		    button.css('border-color', 'mediumseagreen');
	    }
	    else if(current_status == 2) {
		    text.text('Approved');
		    text.css('color', 'mediumseagreen');
		    button.text('Undo');
		    button.css('background-color', 'dimgray');
    		    button.css('border-color', 'dimgray');
	    }
	    else {
		    throw "Unrecognized permission value in database: "+current_status+" for "+name;
	    }
	    button.click(update_permission);
    };
});
