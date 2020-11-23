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
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
class mod_calculator_mod_form extends moodleform_mod  {

    function definition() {
            
            $mform =& $this->_form;

//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('newmodulename', 'calculator'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

    /// Adding the required "intro" field to hold the description of the instance

        $mform->addElement('editor', 'introeditor', 'intro' , array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
           'noclean' => true, 'subdirs' => true));
        $mform->setType('introeditor', PARAM_RAW);
       

        $mform->addElement('header', 'newmodulefieldset', get_string('newmodulefieldset', 'calculator'));
        
        $select = $mform->addElement('select', 'oprator', get_string('oprator', 'calculator'), array('none', 'add', 'subtract', 'divide', 'multiply'));
        
//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
       $this->standard_coursemodule_elements();
    
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

    }
}

?>
