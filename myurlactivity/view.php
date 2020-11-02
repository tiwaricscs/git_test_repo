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
 * myurlactivity module main user interface
 *
 * @author     <ballisticlearning.com>
 * @package    mod_myurlactivity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/myurlactivity/lib.php");
require_once("$CFG->dirroot/mod/myurlactivity/locallib.php");
require_once($CFG->libdir . '/completionlib.php');

$id                = optional_param('id', 0, PARAM_INT);        // Course module ID.
$u                 = optional_param('u', 0, PARAM_INT);         // URL instance id.
$redirect          = optional_param('redirect', 0, PARAM_BOOL);
$forceview         = optional_param('forceview', 0, PARAM_BOOL);

if ($u) {
    $url = $DB->get_record('myurlactivity', array('id' => $u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('myurlactivity', $url->id, $url->course, false, MUST_EXIST);
} else {
    $cm = myurlactivity_get_coursemodule_from_id('myurlactivity', $id, 0, false, MUST_EXIST);
    $url = $DB->get_record('myurlactivity', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/myurlactivity:view', $context);


$params = array(
    'context' => context_course::instance($course->id),
    'userid'  =>  $USER->id
);
$event = \mod_myurlactivity\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

// Completion and trigger events.
myurlactivity_view($url, $course, $cm, $context);

$PAGE->set_url('/mod/myurlactivity/view.php', array('id' => $cm->id));

//valdating external_url
$exturl = trim($url->externalurl);
if (empty($exturl) or $exturl === 'http://') {
    myurlactivity_print_header($url, $cm, $course);
    myurlactivity_print_heading($url, $cm, $course);
    myurlactivity_print_intro($url, $cm, $course);
    notice(get_string('invalidstoredurl', 'myurlactivity'), new moodle_url('/course/view.php', array('id' => $cm->course)));
    die;
}
unset($exturl);

$displaytype = myurlactivity_get_final_display_type($url);

if ($displaytype == ACTIVITYLIB_DISPLAY_EMBED) {
    $redirect = true;
}
 
if (!$redirect && $forceview) {
    // Coming from course page or url index page.
    // The redirection is needed for completion tracking and logging.
    $fullurl = str_replace('&amp;', '&', myurlactivity_get_full_url($url, $cm, $course));
    if (!course_get_format($course)->has_view_page()) {
        // If course format does not have a view page, add redirection delay with a link to the edit page.
        // Otherwise teacher is redirected to the external URL without any possibility to edit activity or course settings.
        $editurl = null;
        if (has_capability('moodle/course:manageactivities', $context)) {
            $editurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
            $edittext = get_string('editthisactivity');
        } else if (has_capability('moodle/course:update', $context->get_course_context())) {
            $editurl = new moodle_url('/course/edit.php', array('id' => $course->id));
            $edittext = get_string('editcoursesettings');
        }
        if ($editurl) {
            redirect($fullurl, html_writer::link($editurl, $edittext)."<br/>".
                    get_string('pageshouldredirect'), 10);
        }
    }
}
 
switch ($displaytype) {
    case ACTIVITYLIB_DISPLAY_FRAME:
      myurlactivity_display_frame($url, $cm, $course);
      break;
    case ACTIVITYLIB_DISPLAY_EMBED:
        $config = get_config('myurlactivity');
        myurlactivity_display_embed($url, $cm, $course, $config);
        break;
    default:
        myurlactivity_print_workaround($url, $cm, $course);
        break;
}
