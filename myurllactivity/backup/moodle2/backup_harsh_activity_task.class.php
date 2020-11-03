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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/myurllactivity/backup/moodle2/backup_myurllactivity_stepslib.php');
 
class backup_myurllactivity_activity_task extends backup_activity_task {
 
    protected function define_my_settings() {
    }
     protected function define_my_steps() {
        $this->add_step(new backup_myurllactivity_activity_structure_step('myurllactivity_structure', 'myurllactivity.xml'));
    }
     static public function encode_content_links($content) {
        global $CFG;
        $base = preg_quote($CFG->wwwroot.'/mod/myurllactivity','#');
        $pattern = '#('.$base.'/index\.php\?id=)([0-9]+)#';
        $replacement = '$@myurllactivityINDEX*$2@$';
        $content = preg_replace($pattern, $replacement, $content);
        $pattern = '#('.$base.'/view\.php\?id=)([0-9]+)#';
        $replacement = '$@myurllactivityVIEWBYID*$2@$';
        $content = preg_replace($pattern, $replacement, $content);
        $pattern = '#('.$base.'/view\.php\?u=)([0-9]+)#';
        $replacement = '$@myurllactivityVIEWBYU*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        return $content;
    }
}
