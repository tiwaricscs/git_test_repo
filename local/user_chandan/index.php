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
 * This is index file used for displaying the data.
 *
 * @package local_user_chandan
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once('../../config.php');
require_once('lib.php');
require_once('renderor.php');
require_once('form.php');

//page setup
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/user_chandan/index.php');
$title = get_string('pluginname', 'local_user_chandan');
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('standard');
$url = $CFG->wwwroot . '/local/user_chandan/registration_form.php';

//output is start
echo $OUTPUT->header();

//creating a link
echo html_writer::link($url, get_string('register_link', 'local_user_chandan'));

echo "  ";

//getting the renderer class in renderor.php file
$output = $PAGE->get_renderer('local_user_chandan');

$url_hit = $CFG->wwwroot . '/local/user_chandan/ajaxhandler.php';

//Load an AMD(Asynchronous Module Definition) module and eventually call its method.
//This function creates a minimal inline JS snippet that requires an AMD module and eventually calls a singlefunction 
//from the module with given arguments. If it is called multiple times, 
//it will be create multiplesnippets.
//Parameters:string $fullmodule The name of the AMD module to load, formatted as /. 
//string $func Optional function from the module to call, defaults to just loading the AMD module. 
//array $params The params to pass to the function (will be serialized into JSON).
$PAGE->requires->js_call_amd('local_user_chandan/search', 'search_func');

//echo html_writer::tag('input', '', ['class' => 'search_user', 'id' => $url_hit]);
echo html_writer::tag('div', '', ['class' => 'show_data']);
echo html_writer::start_div('previous_table', []);
echo $output->local_crud_table();
echo html_writer::end_div();
echo $OUTPUT->download_dataformat_selector(get_string('download', 'local_user_chandan'), 'download.php', 'dataformat');
echo $OUTPUT->footer();