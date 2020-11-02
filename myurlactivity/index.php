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
 * List of urls in course
 *
 * @author     <ballisticlearning.com>
 * @package    mod_myurlactivity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$id = required_param('id', PARAM_INT); // Course id.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$myurlactivity = get_string('modulename', 'myurlactivity');
$myurlactivitys = get_string('modulenameplural', 'myurlactivity');

$PAGE->set_url('/mod/myurlactivity/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname . ': ' . $myurlactivitys);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($myurlactivitys);
echo $OUTPUT->header();
echo $OUTPUT->heading($myurlactivitys);

if (!$urls = get_all_instances_in_course('myurlactivity', $course)) {
    notice(get_string('thereareno', 'moodle', $strurls), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}
$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';
if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_' . $course->format);
    $table->head = array($strsectionname, $strname, $strintro);
    $table->align = array('center', 'left', 'left');
} else {
    $table->head = array($strlastmodified, $strname, $strintro);
    $table->align = array('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($urls as $url) {
    $cm = $modinfo->cms[$url->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($url->section !== $currentsection) {
            if ($url->section) {
                $printsection = get_section_name($course, $url->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $url->section;
        }
    } else {
        $printsection = '<span class="smallinfo">' . userdate($url->timemodified) . "</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        $icon = $OUTPUT->pix_icon($cm->icon, get_string('modulename', $cm->modname)) . ' ';
    }
    $class = $url->visible ? '' : 'class="btn"'; // Hidden modules are dimmed.
    $table->data[] = array(
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">" . $icon . format_string($url->name) . "</a>",
        format_module_intro('myurlactivity', $url, $cm->id));
}

echo html_writer::table($table);
echo $OUTPUT->footer();
