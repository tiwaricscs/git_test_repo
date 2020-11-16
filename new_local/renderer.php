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

class local_new_local_renderer extends plugin_renderer_base {

    public function local_crud_table($searchkey, $pepage) {

        global $CFG, $DB, $OUTPUT;
        $table = new html_table();
        $viewrecord = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

        $page = optional_param('page', 0, PARAM_INT);
        //  $perpage = optional_param('perpage', 4, PARAM_INT);
        $perpage = $viewrecord[$pepage];


        if (!empty($searchkey)) {
            $query = "SELECT * FROM {local_new_local} WHERE name LIKE '%$searchkey%'";
            $totalrecords = $DB->get_records_sql($query);
            $countpage = count($totalrecords);
            $start = $page * $perpage;
            if ($start > $countpage) {
                $page = 0;
                $start = 0;
            }
            if ($countpage == 0) {

                //showing notification
                // \core\notification::warning(get_string('dnf', 'local_new_local')); 
            }
            $userdata = $DB->get_records_sql($query, [], $start, $perpage);
        } else {
            $totalrecords = $DB->get_records('local_new_local');
            $countpage = count($totalrecords);
            $start = $page * $perpage;
            if ($start > $countpage) {
                $page = 0;
                $start = 0;
            }

            $userdata = $DB->get_records('local_new_local', [], '', '*', $start, $perpage);
        }

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
        if (!empty($searchkey)) {
            $pageurl = new moodle_url($CFG->wwwroot . '/local/new_local/index.php', ['searchkey' => $searchkey]);
        } else {
            $pageurl = new moodle_url($CFG->wwwroot . '/local/new_local/index.php');
        }
        $old .= $OUTPUT->paging_bar($countpage, $page, $perpage, $pageurl);
        echo $old;
    }

}
