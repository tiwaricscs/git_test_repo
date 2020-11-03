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
require('../../config.php');
require_once("$CFG->dirroot/mod/myurllactivity/lib.php");
require_once("$CFG->dirroot/mod/myurllactivity/locallib.php");
require_once($CFG->libdir . '/completionlib.php');
$id = optional_param('id', 0, PARAM_INT);        // Course module ID
$u = optional_param('u', 0, PARAM_INT);         // URL instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);
$forceview = optional_param('forceview', 0, PARAM_BOOL);

if ($u) {  
    $myurllactivity = $DB->get_record('myurllactivity', array('id' => $u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('url', $myurllactivity->id, $myurllactivity->course, false, MUST_EXIST);
} else {
    $cm = get_coursemodule_from_id('myurllactivity', $id, 0, false, MUST_EXIST);
    $myurllactivity = $DB->get_record('myurllactivity', array('id' => $cm->instance), '*', MUST_EXIST);
}
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/myurllactivity:view', $context);
$modcontext = context_module::instance($cm->id);
$users = get_enrolled_users($modcontext);
$coursecontext = context_course::instance($course->id);
$role = $DB->get_record('role', array('shortname' => 'student'));
$obj_student = get_role_users($role->id, $coursecontext);
$activity_completion = $DB->get_record('course_modules_completion', array('coursemoduleid' => $cm->id, 'userid' => $USER->id), 'completionstate');
$role_teacher = $DB->get_record('role', array('shortname' => 'teacher'));
$role = $DB->get_record('role', array('shortname' => 'student'));
$obj_teacher = get_role_users(3, $coursecontext);

if ($activity_completion != null) {
    if ($activity_completion->completionstate > 0) {
        if (user_has_role_assignment($USER->id, 5)) {

            $user = get_complete_user_data('id', $USER->id);
            $from = 'noreply@ballisticlearning.com';
            $subject = get_string('completion', 'myurllactivity');
            $messagetext = get_string('completion_subject', 'myurllactivity');
            $messagehtml = '';
            email_to_user($user, $from, $subject, $messagetext, $messagehtml = '', $attachment = '', $attachname = '',
                    $wordwrapwidth = 79);
        }
    }
}
myurllactivity_view($myurllactivity, $course, $cm, $context);
$PAGE->set_url('/mod/myurllactivity/view.php', array('id' => $cm->id));
$exturl = trim($myurllactivity->externalurl);
if (empty($exturl) or $exturl === 'http://') {
    myurllactivity_print_header($myurllactivity, $cm, $course);
    myurllactivity_print_heading($myurllactivity, $cm, $course);
    myurllactivity_print_intro($myurllactivity, $cm, $course);
    notice(get_string('invalidstoredurl', 'myurllactivity'), new moodle_url('/course/view.php', array('id' => $cm->course)));
    die;
}
unset($exturl);
$displaytype = myurllactivity_get_final_display_type($myurllactivity);
if ($redirect && !$forceview) {
    $fullurl = str_replace('&amp;', '&', myurllactivity_get_full_url($myurllactivity, $cm, $course));
    if (!course_get_format($course)->has_view_page()) {
        $editurl = null;
        if (has_capability('moodle/course:manageactivities', $context)) {
            $editurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
            $edittext = get_string('editthisactivity');
        } else if (has_capability('moodle/course:update', $context->get_course_context())) {
            $editurl = new moodle_url('/course/edit.php', array('id' => $course->id));
            $edittext = get_string('editcoursesettings');
        }
        if ($editurl) {
            redirect($fullurl, html_writer::link($editurl, $edittext) . "<br/>" .
                    get_string('pageshouldredirect'), 10);
        }
    }
    redirect($fullurl);
}
switch ($displaytype) {
    case RESOURCELIB_DISPLAY_EMBED:
        myurllactivity_display_embed($myurllactivity, $cm, $course);
        break;
    case RESOURCELIB_DISPLAY_FRAME:
        myurllactivity_display_frame($myurllactivity, $cm, $course);
        break;
    default:
        myurllactivity_print_workaround($myurllactivity, $cm, $course);
        break;
}
