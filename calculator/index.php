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
 * @package    mod_calculator
 * @author     <Ballisticlearning.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

global $DB;

$id = required_param('id', PARAM_INT);   // course

if (! $course = $DB->get_record('course', array('id'=>$id))) {
    error('Course ID is incorrect');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');

$strcalculators = get_string('modulenameplural', 'calculator');
$strcalculator  = get_string('modulename', 'calculator');

$PAGE->set_url('/mod/calculator/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strcalculators);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strcalculators);
echo $OUTPUT->header();
echo $OUTPUT->heading($strcalculators);



if (! $calculators = get_all_instances_in_course('calculator', $course)) {
    notice('There are no instances of calculator', "../../course/view.php?id=$course->id");
    die;
}


$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($calculators as $calculator) {
    if (!$calculator->visible) {
        
        $edit_link = new moodle_url('view.php', array('id' => $calculator->coursemodule, 'class'=>'dimmed'));
        $link= html_writer::tag('a', format_string($calculator->name), array('href' => $edit_link));

    } else {
       
        $edit_link = new moodle_url('view.php', array('id' => $calculator->coursemodule));
        $link= html_writer::tag('a', format_string($calculator->name), array('href' => $edit_link));
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($calculator->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}


echo html_writer::table($table);

echo $OUTPUT->footer();

?>
