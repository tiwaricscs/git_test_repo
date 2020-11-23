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
require_once('../../config.php');
require_once('view.php');
require_once("$CFG->libdir/formslib.php");

class submit_form extends moodleform {

    function definition() {
        global $DB;
       
        $mform = $this->_form;
        $cid = $this->_customdata['cid'];
        $symbol=array('none', 'add', 'subtract', 'divide', 'multiply');
        $oprator=$DB->get_record('calculator', array('id'=>$cid));
        $op=$symbol[$oprator->oprator];
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'opratorid', $oprator->oprator);
        $mform->setType('opratorid', PARAM_INT);
        $mform->addElement('header', 'general', get_string('calculator', 'calculator'));
        $mform->addElement('text', 'num1', get_string('numone', 'calculator'), array('size'=>'10'));
        $mform->setType('num1', PARAM_INT);
      //  $mform->addRule('num1', null, 'required', null, 'client');
        $mform->addElement('static', 'oprator', '' , $op);
        $mform->addElement('text', 'num2', get_string('numtwo', 'calculator'), array('size'=>'10'));
        $mform->setType('num2', PARAM_INT);
       // $mform->addRule('num2', null, 'required', null, 'client');
         $this->add_action_buttons(false);
     }

     function validation($data, $files)
    {
        
        $ernum1 = trim($data['num1']);
        $ernum2 = trim($data['num2']);
        if (empty($ernum1)) {
            $error['num1'] = get_string('namefill', 'calculator');
        }
        if (empty($ernum2)) {
            $error['num2'] = get_string('namefill', 'calculator');
        }
        return isset($error) ? $error : null;
    }
}

?>
