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
 * @package    mod_calculator
 * @author     <Ballisticlearning.com> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('classes/event/calculator_created.php');

function calculator_add_instance($calculator) {
    global $DB,$USER;
    $calculator->timecreated = time();
    $calculator->timemodified = time();
    $insert = $DB->insert_record('calculator', $calculator);
     if ($insert){
        $context=context_system::instance();
        $event = \mod_calculator\event\calculator_created::create(array('context' => $context, 'objectid' => $USER->id, 'other' => 'This is add '));
        $event->trigger();
        return $insert;
    }
}


function calculator_update_instance($calculator) {
    global $DB;
    $calculator->timemodified = time();
    $calculator->id = $calculator->instance;
    return $DB->update_record('calculator', $calculator);
}


function calculator_delete_instance($id) {
    global  $DB;
    if (! $calculator = $DB->get_record('calculator', array('id'=>$id))) {
        return false;
    }
    $result = true;
    if (! $DB->delete_records('calculator', array('id'=>$calculator->id))) {
        $result = false;
    }
    return $result;
}

?>