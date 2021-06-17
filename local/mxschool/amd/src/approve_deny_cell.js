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
 * Logic for an approve/deny cell in a report
 *
 * @module      local_mxschool/comment
 * @package     local_mxschool
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   20021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
	function approve_clicked(event) {
		var approve_button = $(event.target);
		var id = approve_button.attr('value');
		var deny_button = $('.mx-deny-button'+id);
		var undo_button = $('.mx-ad-undo-button'+id);
		var text = $('.mx-ad-text'+id);

		approve_button.hide();
		deny_button.hide();
		text.text('Approved');
		text.css('color', 'mediumseagreen');
		text.show();
		undo_button.show();

		var field = approve_button.attr('name');
		var table = deny_button.attr('name');
		var promises = ajax.call([{
			methodname: 'local_mxschool_update_approve_deny_cell',
			args: {
				id: id,
				field: field,
				table: table,
				new_value: 1
			}
		}]);
	     promises[0].done().fail(notification.exception);
	}
	function deny_clicked(event) {
		var deny_button = $(event.target);
		var id = deny_button.attr('value');
		var approve_button = $('.mx-approve-button'+id);
		var undo_button = $('.mx-ad-undo-button'+id);
		var text = $('.mx-ad-text'+id);

		approve_button.hide();
		deny_button.hide();
		text.text('Denied');
		text.css('color', 'indianred');
		text.show();
		undo_button.show();

		var field = approve_button.attr('name');
		var table = deny_button.attr('name');
		var promises = ajax.call([{
			methodname: 'local_mxschool_update_approve_deny_cell',
			args: {
				id: id,
				field: field,
				table: table,
				new_value: 2
			}
		}]);
		promises[0].done().fail(notification.exception);
	}
	function undo_clicked(event) {
		var undo_button = $(event.target);
		var id = undo_button.attr('value');
		var approve_button = $('.mx-approve-button'+id);
		var deny_button = $('.mx-deny-button'+id);
		var text = $('.mx-ad-text'+id);

		approve_button.show();
		deny_button.show();
		undo_button.hide();
		text.hide();

		var field = approve_button.attr('name');
		var table = deny_button.attr('name');
		var promises = ajax.call([{
			methodname: 'local_mxschool_update_approve_deny_cell',
			args: {
				id: id,
				field: field,
				table: table,
				new_value: 0
			}
		}]);
		promises[0].done().fail(notification.exception);
	}
    return function(id, status) {
	    var approve_button = $('.mx-approve-button'+id);
	    var deny_button = $('.mx-deny-button'+id);
	    var undo_button = $('.mx-ad-undo-button'+id);
	    var text = $('.mx-ad-text'+id);
	    // Set default value for override_status column
	    if(status == 0) {
		    approve_button.show();
		    deny_button.show();
		    undo_button.hide();
		    text.hide();
	    }
	    else if(status == 1) {
		    approve_button.hide();
		    deny_button.hide();
		    text.text('Approved');
		    text.css('color', 'mediumseagreen');
		    text.show();
		    undo_button.show();
	    }
	    else if(status == 2) {
		    approve_button.hide();
		    deny_button.hide();
		    text.text('Denied');
		    text.css('color', 'indianred');
		    text.show();
		    undo_button.show();
	    }
	    else {
		    throw "Unrecognized status value in database: "+status;
	    }
	    approve_button.click(approve_clicked);
	    deny_button.click(deny_clicked);
	    undo_button.click(undo_clicked);
    };
});
