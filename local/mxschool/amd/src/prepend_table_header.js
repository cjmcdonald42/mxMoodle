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
 * Prepends a second header to a table for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @module     local_mxschool/prepend_table_header
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    return function(headers) {
        $(document).ready(function() {
            $('.mx-table thead').prepend(function() {
                var row = $('<tr></tr>');
                var count = 0;
                $.each(headers, function(index, header) {
                    row.append($('<th></th>').attr('class', function() {
                        var result = 'header';
                        for (var c = count; c < count + header.length; c++) {
                            result += ' c' + c;
                        }
                        count += header.length;
                        return result;
                    }).attr('colspan', header.length).attr('scope', 'col').text(header.text));
                });
                return row;
            });
        });
    };
});
