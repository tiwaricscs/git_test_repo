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
defined('MOODLE_INTERNAL') || die;

function harsh_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE: return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS: return false;
        case FEATURE_GROUPINGS: return false;
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE: return false;
        case FEATURE_GRADE_OUTCOMES: return false;
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;

        default: return null;
    }
}

function harsh_reset_userdata($data) {
    return array();
}

function harsh_get_view_actions() {
    return array('view', 'view all');
}

function harsh_get_post_actions() {
    return array('update', 'add');
}

function harsh_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/harsh/locallib.php');
    $parameters = array();
    for ($i = 0; $i < 100; $i++) {
        $parameter = "parameter_$i";
        $variable = "variable_$i";
        if (empty($data->$parameter) or empty($data->$variable)) {
            continue;
        }
        $parameters[$data->$parameter] = $data->$variable;
    }
    $data->parameters = serialize($parameters);

    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_FRAME) {
        $displayoptions['iframewidth'] = $data->iframewidth;
        $displayoptions['iframeheight'] = $data->iframeheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printintro'] = (int) !empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);
    $data->externalurl = harsh_fix_submitted_url($data->externalurl);
    $data->timemodified = time();
    $data->id = $DB->insert_record('harsh', $data);
    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'harsh', $data->id, $completiontimeexpected);
    return $data->id;
}

function harsh_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/harsh/locallib.php');
    $parameters = array();
    for ($i = 0; $i < 100; $i++) {
        $parameter = "parameter_$i";
        $variable = "variable_$i";
        if (empty($data->$parameter) or empty($data->$variable)) {
            continue;
        }
        $parameters[$data->$parameter] = $data->$variable;
    }
    $data->parameters = serialize($parameters);
    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_FRAME) {
        $displayoptions['iframewidth'] = $data->iframewidth;
        $displayoptions['iframeheight'] = $data->iframeheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printintro'] = (int) !empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);
    $data->externalurl = harsh_fix_submitted_url($data->externalurl);
    $data->timemodified = time();
    $data->id = $data->instance;
    $DB->update_record('harsh', $data);
    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'harsh', $data->id, $completiontimeexpected);
    return true;
}

function harsh_delete_instance($id) {
    global $DB;
    if (!$url = $DB->get_record('harsh', array('id' => $id))) {
        return false;
    }
    $cm = get_coursemodule_from_instance('harsh', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'harsh', $id, null);
    $DB->delete_records('harsh', array('id' => $url->id));
    return true;
}

function harsh_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/harsh/locallib.php");
    if (!$url = $DB->get_record('harsh', array('id' => $coursemodule->instance),
            'id, name, display, displayoptions, externalurl, parameters, intro, introformat')) {
        return NULL;
    }
    $info = new cached_cm_info();
    $info->name = $url->name;
    $info->icon = harsh_guess_icon($url->externalurl, 24);
    $display = harsh_get_final_display_type($url);
    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $fullurl = "$CFG->wwwroot/mod/harsh/view.php?id=$coursemodule->id&amp;redirect=1";
        $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
        $width = empty($options['popupwidth']) ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";
    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $fullurl = "$CFG->wwwroot/mod/harsh/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->onclick = "window.open('$fullurl'); return false;";
    }
    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('harsh', $url, $coursemodule->id, false);
    }
    return $info;
}
function harsh_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-url-*' => get_string('page-mod-url-x', 'harsh'));
    return $module_pagetype;
}
function harsh_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/harsh/locallib.php");
    $contents = array();
    $context = context_module::instance($cm->id);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $urlrecord = $DB->get_record('harsh', array('id' => $cm->instance), '*', MUST_EXIST);
    $fullurl = str_replace('&amp;', '&', harsh_get_full_url($urlrecord, $cm, $course));
    $isurl = clean_param($fullurl, PARAM_URL);
    if (empty($isurl)) {
        return null;
    }
    $url = array();
    $url['type'] = 'url';
    $url['filename'] = clean_param(format_string($urlrecord->name), PARAM_FILE);
    $url['filepath'] = null;
    $url['filesize'] = 0;
    $url['fileurl'] = $fullurl;
    $url['timecreated'] = null;
    $url['timemodified'] = $urlrecord->timemodified;
    $url['sortorder'] = null;
    $url['userid'] = null;
    $url['author'] = null;
    $url['license'] = null;
    $contents[] = $url;

    return $contents;
}

function harsh_dndupload_register() {
    return array('types' => array(
            array('identifier' => 'url', 'message' => get_string('createurl', 'harsh'))
    ));
}
function harsh_dndupload_handle($uploadinfo) {
    // Gather all the required data.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '<p>' . $uploadinfo->displayname . '</p>';
    $data->introformat = FORMAT_HTML;
    $data->externalurl = clean_param($uploadinfo->content, PARAM_URL);
    $data->timemodified = time();
    $data->coursemodule = $uploadinfo->coursemodule;
    $config = get_config('mod_harsh');
    $data->display = $config->display;
    $data->popupwidth = $config->popupwidth;
    $data->popupheight = $config->popupheight;
    $data->printintro = $config->printintro;
    return harsh_add_instance($data, null);
}

function harsh_view($url, $course, $cm, $context) {
    $params = array(
        'context' => $context,
        'objectid' => $url->id
    );
    $event = \mod_harsh\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('harsh', $url);
    $event->trigger();
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

function harsh_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

function mod_harsh_core_calendar_provide_event_action(calendar_event $event,
        \core_calendar\action_factory $factory, $userid = 0) {
    global $USER;
    if (empty($userid)) {
        $userid = $USER->id;
    }
    $cm = get_fast_modinfo($event->courseid, $userid)->instances['harsh'][$event->instance];
    $completion = new \completion_info($cm->get_course());
    $completiondata = $completion->get_data($cm, false, $userid);
    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }
    return $factory->create_instance(
                    get_string('view'),
                    new \moodle_url('/mod/harsh/view.php', ['id' => $cm->id]),
                    1,
                    true
    );
}
