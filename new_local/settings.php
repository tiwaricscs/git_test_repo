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
 * Thesis settings and defaults
 *
 * @package    local_new_local
 * @auther     chandan tiwari
 */
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_new_local', new lang_string('pluginname', 'local_new_local')));
    $settingspage = new admin_settingpage('managepage', new lang_string('manage', 'local_new_local'));
    if ($ADMIN->fulltree) {

        $viewrecord = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        $setting = new admin_setting_configselect('local_new_local/showinnavigation', new lang_string('showinnavigation', 'local_new_local'), new lang_string('showinnavigation_desc', 'local_new_local'), get_string('perpage', 'local_new_local'), $viewrecord);


        $settingspage->add($setting);
    }
    $ADMIN->add('localplugins', $settingspage);
}