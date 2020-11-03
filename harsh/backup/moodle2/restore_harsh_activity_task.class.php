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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/harsh/backup/moodle2/restore_harsh_stepslib.php'); // Because it exists (must)

class restore_harsh_activity_task extends restore_activity_task {

    protected function define_my_settings() {
       
    }

    protected function define_my_steps() {
        $this->add_step(new restore_harsh_activity_structure_step('harsh_structure', 'harsh.xml'));
    }

    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('harsh', array('intro', 'externalurl'), 'harsh');

        return $contents;
    }

       static public function define_decode_rules() {
        $rules = array();
        $rules[] = new restore_decode_rule('HARSHINDEX', '/mod/harsh/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('HARSHVIEWBYID', '/mod/harsh/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('HARSHVIEWBYU', '/mod/harsh/view.php?u=$1', 'harsh');

        return $rules;

    }

    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('harsh', 'add', 'view.php?id={course_module}', '{harsh}');
        $rules[] = new restore_log_rule('harsh', 'update', 'view.php?id={course_module}', '{harsh}');
        $rules[] = new restore_log_rule('harsh', 'view', 'view.php?id={course_module}', '{harsh}');

        return $rules;
    }

    static public function define_restore_log_rules_for_course() {
        $rules = array();
        $rules[] = new restore_log_rule('harsh', 'view all', 'index.php?id={course}', null);
        return $rules;
    }
}
