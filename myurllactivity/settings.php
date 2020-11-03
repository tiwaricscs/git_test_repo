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

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_AUTO,
        RESOURCELIB_DISPLAY_EMBED,
        RESOURCELIB_DISPLAY_FRAME,
    ));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_AUTO,
        RESOURCELIB_DISPLAY_EMBED,
    );
     $settings->add(new admin_setting_configtext('mod_myurllactivity/framesize',
                    get_string('framesize', 'myurllactivity'), get_string('configframesize', 'myurllactivity'), 130, PARAM_INT));
    $settings->add(new admin_setting_configpasswordunmask('mod_myurllactivity/secretphrase', get_string('password'),
                    get_string('configsecretphrase', 'myurllactivity'), ''));
    $settings->add(new admin_setting_configcheckbox('mod_myurllactivity/rolesinparams',
                    get_string('rolesinparams', 'myurllactivity'), get_string('configrolesinparams', 'myurllactivity'), false));
    $settings->add(new admin_setting_configmultiselect('mod_myurllactivity/displayoptions',
                    get_string('displayoptions', 'myurllactivity'), get_string('configdisplayoptions', 'myurllactivity'),
                    $defaultdisplayoptions, $displayoptions));
      $settings->add(new admin_setting_heading('urlmodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));
    $settings->add(new admin_setting_configcheckbox('mod_myurllactivity/printintro',
                    get_string('printintro', 'myurllactivity'), get_string('printintroexplain', 'myurllactivity'), 1));
    $settings->add(new admin_setting_configselect('mod_myurllactivity/display',
                    get_string('displayselect', 'myurllactivity'), get_string('displayselectexplain', 'myurllactivity'), RESOURCELIB_DISPLAY_AUTO, $displayoptions));
    $settings->add(new admin_setting_configtext('mod_myurllactivity/iframewidth',
                    get_string('iframewidth', 'myurllactivity'), get_string('iframewidthexplain', 'myurllactivity'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('mod_myurllactivity/iframeheight',
                    get_string('iframeheight', 'myurllactivity'), get_string('iframeheightexplain', 'myurllactivity'), 450, PARAM_INT, 7));
}
