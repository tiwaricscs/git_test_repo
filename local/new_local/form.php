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
 * @package local_new_local
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

use core_table\external\dynamic\get;

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
class registration extends moodleform{

    //Add elements to form
    public function definition() {
        global $CFG;
 
        $mform = $this->_form;                                                           // Don't forget the underscore! 

        $mform = $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        //name field
        $mform->addElement('text', 'name', get_string('name', 'local_new_local'));      // Add elements to your form
        $mform->setType('name', PARAM_TEXT);                                             //Set type of element  
        $mform->setDefault('name', '');                                                 //set the default value
        $mform->addRule('name', '', 'required', 'client');

        //mobile
        $mform->addElement('text', 'mobile', get_string('mobile', 'local_new_local'), 'maxlength = 10');
        $mform->setType('mobile', PARAM_NOTAGS);
        $mform->setDefault('mobile', '');
        $mform->addRule('mobile', '', 'required', 'client');

        //last modified
        $mform->addElement('hidden', 'last_modified');
        $mform->setType('last_modified', PARAM_INT);

        //adding the submit button
        $this->add_action_buttons();
    }
   
   
    //Custom validation should be added here
    function validation($data, $files) {

        global $DB;
        $id_get = trim($data['id']);
        $errorname = trim($data['name']);
        $errormobile = trim($data['mobile']);

        if (empty($errorname)) {
            $error['name'] = get_string('namefill', 'local_new_local');
        }

        if (empty($errormobile)) {
            $error['mobile'] = get_string('mobilefill', 'local_new_local');
        }
        if ($errormobile < 999999999) {
            $error['mobile'] = get_string('mobilelength', 'local_new_local');
        }

        return isset($error) ? $error : null;
    }
}