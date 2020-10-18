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
 * This is registration_form which is used for designing the registration page.
 *
 * @package local_user_chandan
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once('../../config.php');
require_once('lib.php');
require_once('form.php');

//page setup
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/user_chandan/registration_form.php');
$title = get_string('pluginname', 'local_user_chandan');
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('standard');
$url = $CFG->wwwroot . '/local/user_chandan/index.php';

$id_act = optional_param('id', 0, PARAM_INT);
$action_perform = optional_param('action', null, PARAM_TEXT);

//object creation of our form
$mform = new registration();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {                                             //check if the cancle button is press
    redirect($url, get_string('pluginname', 'local_user_chandan'));
} else if ($fromform = $mform->get_data()) {                              //getting the submitted form data
    $now = time();
    $fromform->last_modified = $now;
    if ($fromform->id > 0) {
        $id = $DB->update_record('local_sr_registration', $fromform);
        redirect($url, get_string('updated', 'local_user_chandan'));
    } else {
        $id = $DB->insert_record('local_sr_registration', $fromform);
        redirect($url, get_string('added', 'local_user_chandan'));
    }
} else {
    if ($action_perform == 'delete') {
        $delete = $DB->delete_records('local_sr_registration', ['id' => $id_act]);
        redirect($url, get_string('deleted', 'local_user_chandan'));
    } else {
        $toform =  $DB->get_record('local_sr_registration', array('id' => $id_act));;
        $mform->set_data($toform);
    }
}
echo $OUTPUT->header();
echo html_writer::link($url, get_string('displayall', 'local_user_chandan'));
echo html_writer::empty_tag('br');
$mform->display();
echo $OUTPUT->footer();