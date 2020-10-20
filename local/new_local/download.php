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
 * This is download.php file which is used for downloading different type of data format.
 * 
 * @package local_new_local
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once('../../config.php');
require_once($CFG->libdir . '/dataformatlib.php');
global $CFG, $DB;
$file_name = clean_filename('my_file');
$format = optional_param('dataformat', 'pdf', PARAM_ALPHA);
$columns = array(
    'id' => get_string('id', 'local_new_local'),
    'name' => get_string('name', 'local_new_local'),
    'mobile' => get_string('mobile', 'local_new_local'),
    'last_modified' => get_string('last_modified', 'local_new_local'),
);
$fields = 'id,name,mobile,last_modified';
$rs = $DB->get_records('local_new_local', [], '', $fields);
$downloadusers = new ArrayObject($rs);
$iterator = $downloadusers->getIterator();
//\core\dataformat::download_data($file_name,$format,$columns,$iterator);
download_as_dataformat($file_name, $format, $columns, $iterator);

