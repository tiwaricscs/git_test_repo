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

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_AUTO,
        RESOURCELIB_DISPLAY_EMBED,
        RESOURCELIB_DISPLAY_FRAME,
    ));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_AUTO,
        RESOURCELIB_DISPLAY_EMBED,
    );
     $settings->add(new admin_setting_configtext('mod_harsh/framesize',
                    get_string('framesize', 'harsh'), get_string('configframesize', 'harsh'), 130, PARAM_INT));
    $settings->add(new admin_setting_configpasswordunmask('mod_harsh/secretphrase', get_string('password'),
                    get_string('configsecretphrase', 'harsh'), ''));
    $settings->add(new admin_setting_configcheckbox('mod_harsh/rolesinparams',
                    get_string('rolesinparams', 'harsh'), get_string('configrolesinparams', 'harsh'), false));
    $settings->add(new admin_setting_configmultiselect('mod_harsh/displayoptions',
                    get_string('displayoptions', 'harsh'), get_string('configdisplayoptions', 'harsh'),
                    $defaultdisplayoptions, $displayoptions));
      $settings->add(new admin_setting_heading('urlmodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));
    $settings->add(new admin_setting_configcheckbox('mod_harsh/printintro',
                    get_string('printintro', 'harsh'), get_string('printintroexplain', 'harsh'), 1));
    $settings->add(new admin_setting_configselect('mod_harsh/display',
                    get_string('displayselect', 'harsh'), get_string('displayselectexplain', 'harsh'), RESOURCELIB_DISPLAY_AUTO, $displayoptions));
    $settings->add(new admin_setting_configtext('mod_harsh/iframewidth',
                    get_string('iframewidth', 'harsh'), get_string('iframewidthexplain', 'harsh'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('mod_harsh/iframeheight',
                    get_string('iframeheight', 'harsh'), get_string('iframeheightexplain', 'harsh'), 450, PARAM_INT, 7));
}
