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
 * myurlactivity module admin settings and defaults
 *
 * @author     <ballisticlearning.com>
 * @package    mod_myurlactivity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->dirroot/mod/myurlactivity/lib.php");
    $displayoptions = activitylib_get_displayoptions(array(ACTIVITYLIB_DISPLAY_FRAME,
                                                           ACTIVITYLIB_DISPLAY_EMBED,
                                                           ACTIVITYLIB_DISPLAY_POPUP,));
    $defaultdisplayoptions = array(ACTIVITYLIB_DISPLAY_FRAME, ACTIVITYLIB_DISPLAY_EMBED, ACTIVITYLIB_DISPLAY_POPUP);

    // General settings. -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configtext('myurlactivity/framesize',
        get_string('framesize', 'myurlactivity'), get_string('configframesize', 'myurlactivity'), 380, PARAM_INT));
    $settings->add(new admin_setting_configpasswordunmask('myurlactivity/secretphrase', get_string('password'),
        get_string('configsecretphrase', 'myurlactivity'), ''));
    $settings->add(new admin_setting_configcheckbox('myurlactivity/rolesinparams',
        get_string('rolesinparams', 'myurlactivity'), get_string('configrolesinparams', 'myurlactivity'), false));
    $settings->add(new admin_setting_configmultiselect('myurlactivity/displayoptions',
        get_string('displayoptions', 'myurlactivity'), get_string('configdisplayoptions', 'myurlactivity'),
        $defaultdisplayoptions, $displayoptions));

    //--- Modedit defaults. -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('urlmodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('myurlactivity/printintro',
        get_string('printintro', 'myurlactivity'), get_string('printintroexplain', 'myurlactivity'), 1));
    $settings->add(new admin_setting_configselect('myurlactivity/display',
        get_string('displayselect', 'myurlactivity'), get_string('displayselectexplain', 'myurlactivity'), ACTIVITYLIB_DISPLAY_EMBED, $displayoptions));
    $settings->add(new admin_setting_configtext('myurlactivity/iframewidth',
        get_string('iframewidth', 'myurlactivity'), get_string('framewidthexplain', 'myurlactivity'), 750, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('myurlactivity/iframeheight',
        get_string('iframeheight', 'myurlactivity'), get_string('frameheightexplain', 'myurlactivity'), 455, PARAM_INT, 7));
}
