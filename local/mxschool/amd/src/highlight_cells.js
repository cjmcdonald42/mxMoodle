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
 * Highlighs table cells for Middlesex's Dorm and Student Functions Plugin.
 * This module is designed to be used for the weekend calculator and is currently very non-reusable.
 *
 * @module      local_mxschool/highlight_cells
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    function highlight() {
        $('.mx-table tbody > tr').each(function(index, element) {
            var formatCell = $(element).children('.highlight-format');
            var referenceCell = $(element).children('.highlight-reference');
            if (formatCell && referenceCell) {
                var difference = referenceCell.text() - formatCell.text();
                if (difference >= 3) {
                    formatCell.addClass('table-success');
                } else if (difference === 2) {
                    formatCell.addClass('table-info');
                } else if (difference === 1) {
                    formatCell.addClass('table-warning');
                } else if (difference <= 0) {
                    formatCell.addClass('table-danger');
                }
            }
        });
    }
    return function() {
        $(document).ready(highlight);
    };
});
