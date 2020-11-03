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
 * . 
 *
 * @package    mod_harsh
 * @author     <Ballisticlearning.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_harsh\task;

defined('MOODLE_INTERNAL') || die();

class api_log_deleted extends \core\task\scheduled_task {
    public function execute() {
        global $DB;
        $condition = array('eventname' == '\mod_harsh\event\course_module_viewed');
        $DB->delete_records('logstore_standard_log', $condition);
    }
    public function get_name(): string {
        return get_string('schedule_alert', 'harsh');
    }
}
?> 