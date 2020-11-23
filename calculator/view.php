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
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('lib.php');
require_once("locallib.php");
require_once('submitform.php');
require_once('result_form.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$calinstance_id  = optional_param('a', 0, PARAM_INT);  // calculator instance ID
$redirect = optional_param('redirect', 0, PARAM_BOOL);
$forceview = optional_param('forceview', 0, PARAM_BOOL);

 
global  $DB;

 if ($id) {
    if (! $cm = get_coursemodule_from_id('calculator', $id)) {
        print_error(get_string('cmderor', 'calculator'));
    }

    if (! $course = $DB->get_record('course', array('id'=> $cm->course))) {
       print_error(get_string('cmsf', 'calculator'));
    }


}else if ($calinstance_id ) {  // Two ways to specify the module
    $calculator = $DB->get_record('calculator', array('id'=>$calinstance_id), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('calculator', $calculator->id, $calculator->course, false, MUST_EXIST);

}else {
    $cm = get_coursemodule_from_id('calculator', $id, 0, false, MUST_EXIST);
    $calculator = $DB->get_record('calculator', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/calculator:view', $context);
$PAGE->set_url('/mod/calculator/view.php', array('id' => $cm->id));
$PAGE->set_context($context);
$title = get_string('pluginname', 'calculator');
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('standard');


//object creation of our form
$mform = new submit_form('', array('id' => $id, 'cid'=>$cm->instance));


 if ($fromform = (array)$mform->get_data()) {                              //getting the submitted form data
        //  print_object($fromform['opratorid']);die;
   switch($fromform['opratorid']){

            case "0":
            $result = 'you need to select a oprator';
            break;

            case "1":
            $result = add($fromform['num1'], $fromform['num2']);
            break;

            case "2":
            $result = subtract($fromform['num1'], $fromform['num2']);
            break;

            case "4":
            $result = multiply($fromform['num1'], $fromform['num2']);
            break;

            case "3":
            $result =  divide($fromform['num1'], $fromform['num2']);
            break;
        }
        $toform = array('id' => $id, 'cid'=>$cm->instance);
        $mform->set_data($toform);
        $resultform = new result_form('', array('resultid' => $result));
}else {  
        $toform = array('id' => $id, 'cid'=>$cm->instance);
        $mform->set_data($toform);
        $resultform = new result_form('', array('resultid' => ''));
}
echo $OUTPUT->header();
echo html_writer::empty_tag('br');
$mform->display();
$resultform->display();
echo $OUTPUT->footer();



?>
