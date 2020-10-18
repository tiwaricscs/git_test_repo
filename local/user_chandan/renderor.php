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
 * This is renderer file used to define and call the function.
 *
 * @package local_user_chandan
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();
class local_user_chandan_renderer extends plugin_renderer_base
{
    function search_box($search_key)
    {   
        
        //accessing the global
        global $CFG, $DB, $OUTPUT;
        
        //creating a new table object
        $table = new html_table();
        
        //getting the records
        $totalrecords = $DB->get_records('local_sr_registration', []);
        $countpage = count($totalrecords);
        $PAGE = optional_param('page', 0, PARAM_INT);
        $limit = get_string('limit', 'local_user_chandan');
        $pageurl = new moodle_url($CFG->wwwroot . '/local/user_chandan/index.php');
        $showpage = $limit * $PAGE;
        
        //setting the table headings
        $table->head = array(
            get_string('id', 'local_user_chandan'),
            get_string('name', 'local_user_chandan'),
            get_string('registration_number', 'local_user_chandan'),
            get_string('email', 'local_user_chandan'),
            get_string('gender', 'local_user_chandan'),
            get_string('mobile', 'local_user_chandan'),
            get_string('last_modified', 'local_user_chandan'),
            get_string('edit', 'local_user_chandan'),
            get_string('delete', 'local_user_chandan')
        );
        $userdata = list_all_data_with_ajax($showpage, $limit, $search_key);
        $count_new_data = count($userdata);
        $id = 1;
        
        //putting data into table
        foreach ($userdata as $user) {
            $gender = '';
            $url_update = new moodle_url($CFG->wwwroot . '/local/user_chandan/registration_form.php', array('id' => $user->id, 'action' => 'edit'));
            $url_delete = new moodle_url($CFG->wwwroot . '/local/user_chandan/registration_form.php', array('id' => $user->id, 'action' => 'delete'));
            $table->data[] = array(
                get_string('id', 'local_user_chandan') => $id,
                get_string('name', 'local_user_chandan') => $user->name,
                get_string('registration_number', 'local_user_chandan') => $user->registration_number,
                get_string('email', 'local_user_chandan') => $user->email,
                get_string('gender', 'local_user_chandan') => $gender = $user->gender == 1 ? 'Male' : 'Female',
                get_string('mobile', 'local_user_chandan') => $user->mobile,
                get_string('last_modified', 'local_user_chandan') => userdate($user->last_modified),
                get_string('edit', 'local_user_chandan') => html_writer::link($url_update, 'Update'),
                get_string('delete', 'local_user_chandan') => html_writer::link($url_delete, 'Delete')
            );
            $id++;
        }
        $newoutput = html_writer::table($table);
        if ($count_new_data >= $limit - 1) {
            $newoutput .= $OUTPUT->paging_bar($countpage, $PAGE, $limit, $pageurl);
        }
        echo $newoutput;
    }
    public function local_crud_table()
    {
        global $CFG, $DB, $OUTPUT;
        $table = new html_table();
        $totalrecords = $DB->get_records('local_sr_registration', []);
        $countpage = count($totalrecords);
        $PAGE = optional_param('page', 0, PARAM_INT);
        $limit = get_string('limit', 'local_user_chandan');
        $pageurl = new moodle_url($CFG->wwwroot . '/local/user_chandan/index.php');
        $showpage = $limit * $PAGE;
        $table->id = 'table_data';
        $table->head = array(
            get_string('id', 'local_user_chandan'),
            get_string('name', 'local_user_chandan'),
            get_string('registration_number', 'local_user_chandan'),
            get_string('email', 'local_user_chandan'),
            get_string('gender', 'local_user_chandan'),
            get_string('mobile', 'local_user_chandan'),
            get_string('last_modified', 'local_user_chandan'),
            get_string('edit', 'local_user_chandan'),
            get_string('delete', 'local_user_chandan')
        );
        $userdata = list_all_data($showpage, $limit);
        $id = 1;
        foreach ($userdata as $user) {
            $gender = '';
            $url_update = new moodle_url($CFG->wwwroot . '/local/user_chandan/registration_form.php', array('id' => $user->id, 'action' => 'edit'));
            $url_delete = new moodle_url($CFG->wwwroot . '/local/user_chandan/registration_form.php', array('id' => $user->id, 'action' => 'delete'));
            $table->data[] = array(
                get_string('id', 'local_user_chandan') => $id,
                get_string('name', 'local_user_chandan') => $user->name,
                get_string('registration_number', 'local_user_chandan') => $user->registration_number,
                get_string('email', 'local_user_chandan') => $user->email,
                get_string('gender', 'local_user_chandan') => $gender = $user->gender == 1 ? 'Male' : 'Female',
                get_string('mobile', 'local_user_chandan') => $user->mobile,
                get_string('last_modified', 'local_user_chandan') => userdate($user->last_modified),
                get_string('edit', 'local_user_chandan') => html_writer::link($url_update, 'Update'),
                get_string('delete', 'local_user_chandan') => html_writer::link($url_delete, 'Delete')
            );
            $id++;
        }
        //renders html table
        $old = html_writer::table($table);
        $old .= $OUTPUT->paging_bar($countpage, $PAGE, $limit, $pageurl);
        echo $old;
    }
}