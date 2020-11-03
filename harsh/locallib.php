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
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/harsh/lib.php");

function harsh_appears_valid_url($url) {
    if (preg_match('/^(\/|https?:|ftp:)/i', $url)) {
        // note: this is not exact validation, we look for severely malformed URLs only
        return (bool) preg_match('/^[a-z]+:\/\/([^:@\s]+:[^@\s]+@)?[^ @]+(:[0-9]+)?(\/[^#]*)?(#.*)?$/i', $url);
    } else {
        return (bool) preg_match('/^[a-z]+:\/\/...*$/i', $url);
    }
}
function harsh_fix_submitted_url($url) {
    $url = trim($url);
    $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
    if (!preg_match('|^[a-z]+:|i', $url) and!preg_match('|^/|', $url)) {
        $url = 'http://' . $url;
    }
    return $url;
}
function harsh_get_full_url($url, $cm, $course, $config = null) {
    $parameters = empty($url->parameters) ? array() : unserialize($url->parameters);
    $fullurl = html_entity_decode($url->externalurl, ENT_QUOTES, 'UTF-8');
    $letters = '\pL';
    $latin = 'a-zA-Z';
    $digits = '0-9';
    $symbols = '\x{20E3}\x{00AE}\x{00A9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}' .
            '\x{2300}-\x{23FF}\x{2600}-\x{27BF}\x{2B00}-\x{2BF0}';
    $arabic = '\x{FE00}-\x{FEFF}';
    $math = '\x{2190}-\x{21FF}\x{2900}-\x{297F}';
    $othernumbers = '\x{2460}-\x{24FF}';
    $geometric = '\x{25A0}-\x{25FF}';
    $emojis = '\x{1F000}-\x{1F6FF}';
    if (preg_match('/^(\/|https?:|ftp:)/i', $fullurl) or preg_match('|^/|', $fullurl)) {
        $allowed = "[" . $letters . $latin . $digits . $symbols . $arabic . $math . $othernumbers . $geometric .
                $emojis . "]" . preg_quote(';/?:@=&$_.+!*(),-#%', '/');
        $fullurl = preg_replace_callback("/[^$allowed]/u", 'harsh_filter_callback', $fullurl);
    } else {
        $fullurl = str_replace('"', '%22', $fullurl);
        $fullurl = str_replace('\'', '%27', $fullurl);
        $fullurl = str_replace(' ', '%20', $fullurl);
        $fullurl = str_replace('<', '%3C', $fullurl);
        $fullurl = str_replace('>', '%3E', $fullurl);
    }
    if (!empty($parameters)) {
        if (!$config) {
            $config = get_config('mod_harsh');
        }
        $paramvalues = harsh_get_variable_values($url, $cm, $course, $config);
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
    $fullurl = str_replace('&', '&amp;', $fullurl);
    return $fullurl;
}
function harsh_filter_callback($matches) {
    return rawurlencode($matches[0]);
}
function harsh_print_header($url, $cm, $course) {
    global $PAGE, $OUTPUT;
    $PAGE->set_title($course->shortname . ': ' . $url->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($url);
    echo $OUTPUT->header();
}
function harsh_print_heading($url, $cm, $course, $notused = false) {
    global $OUTPUT;
    echo $OUTPUT->heading(format_string($url->name), 2);
}
function harsh_print_intro($url, $cm, $course, $ignoresettings = false) {
    global $OUTPUT;
    $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
    if ($ignoresettings or!empty($options['printintro'])) {
        if (trim(strip_tags($url->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'urlintro');
            echo format_module_intro('harsh', $url, $cm->id);
            echo $OUTPUT->box_end();
        }
    }
}
function harsh_display_frame($url, $cm, $course) {
    global $PAGE, $OUTPUT, $CFG;
    $a = unserialize($url->displayoptions);
    $frame = optional_param('frameset', 'main', PARAM_ALPHA);
    if ($frame === 'top') {
        $PAGE->set_pagelayout('frametop');
        harsh_print_header($url, $cm, $course);
        harsh_print_heading($url, $cm, $course);
        harsh_print_intro($url, $cm, $course);
        echo $OUTPUT->footer();
        die;
    } else {
        $config = get_config('mod_harsh');
        $context = context_module::instance($cm->id);
        $exteurl = harsh_get_full_url($url, $cm, $course, $config);
        $navurl = "$CFG->wwwroot/mod/harsh/view.php?id=$cm->id&amp;frameset=top";
        $coursecontext = context_course::instance($course->id);
        $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
        $title = strip_tags($courseshortname . ': ' . format_string($url->name));
        $framesize = $config->framesize;
        if (empty($a['iframewidth']) || empty($a['iframeheight'])) {
            $store = get_config('mod_harsh');
            $width = $store->iframewidth;
            $height = $store->iframeheight;
        } else {
            $width = $a['iframewidth'];
            $height = $a['iframeheight'];
        }
        $modulename = s(get_string('modulename', 'harsh'));
        $contentframetitle = s(format_string($url->name));
        $dir = get_string('thisdirection', 'langconfig');
        harsh_print_header($url, $cm, $course);
        harsh_print_heading($url, $cm, $course);
        harsh_print_intro($url, $cm, $course);
        $img = $CFG->wwwroot . '/mod/harsh/pix/waiting.gif';
        echo $style = '<style> #load { background:url(' . $img . ' ) center center no-repeat; }</style>';
        echo "<iframe id='load' src=\"https://www.wikipedia.org/\" width=\"$width\" height=\"$height\"></iframe>";
        echo $OUTPUT->footer();
        die;
    }
}
function harsh_print_workaround($url, $cm, $course) {
    global $OUTPUT;
    harsh_print_header($url, $cm, $course);
    harsh_print_heading($url, $cm, $course, true);
    harsh_print_intro($url, $cm, $course, true);
    $fullurl = harsh_get_full_url($url, $cm, $course);
    $display = harsh_get_final_display_type($url);
    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $jsfullurl = addslashes_js($fullurl);
        $options = empty($url->displayoptions) ? array() : unserialize($url->displayoptions);
        $width = empty($options['popupwidth']) ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $extra = "onclick=\"window.open('$jsfullurl', '', '$wh'); return false;\"";
    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $extra = "onclick=\"this.target='_blank';\"";
    } else {
        $extra = '';
    }
    echo '<div class="urlworkaround">';
    print_string('clicktoopen', 'harsh', "<a href=\"$fullurl\" $extra>$fullurl</a>");
    echo '</div>';
    echo $OUTPUT->footer();
    die;
}
function harsh_display_embed($url, $cm, $course) {
    global $CFG, $PAGE, $OUTPUT;
    $mimetype = resourcelib_guess_url_mimetype($url->externalurl);
    $fullurl = harsh_get_full_url($url, $cm, $course);
    $title = $url->name;
    $link = html_writer::tag('a', $fullurl, array('href' => str_replace('&amp;', '&', $fullurl)));
    $clicktoopen = get_string('clicktoopen', 'harsh', $link);
    $moodleurl = new moodle_url($fullurl);
    $extension = resourcelib_get_extension($url->externalurl);
    $mediamanager = core_media_manager::instance($PAGE);
    $embedoptions = array(
        core_media_manager::OPTION_TRUSTED => true,
        core_media_manager::OPTION_BLOCK => true
    );
    if (in_array($mimetype, array('image/gif', 'image/jpeg', 'image/png'))) {
        $code = resourcelib_embed_image($fullurl, $title);
    } else if ($mediamanager->can_embed_url($moodleurl, $embedoptions)) {
        $code = $mediamanager->embed_url($moodleurl, $title, 0, 0, $embedoptions);
    } else {
        $code = resourcelib_embed_general($fullurl, $title, $clicktoopen, $mimetype);
    }
    harsh_print_header($url, $cm, $course);
    harsh_print_heading($url, $cm, $course);
    echo $code;
    harsh_print_intro($url, $cm, $course);
    echo $OUTPUT->footer();
    die;
}
function harsh_get_final_display_type($url) {
    global $CFG;
    if ($url->display != RESOURCELIB_DISPLAY_AUTO) {
        return $url->display;
    }
    if (strpos($url->externalurl, $CFG->wwwroot) === 0) {
        if (strpos($url->externalurl, 'file.php') === false and strpos($url->externalurl, '.php') !== false) {
            return RESOURCELIB_DISPLAY_OPEN;
        }
    }
    static $download = array('application/zip', 'application/x-tar', 'application/g-zip', // binary formats
        'application/pdf', 'text/html');  // these are known to cause trouble for external links, sorry
    static $embed = array('image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', // images
        'application/x-shockwave-flash', 'video/x-flv', 'video/x-ms-wm', // video formats
        'video/quicktime', 'video/mpeg', 'video/mp4',
        'audio/mp3', 'audio/x-realaudio-plugin', 'x-realaudio-plugin', // audio formats,
    );
    $mimetype = resourcelib_guess_url_mimetype($url->externalurl);
    if (in_array($mimetype, $download)) {
        return RESOURCELIB_DISPLAY_DOWNLOAD;
    }
    if (in_array($mimetype, $embed)) {
        return RESOURCELIB_DISPLAY_EMBED;
    }
    return RESOURCELIB_DISPLAY_OPEN;
}
function harsh_get_variable_options($config) {
    global $CFG;
    $options = array();
    $options[''] = array('' => get_string('chooseavariable', 'harsh'));
    $options[get_string('course')] = array(
        'courseid' => 'id',
        'coursefullname' => get_string('fullnamecourse'),
        'courseshortname' => get_string('shortnamecourse'),
        'courseidnumber' => get_string('idnumbercourse'),
        'coursesummary' => get_string('summary'),
        'courseformat' => get_string('format'),
    );
    $options[get_string('modulename', 'harsh')] = array(
        'urlinstance' => 'id',
        'urlcmid' => 'cmid',
        'urlname' => get_string('name'),
        'urlidnumber' => get_string('idnumbermod'),
    );
    $options[get_string('miscellaneous')] = array(
        'sitename' => get_string('fullsitename'),
        'serverurl' => get_string('serverurl', 'harsh'),
        'currenttime' => get_string('time'),
        'lang' => get_string('language'),
    );
    if (!empty($config->secretphrase)) {
        $options[get_string('miscellaneous')]['encryptedcode'] = get_string('encryptedcode');
    }
    $options[get_string('user')] = array(
        'userid' => 'id',
        'userusername' => get_string('username'),
        'useridnumber' => get_string('idnumber'),
        'userfirstname' => get_string('firstname'),
        'userlastname' => get_string('lastname'),
        'userfullname' => get_string('fullnameuser'),
        'useremail' => get_string('email'),
        'usericq' => get_string('icqnumber'),
        'userphone1' => get_string('phone1'),
        'userphone2' => get_string('phone2'),
        'userinstitution' => get_string('institution'),
        'userdepartment' => get_string('department'),
        'useraddress' => get_string('address'),
        'usercity' => get_string('city'),
        'usertimezone' => get_string('timezone'),
        'userurl' => get_string('webpage'),
    );
    if ($config->rolesinparams) {
        $roles = role_fix_names(get_all_roles());
        $roleoptions = array();
        foreach ($roles as $role) {
            $roleoptions['course' . $role->shortname] = get_string('yourwordforx', '', $role->localname);
        }
        $options[get_string('roles')] = $roleoptions;
    }
    return $options;
}
function harsh_get_variable_values($url, $cm, $course, $config) {
    global $USER, $CFG;
    $site = get_site();
    $coursecontext = context_course::instance($course->id);
    $values = array(
        'courseid' => $course->id,
        'coursefullname' => format_string($course->fullname, true, array('context' => $coursecontext)),
        'courseshortname' => format_string($course->shortname, true, array('context' => $coursecontext)),
        'courseidnumber' => $course->idnumber,
        'coursesummary' => $course->summary,
        'courseformat' => $course->format,
        'lang' => current_language(),
        'sitename' => format_string($site->fullname, true, array('context' => $coursecontext)),
        'serverurl' => $CFG->wwwroot,
        'currenttime' => time(),
        'urlinstance' => $url->id,
        'urlcmid' => $cm->id,
        'urlname' => format_string($url->name, true, array('context' => $coursecontext)),
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
    if (!empty($config->secretphrase)) {
        $values['encryptedcode'] = harsh_get_encrypted_parameter($url, $config);
    }
    if ($config->rolesinparams) {
        $coursecontext = context_course::instance($course->id);
        $roles = role_fix_names(get_all_roles($coursecontext), $coursecontext, ROLENAME_ALIAS);
        foreach ($roles as $role) {
            $values['course' . $role->shortname] = $role->localname;
        }
    }
    return $values;
}
function harsh_get_encrypted_parameter($url, $config) {
    global $CFG;
    if (file_exists("$CFG->dirroot/local/externserverfile.php")) {
        require_once("$CFG->dirroot/local/externserverfile.php");
        if (function_exists('extern_server_file')) {
            return extern_server_file($url, $config);
        }
    }
    return md5(getremoteaddr() . $config->secretphrase);
}
function harsh_guess_icon($fullurl, $size = null) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");
    if (substr_count($fullurl, '/') < 3 or substr($fullurl, -1) === '/') {
        return null;
    }
    $icon = file_extension_icon($fullurl, $size);
    $htmlicon = file_extension_icon('.htm', $size);
    $unknownicon = file_extension_icon('', $size);

    if ($icon === $unknownicon || $icon === $htmlicon) {
        return null;
    }
    return $icon;
}
