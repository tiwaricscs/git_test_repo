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
 * myurlactivity module functions
 *
 * @author     <ballisticlearning.com>
 * @package    mod_myurlactivity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/myurlactivity/lib.php");

/**
 * Fix common URL problems that we want teachers to see fixed
 * the next time they edit the resource.
 *
 * This function does not include any XSS protection.
 * @param string $url
 * @return string
 */
function myurlactivity_fix_submitted_url($url) {
    // Note: empty urls are prevented in form validation.
    $url = trim($url);

    // Remove encoded entities - we want the raw URI here.
    $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');

    if (!preg_match('|^[a-z]+:|i', $url) and ! preg_match('|^/|', $url)) {
        // Invalid URI, try to fix it by making it normal URL,
        // Please note relative urls are not allowed, /xx/yy links are ok.
        $url = 'http://' . $url;
    }

    return $url;
}

/**
 * Return full myurlactivity with all extra parameters
 * @param string $url
 * @param object $cm
 * @param object $course
 * @param object $config
 * @return string url with & encoded as &amp;
 */
function myurlactivity_get_full_url($url, $cm, $course, $config = null) {

    $parameters = empty($url->parameters) ? array() : unserialize($url->parameters);

    // Make sure there are no encoded entities, it is ok to do this twice.
    $fullurl = html_entity_decode($url->externalurl, ENT_QUOTES, 'UTF-8');

    if (preg_match('/^(\/|https?:|ftp:)/i', $fullurl) or preg_match('|^/|', $fullurl)) {
        // Encode extra chars in URLs - this does not make it always valid, but it helps with some UTF-8 problems.
        $allowed = "a-zA-Z0-9" . preg_quote(';/?:@=&$_.+!*(),-#%', '/');
        $fullurl = preg_replace_callback("/[^$allowed]/", 'myurlactivity_filter_callback', $fullurl);
    } else {
        // Rncode special chars only.
        $fullurl = str_replace('"', '%22', $fullurl);
        $fullurl = str_replace('\'', '%27', $fullurl);
        $fullurl = str_replace(' ', '%20', $fullurl);
        $fullurl = str_replace('<', '%3C', $fullurl);
        $fullurl = str_replace('>', '%3E', $fullurl);
    }

    // Add variable url parameters.
    if (!empty($parameters)) {
        if (!$config) {
            $config = get_config('myurlactivity');
        }
        $paramvalues = myurlactivity_get_variable_values($url, $cm, $course, $config);

        foreach ($parameters as $parse => $parameter) {
            if (isset($paramvalues[$parameter])) {
                $parameters[$parse] = rawurlencode($parse) . '=' . rawurlencode($paramvalues[$parameter]);
            } else {
                unset($parameters[$parse]);
            }
        }

        if (!empty($parameters)) {
            if (stripos($fullurl, 'teamspeak://') === 0) {
                $fullurl = $fullurl . '?' . implode('?', $parameters);
            } else {
                $join = (strpos($fullurl, '?') === false) ? '?' : '&';
                $fullurl = $fullurl . $join . implode('&', $parameters);
            }
        }
    }

    // Encode all & to &amp; entity.
    $fullurl = str_replace('&', '&amp;', $fullurl);

    return $fullurl;
}

/**
 * Unicode encoding helper callback
 * @internal
 * @param array $matches
 * @return string
 */
function myurlactivity_filter_callback($matches) {
    return rawurlencode($matches[0]);
}

/**
 * Print myurlactivity header.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @return void
 */
function myurlactivity_print_header($url, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname . ': ' . $url->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($url);
    echo $OUTPUT->header();
}

/**
 * Print myurlactivity heading.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @param bool $notused This variable is no longer used.
 * @return void
 */
function myurlactivity_print_heading($url, $cm, $course, $notused = false) {
    global $OUTPUT;
    echo $OUTPUT->heading(format_string($url->name), 2);
}

/**
 * Print myurlactivity introduction.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function myurlactivity_print_intro($url, $cm, $course, $ignoresettings = false) {
    global $OUTPUT;

    $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
    if ($ignoresettings or ! empty($options['printintro'])) {
        if (trim(strip_tags($url->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'urlintro');
            echo format_module_intro('myurlactivity', $url, $cm->id);
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Display myurlactivity frames.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function myurlactivity_display_frame($url, $cm, $course) {
    global $PAGE, $OUTPUT, $CFG;
    echo $OUTPUT->header();
    $frame = optional_param('frameset', 'main', PARAM_ALPHA);
    if ($frame === 'top') {
        $PAGE->set_pagelayout('frametop');
        myurlactivity_print_header($url, $cm, $course);
        myurlactivity_print_heading($url, $cm, $course);
        myurlactivity_print_intro($url, $cm, $course);
        echo $OUTPUT->footer();
        die;
    } else {
        $curl = new curl();
        $config = get_config('myurlactivity');
        $exteurl = myurlactivity_get_full_url($url, $cm, $course, $config);
        echo html_writer::start_tag('div');
        echo html_writer::start_tag('img', array('src' => $CFG->wwwroot.'/mod/myurlactivity/pix/loader.gif',
            'class' => 'loader_main_overlay', 'url' => $exteurl));
        $result = $curl->get($exteurl);
        echo "$result";
        echo html_writer::end_tag('div');
        $PAGE->requires->js_call_amd('mod_myurlactivity/main', 'loader');
        echo $OUTPUT->footer();
        die;
    }
}

/**
 * Display embedded myurlactivity file.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function myurlactivity_display_embed($url, $cm, $course, $config) {
    global $CFG, $PAGE, $OUTPUT;
    $mimetype = resourcelib_guess_url_mimetype($url->externalurl);
    $fullurl = myurlactivity_get_full_url($url, $cm, $course);
    $title = $url->name;
    $style = "width:$config->iframewidth".'px;'." height:$config->iframeheight".'px';
    $link = html_writer::tag('a', $fullurl, array('href' => str_replace('&amp;', '&', $fullurl)));
    $clicktoopen = get_string('clicktoopen', 'myurlactivity', $link);
    $moodleurl = new moodle_url($fullurl);
    $extension = resourcelib_get_extension($url->externalurl);
    $mediamanager = core_media_manager::instance($PAGE);
    $embedoptions = array(
        core_media_manager::OPTION_TRUSTED => true,
        core_media_manager::OPTION_BLOCK => true
    );
    if (in_array($mimetype, array('image/gif', 'image/jpeg', 'image/png'))) {  // It's an image.
        $code = resourcelib_embed_image($fullurl, $title);
    } else if ($mediamanager->can_embed_url($moodleurl, $embedoptions)) {
        // Media (audio/video) file.
        $code = $mediamanager->embed_url($moodleurl, $title, 0, 0, $embedoptions);
    } else {
        // Anything else - just try object tag enlarged as much as possible.
        $code = myurlactivity_iframe_general($fullurl, $title, $clicktoopen, $mimetype, $style);
    }
    myurlactivity_print_header($url, $cm, $course);
    myurlactivity_print_heading($url, $cm, $course);
    echo html_writer::start_tag('div');
    echo html_writer::start_tag('img', array('src' => $CFG->wwwroot.'/mod/myurlactivity/pix/loader.gif',
        'class' => 'loader_main_overlay', 'url' => $link));
    echo $code;
    echo html_writer::end_tag('div');
    $PAGE->requires->js_call_amd('mod_myurlactivity/main', 'loader');
    myurlactivity_print_intro($url, $cm, $course);

    echo $OUTPUT->footer();
    die;
}

/**
 * Decide the best display format.
 * @param object $url
 * @return int display type constant
 */
function myurlactivity_get_final_display_type($url) {
    global $CFG;
    if ($url->display != RESOURCELIB_DISPLAY_AUTO) {
        return $url->display;
    }
    static $download = array('application/zip', 'application/x-tar', 'application/g-zip', // Binary formats.
        'application/pdf', 'text/html');  // These are known to cause trouble for external links, sorry.
    static $embed = array('image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', // Images.
        'application/x-shockwave-flash', 'video/x-flv', 'video/x-ms-wm', // Video formats.
        'video/quicktime', 'video/mpeg', 'video/mp4',
        'audio/mp3', 'audio/x-realaudio-plugin', 'x-realaudio-plugin', // Audio formats.
    );

    $mimetype = resourcelib_guess_url_mimetype($url->externalurl);

    if (in_array($mimetype, $download)) {
        return ACTIVITYLIB_DISPLAY_FRAME;
    }
    if (in_array($mimetype, $embed)) {
        return ACTIVITYLIB_DISPLAY_EMBED;
    }

    // Let the browser deal with it somehow.
    return ACTIVITYLIB_DISPLAY_FRAME;
}

/**
 * Get the parameters that may be appended to URL
 * @param object $config url module config options
 * @return array array describing opt groups
 */
function myurlactivity_get_variable_options($config) {
    global $CFG;

    $options = array();
    $options[get_string('course')] = array(
        'courseid'        => 'id',
    );
    $options[get_string('user')] = array(
        'userid'          => 'id',
    );
    return $options;
}

/**
 * Get the parameter values that may be appended to URL
 * @param object $url module instance
 * @param object $cm
 * @param object $course
 * @param object $config module config options
 * @return array of parameter values
 */
function myurlactivity_get_variable_values($url, $cm, $course, $config) {
    global $USER, $CFG;

    $site = get_site();

    $coursecontext = context_course::instance($course->id);

    $values = array(
        'courseid' => $course->id,
        'coursefullname' => format_string($course->fullname),
        'courseshortname' => format_string($course->shortname, true, array('context' => $coursecontext)),
        'courseidnumber' => $course->idnumber,
        'coursesummary' => $course->summary,
        'courseformat' => $course->format,
        'lang' => current_language(),
        'sitename' => format_string($site->fullname),
        'serverurl' => $CFG->wwwroot,
        'currenttime' => time(),
        'urlinstance' => $url->id,
        'urlcmid' => $cm->id,
        'urlname' => format_string($url->name),
        'urlidnumber' => $cm->idnumber,
    );

    if (isloggedin()) {
        $values['userid'] = $USER->id;
        $values['userusername'] = $USER->username;
        $values['useridnumber'] = $USER->idnumber;
        $values['userfirstname'] = $USER->firstname;
        $values['userlastname'] = $USER->lastname;
        $values['userfullname'] = fullname($USER);
        $values['useremail'] = $USER->email;
        $values['usericq'] = $USER->icq;
        $values['userphone1'] = $USER->phone1;
        $values['userphone2'] = $USER->phone2;
        $values['userinstitution'] = $USER->institution;
        $values['userdepartment'] = $USER->department;
        $values['useraddress'] = $USER->address;
        $values['usercity'] = $USER->city;
        $now = new DateTime('now', core_date::get_user_timezone_object());
        $values['usertimezone'] = $now->getOffset() / 3600.0; // Value in hours for BC.
        $values['userurl'] = $USER->url;
    }

    // Weak imitation of Single-Sign-On, for backwards compatibility only
    // NOTE: login hack is not included in 2.0 any more, new contrib auth plugin
    //       needs to be createed if somebody needs the old functionality!
    if (!empty($config->secretphrase)) {
        $values['encryptedcode'] = myurlactivity_get_encrypted_parameter($url, $config);
    }

    // This is pretty fragile and slow, why do we need it here??.
    if ($config->rolesinparams) {
        $coursecontext = context_course::instance($course->id);
        $roles = role_fix_names(get_all_roles($coursecontext), $coursecontext, ROLENAME_ALIAS);
        foreach ($roles as $role) {
            $values['course' . $role->shortname] = $role->localname;
        }
    }

    return $values;
}
function myurlactivity_appears_valid_url($url) {
    if (preg_match('/^(\/|https?:|ftp:)/i', $url)) {
        // note: this is not exact validation, we look for severely malformed URLs only
        return (bool)preg_match('/^[a-z]+:\/\/([^:@\s]+:[^@\s]+@)?[a-z0-9_\.\-]+(:[0-9]+)?(\/[^#]*)?(#.*)?$/i', $url);
    } else {
        return (bool)preg_match('/^[a-z]+:\/\/...*$/i', $url);
    }
}

function myurlactivity_print_workaround($url, $cm, $course) {
    global $OUTPUT;
    myurlactivity_print_header($url, $cm, $course);
    myurlactivity_print_heading($url, $cm, $course, true);
    myurlactivity_print_intro($url, $cm, $course, true);

    $fullurl = myurlactivity_get_full_url($url, $cm, $course);

    $display = myurlactivity_get_final_display_type($url);
    if ($display == ACTIVITYLIB_DISPLAY_POPUP) {
        $jsfullurl = addslashes_js($fullurl);
        $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $extra = "onclick=\"window.open('$jsfullurl', '', '$wh'); return false;\"";

    } else {
        $extra = '';
    }

    echo '<div class="urlworkaround">';
    print_string('clicktoopen', 'url', "<a href=\"$fullurl\" $extra>$fullurl</a>");
    echo '</div>';

    echo $OUTPUT->footer();
    die;
}

function myurlactivity_get_coursemodule_from_id($modulename, $cmid, $courseid=0, $sectionnum=false, $strictness=IGNORE_MISSING) {
    global $DB;

    $params = array('cmid'=>$cmid);

    if (!$modulename) {
        if (!$modulename = $DB->get_field_sql("SELECT md.name
                                                 FROM {modules} md
                                                 JOIN {course_modules} cm ON cm.module = md.id
                                                WHERE cm.id = :cmid", $params, $strictness)) {
            return false;
        }
    } else {
        if (!core_component::is_valid_plugin_name('mod', $modulename)) {
            throw new coding_exception('Invalid modulename parameter');
        }
    }

    $params['modulename'] = $modulename;

    $courseselect = "";
    $sectionfield = "";
    $sectionjoin  = "";

    if ($courseid) {
        $courseselect = "AND cm.course = :courseid";
        $params['courseid'] = $courseid;
    }

    if ($sectionnum) {
        $sectionfield = ", cw.section AS sectionnum";
        $sectionjoin  = "LEFT JOIN {course_sections} cw ON cw.id = cm.section";
    }

    $sql = "SELECT cm.*, m.name, md.name AS modname $sectionfield
              FROM {course_modules} cm
                   JOIN {modules} md ON md.id = cm.module
                   JOIN {".$modulename."} m ON m.id = cm.instance
                   $sectionjoin
             WHERE cm.id = :cmid AND md.name = :modulename
                   $courseselect";

    return $DB->get_record_sql($sql, $params, $strictness);
}
