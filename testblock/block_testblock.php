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
 * Form for editing HTML block instances.
 *
 * @package   block_testblock
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
//require_once(__DIR__ . '/../../../config.php');


class block_testblock extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_testblock');
    }

    

    function get_content() {
     
        global $DB;
        $sql=$DB->get_records("user", [], '', '*', $limitfrom=0, $limitnum=0);
//         "SELECT id, username FROM {user}";
//        $users=$DB->get_records_sql($sql, null);
//       
     //  print_object($sql);        die();
        if ($this->content !== NULL) {
            return $this->content;
        }

      
        $this->content = new stdClass;
       // $this->content->text = 'this is the text'.'<br/>';
        $this->content->text =  'the list of the users<'.'<br/>';  
       //  $users=(array)$sql;
         
        
     //    echo '<pre>';
      //print_r($users);  echo '<pre>';die();
       
         foreach ($sql as $user){
              
             //$u=(array) $user;
               
//                echo '<pre>';
//        print_r($u);  echo '<pre>';die();
              //$this->content->text = $u['id'].' ';
             $this->content->text .=  $user->id.' ';
             $this->content->text .=  $user->username.'<br>';
             
         
              }
        
        if (! empty($this->config->text)) {
         $this->content->text .= $this->config->text;
        }
       
        //$this->content->footer = 'this is the footer';
        
        return $this->content;
    }
    
    
    public function specialization() {
    if (isset($this->config)) {
        if (empty($this->config->title)) {
            $this->title = get_string('blocktitle', 'block_testblock');            
        } else {
            $this->title = $this->config->title;
        }
 
        if (empty($this->config->text)) {
            $this->config->text = get_string('defaulttext', 'block_testblock');
        }    
    }
}


}
