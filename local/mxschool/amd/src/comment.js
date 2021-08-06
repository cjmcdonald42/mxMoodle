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
    function edit_comment(event) {
	   var name = $(event.target).attr('name');
   	   var userid = $(event.target).attr('value');
	   $('.mx-comment-text'+userid+name).hide();
	   $('.mx-comment-edit-button'+userid+name).hide();
	   $('.mx-comment-edit-area'+userid+name).val($('.mx-comment-text'+userid+name).text());
	   $('.mx-comment-edit-area'+userid+name).show();
	   $('.mx-comment-save-button'+userid+name).show();
    }
    function update_comment() {
	    var name = $(event.target).attr('name');
    	    var userid = $(event.target).attr('value');
	    var new_comment = $('.mx-comment-edit-area'+userid+name).val();
	    var table = $('.mx-comment-edit-area'+userid+name).attr('name');
	    var promises = ajax.call([{
		    methodname: 'local_mxschool_update_comment',
		    args: {
			    id: userid,
			    text: new_comment,
			    table: table
		    }
	    }]);
	    promises[0].done().fail(notification.exception);
	    $('.mx-comment-text'+userid+name).text(new_comment);
	    $('.mx-comment-text'+userid+name).show();
	    $('.mx-comment-edit-button'+userid+name).show();
	    $('.mx-comment-edit-area'+userid+name).hide();
	    $('.mx-comment-save-button'+userid+name).hide();
    }
    return function(userid, name, original_comment_text) {
        var edit_button = $('.mx-comment-edit-button'+userid+name);
	   var comment_edit_area = $('.mx-comment-edit-area'+userid+name);
	   var save_button = $('.mx-comment-save-button'+userid+name);
	   var comment_text = $('.mx-comment-text'+userid+name);
	   comment_text.show();
	   comment_edit_area.hide();
	   save_button.hide();
        edit_button.click(edit_comment);
	   save_button.click(update_comment);
    };
});
