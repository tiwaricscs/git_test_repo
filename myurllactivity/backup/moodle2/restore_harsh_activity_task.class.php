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
 * @package    mod_myurllactivity
 * @author     <Ballisticlearning.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/myurllactivity/backup/moodle2/restore_myurllactivity_stepslib.php'); // Because it exists (must)

class restore_myurllactivity_activity_task extends restore_activity_task {

    protected function define_my_settings() {
       
    }

    protected function define_my_steps() {
        $this->add_step(new restore_myurllactivity_activity_structure_step('myurllactivity_structure', 'myurllactivity.xml'));
    }

    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('myurllactivity', array('intro', 'externalurl'), 'myurllactivity');

        return $contents;
    }

       static public function define_decode_rules() {
        $rules = array();
        $rules[] = new restore_decode_rule('myurllactivityINDEX', '/mod/myurllactivity/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('myurllactivityVIEWBYID', '/mod/myurllactivity/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('myurllactivityVIEWBYU', '/mod/myurllactivity/view.php?u=$1', 'myurllactivity');

        return $rules;

    }

    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('myurllactivity', 'add', 'view.php?id={course_module}', '{myurllactivity}');
        $rules[] = new restore_log_rule('myurllactivity', 'update', 'view.php?id={course_module}', '{myurllactivity}');
        $rules[] = new restore_log_rule('myurllactivity', 'view', 'view.php?id={course_module}', '{myurllactivity}');

        return $rules;
    }

    static public function define_restore_log_rules_for_course() {
        $rules = array();
        $rules[] = new restore_log_rule('myurllactivity', 'view all', 'index.php?id={course}', null);
        return $rules;
    }
}
