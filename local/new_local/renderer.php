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
 * @package local_new_local
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();
class local_new_local_renderer extends plugin_renderer_base
{
    function search_box($search_key)
    {
        global $CFG, $DB, $OUTPUT;
        $table = new html_table();
        $totalrecords = $DB->get_records('local_new_local', []);
        $countpage = count($totalrecords);
      //  $PAGE = optional_param('page', 0, PARAM_INT);
      //  $limit = get_string('limit', 'local_new_local');
        $pageurl = new moodle_url($CFG->wwwroot . '/local/new_local/index.php');
        //$showpage = $limit * $PAGE;
        $table->head = array(
            get_string('id', 'local_new_local'),
            get_string('name', 'local_new_local'),
            get_string('mobile', 'local_new_local'),
            get_string('last_modified', 'local_new_local'),
            get_string('edit', 'local_new_local'),
            get_string('delete', 'local_new_local')
        );
       // $userdata = list_all_data_ajax($showpage, $limit, $search_key);
       // $count_new_data = count($userdata);
        $userdata = $DB-> get_records('local_new_local');
        $id = 1;
        foreach ($userdata as $user) {
          //  $gender = '';
            $url_update = new moodle_url($CFG->wwwroot . '/local/new_local/regis_form.php', array('id' => $user->id, 'action' => 'edit'));
            $url_delete = new moodle_url($CFG->wwwroot . '/local/new_local/regis_form.php', array('id' => $user->id, 'action' => 'delete'));
            $table->data[] = array(
                get_string('id', 'local_new_local') => $id,
                get_string('name', 'local_new_local') => $user->name,
                get_string('mobile', 'local_new_local') => $user->mobile,
                get_string('last_modified', 'local_new_local') => userdate($user->last_modified),
                get_string('edit', 'local_new_local') => html_writer::link($url_update, 'Update'),
                get_string('delete', 'local_new_local') => html_writer::link($url_delete, 'Delete')
            );
            $id++;
        }
        $newoutput = html_writer::table($table);
        // if ($count_new_data >= $limit - 1) {
        //     $newoutput .= $OUTPUT->paging_bar($countpage, $PAGE, $limit, $pageurl);
        // }
        echo $newoutput;
    }
   
    public function local_crud_table()
    {
        global $CFG, $DB, $OUTPUT;
        $table = new html_table();
        $totalrecords = $DB->get_records('local_new_local', []);
        $countpage = count($totalrecords);
        $PAGE = optional_param('page', 0, PARAM_INT);
       // $limit = get_string('limit', 'local_new_local');
        $pageurl = new moodle_url($CFG->wwwroot . '/local/user_new_local/index.php');
       // $showpage = $limit * $PAGE;
        $table->id = 'table_data';
        $table->head = array(
            get_string('id', 'local_new_local'),
            get_string('name', 'local_new_local'),
            get_string('mobile', 'local_new_local'),
            get_string('last_modified', 'local_new_local'),
            get_string('edit', 'local_new_local'),
            get_string('delete', 'local_new_local')
        );
        //$userdata = list_all_data($showpage, $limit);
        $userdata = $DB-> get_records('local_new_local');
        $id = 1;
        foreach ($userdata as $user) {
            
            $url_update = new moodle_url($CFG->wwwroot . '/local/new_local/regis_form.php', array('id' => $user->id, 'action' => 'edit'));
            $url_delete = new moodle_url($CFG->wwwroot . '/local/new_local/regis_form.php', array('id' => $user->id, 'action' => 'delete'));
            $table->data[] = array(
                get_string('id', 'local_new_local') => $id,
                get_string('name', 'local_new_local') => $user->name,
                get_string('mobile', 'local_new_local') => $user->mobile,
                get_string('last_modified', 'local_new_local') => userdate($user->last_modified),
                get_string('edit', 'local_new_local') => html_writer::link($url_update, 'Update'),
                get_string('delete', 'local_new_local') => html_writer::link($url_delete, 'Delete')
            );
            $id++;
        }
        $old = html_writer::table($table);
       // $old .= $OUTPUT->paging_bar($countpage, $PAGE, $limit, $pageurl);
        echo $old;
    }
}

