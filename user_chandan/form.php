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
 * This is form file used for designing the structure of the form.
 * 
 * @package local_user_chandan
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

use core_table\external\dynamic\get;

require_once("$CFG->libdir/formslib.php");
class registration extends moodleform
{
    //Add elements to form
    public function definition()
    {
        $mform = $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        //name
        $mform->addElement('text', 'name', get_string('name', 'local_user_chandan'));
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', '');
        $mform->addRule('name', '', 'required', 'client');
        //Registration Number 
        $mform->addElement('text', 'registration_number', get_string('registration_number', 'local_user_chandan'));
        $mform->setType('registration_number', PARAM_TEXT);
        $mform->setDefault('registration_number', '');
        $mform->addRule('registration_number', '', 'required', 'client');
        //Email
        $mform->addElement('text', 'email', get_string('email', 'local_user_chandan'));
        $mform->setType('email', PARAM_NOTAGS);
        $mform->setDefault('email', '');
        $mform->addRule('email', '', 'required', 'client');
        //Gender
        $radioarray = array();
        $radioarray[] = $mform->createElement('radio', 'gender', '', get_string('male', 'local_user_chandan'), 1);
        $radioarray[] = $mform->createElement('radio', 'gender', '', get_string('female', 'local_user_chandan'), 0);
        $mform->addGroup($radioarray, 'gender', get_string('gender', 'local_user_chandan'), array(''), false);
        $mform->addRule('gender', get_string('gendererr', 'local_user_chandan'), 'required');
        //Mobile
        $mform->addElement('text', 'mobile', get_string('mobile', 'local_user_chandan'), 'maxlength = 10');
        $mform->setType('mobile', PARAM_NOTAGS);
        $mform->setDefault('mobile', '');
        $mform->addRule('mobile', '', 'required', 'client');
        //to get current time
        $mform->addElement('hidden', 'last_modified');
        $mform->setType('last_modified', PARAM_INT);
        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files)
    {
        global $DB;
        $id_get = trim($data['id']);
        $errorname = trim($data['name']);
        $erroremail = trim($data['email']);
        $errorregistration = trim($data['registration_number']);
        $errormobile = trim($data['mobile']);
        if (empty($errorname)) {
            $error['name'] = get_string('namefill', 'local_user_chandan');
        }
        if (empty($errorregistration)) {
            $error['registration_number'] = get_string('registrationfill', 'local_user_chandan');
        }
        if ($errorregistration < 99999) {
            $error['registration_number'] = get_string('registrationlength', 'local_user_chandan');
        }
        if ($id_get == 0) {
            $unique = $DB->record_exists(get_string('registration', 'local_user_chandan'), ['registration_number' => $data['registration_number']]);
            if ($unique > 0) {
                $error['registration_number'] = get_string('registrationunique', 'local_user_chandan');
            }
            $uniqueemail = $DB->record_exists(get_string('registration', 'local_user_chandan'), ['email' => $data['email']]);
            if ($uniqueemail > 0) {
                $error['email'] = get_string('emailunique', 'local_user_chandan');
            }
        }
        if ($id_get) {
            $user_id = $DB->get_record(get_string('registration', 'local_user_chandan'), ['registration_number' => $data['registration_number']], 'id');
            if ($user_id) {
                $id = $user_id->id;
                if ($id != $id_get) {
                    $error['registration_number'] = get_string('registrationunique', 'local_user_chandan');
                }
            }
            $user_email = $DB->get_record(get_string('registration', 'local_user_chandan'), ['email' => $data['email']], 'id');
            if ($user_email) {
                $uid = $user_email->id;
                if ($uid != $id_get) {
                    $error['email'] = get_string('emailunique', 'local_user_chandan');
                }
            }
        }
        if (empty($erroremail)) {
            $error['email'] = get_string('emailfill', 'local_user_chandan');
        }
        if (!filter_var($erroremail, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = get_string('emailformat', 'local_user_chandan');
        }
        if (empty($errormobile)) {
            $error['mobile'] = get_string('mobilefill', 'local_user_chandan');
        }
        if ($errormobile < 999999999) {
            $error['mobile'] = get_string('mobilelength', 'local_user_chandan');
        }
        return isset($error) ? $error : null;
    }
}

