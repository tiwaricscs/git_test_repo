<?php
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
 * This is lib.php file which is used for defining logical function.
 * 
 * @package local_user_chandan
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();
function list_all_data($limit, $countpage)
{
    global $DB;
    $list = $DB->get_records('local_sr_registration', [], '', '*', $limit, $countpage);
    return $list;
}
function list_all_data_with_ajax($limit, $countpage, $search_key)
{
    global $DB;
    $query = "SELECT * FROM {local_sr_registration} WHERE name LIKE '%$search_key%'";
    $list = $DB->get_records_sql($query, [], $limit, $countpage);
    return $list;
}

