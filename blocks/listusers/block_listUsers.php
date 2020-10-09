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
 * Form for editing listUsers block instances.
 *
 * @package   block_listusers
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.listUsers GNU GPL v3 or later
 */

class block_listusers extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_listusers');
    }

 
        function get_content() {
       global $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $out='';
        $users=$DB->get_records('user');
        foreach($users as $user){
            if($user->id==1||$user->id==2)continue;
            $out .= $user->firstname.' '.$user->lastname.'<br/>';
        }
        $this->content = new stdClass;
        $this->content->text=$out;
        $this->content->footer = 'this is footer';
        return $this->content;
    }

    // function specialization() {
    //     if (isset($this->config->title)) {
    //         $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
    //     } else {
    //         $this->title = get_string('newhtmlblock', 'block_html');
    //     }
    // }


    
}
