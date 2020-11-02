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
 * Mandatory public API of myurlactivity module
 *
 * @author     <ballisticlearning.com>
 * @package    mod_myurlactivity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in URL module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function myurlactivity_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

define('ACTIVITYLIB_DISPLAY_EMBED', 1); //To Display using object tag

define('ACTIVITYLIB_DISPLAY_FRAME', 2); //To Display inside frame

define('ACTIVITYLIB_DISPLAY_POPUP', 3); //To Display with pop up

/* Returns list of available display options
 */
function activitylib_get_displayoptions(array $enabled, $current=null) {
    if (is_number($current)) {
        $enabled[] = $current;
    }
    $options = array(ACTIVITYLIB_DISPLAY_EMBED    => get_string('activitydisplayembed', 'myurlactivity'),
                     ACTIVITYLIB_DISPLAY_FRAME    => get_string('activitydisplayframe', 'myurlactivity'),
                     ACTIVITYLIB_DISPLAY_POPUP    => get_string('activitydisplaypopup', 'myurlactivity'),
                     );
    $result = array();
    foreach ($options as $key => $value) {
        if (in_array($key, $enabled)) {
            $result[$key] = $value;
        }
    }
    if (empty($result)) {
        // if empty then set one option to return result..
        $result[ACTIVITYLIB_DISPLAY_EMBED] = $options[ACTIVITYLIB_DISPLAY_EMBED];
    }
    return $result;
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 */
function myurlactivity_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 */
function myurlactivity_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add myurlactivity instance.
 * @param object $data
 * @param object $mform
 * @return int new url instance id
 */

function myurlactivity_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/myurlactivity/locallib.php');
    $parameters = array();
    // Call Created checkbox.
    $parameters[$data->userid] = $data->userid;
    $parameters[$data->courseid] = $data->courseid;
    $data->parameters = serialize($parameters);
    $displayoptions = array();
    if ($data->display == ACTIVITYLIB_DISPLAY_FRAME) {
        $displayoptions['iframewidth'] =  ($data->iframewidth ?? "100");
        $displayoptions['iframeheight'] = ($data->iframeheight ?? '100');
    }
    if (in_array($data->display, array(ACTIVITYLIB_DISPLAY_EMBED, ACTIVITYLIB_DISPLAY_FRAME))) {
        $displayoptions['printintro'] = (int) !empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);

    $data->externalurl = myurlactivity_fix_submitted_url($data->externalurl);

    $data->timemodified = time();
    $data->id = $DB->insert_record('myurlactivity', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'myurlactivity', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update myurlactivity instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function myurlactivity_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/myurlactivity/locallib.php');
     // Call Created checkbox.
    $parameters = array();
    $parameters[$data->userid] = $data->userid;
    $parameters[$data->courseid] = $data->courseid;
    $data->parameters = serialize($parameters);

    $displayoptions = array();
    if (in_array($data->display, array(ACTIVITYLIB_DISPLAY_EMBED, ACTIVITYLIB_DISPLAY_FRAME))) {
        $displayoptions['printintro'] = (int) !empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);

    $data->externalurl = myurlactivity_fix_submitted_url($data->externalurl);

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('myurlactivity', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'myurlactivity', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Delete myurlactivity instance.
 * @param int $id
 * @return bool true
 */
function myurlactivity_delete_instance($id) {
    global $DB;

    if (!$url = $DB->get_record('myurlactivity', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('myurlactivity', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'myurlactivity', $id, null);

    // Note: all context files are deleted automatically.
    $DB->delete_records('myurlactivity', array('id' => $url->id));

    return true;
}

/**
 * this function returns any "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * @param object $coursemodule
 * @return cached_cm_info info
 */
function myurlactivity_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/myurlactivity/locallib.php");

    if (!$url = $DB->get_record('myurlactivity', array('id' => $coursemodule->instance),
            'id, name, display, displayoptions, externalurl, parameters, intro, introformat')) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $url->name;

    // Note: there should be a way to differentiate links from normal resources.

    $display = myurlactivity_get_final_display_type($url);

    if ($display == ACTIVITYLIB_DISPLAY_EMBED) {
      $fullurl = "$CFG->wwwroot/mod/myurlactivity/view.php?id=$coursemodule->id";
      $info->onclick = "window.location('$fullurl'); return false;";
    } else if ($display == ACTIVITYLIB_DISPLAY_FRAME
            ) {
              $fullurl = "$CFG->wwwroot/mod/myurlactivity/view.php?id=$coursemodule->id";
              $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
              $width = empty($options['iframewidth']) ? 400 : $options['iframewidth'];
              $height = empty($options['iframeheight']) ? 200 : $options['iframeheight'];
              $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,"
                      . "directories=no,scrollbars=yes,resizable=yes";
              $info->onclick = "window.location('$fullurl', '', '$wh'); return false;";
    } else if ($display == ACTIVITYLIB_DISPLAY_POPUP) {
        $fullurl = "$CFG->wwwroot/mod/myurlactivity/view.php?id=$coursemodule->id";
        $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.location('$fullurl', '', '$wh'); return false;";
    }

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('myurlactivity', $url, $coursemodule->id, false);
    }
    return $info;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 */
function myurlactivity_view($url, $course, $cm, $context) {
    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $url->id
    );
    $event = \mod_resource\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('myurlactivity', $url);
    $event->trigger();
    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}
/**
 * Returns general link or file embedding html.
 */
function myurlactivity_iframe_general($fullurl, $title, $clicktoopen, $mimetype, $style) {
    global $CFG, $PAGE;
    if ($fullurl instanceof moodle_url) {
        $fullurl = $fullurl->out();
    }
    $param = '<param name="src" value="'.$fullurl.'" />';
    $code = <<<EOT
<div class="resourcecontent resourcegeneral">
  <iframe id="resourceobject" src="$fullurl" style = "$style">
    $clicktoopen
  </iframe>
</div>
EOT;
    return $code;
}
