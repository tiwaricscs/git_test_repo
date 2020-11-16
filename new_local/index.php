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
 * @package local_new_local
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once('../../config.php');

require_once('form.php');

//setting the page context
$context = context_system::instance();
$PAGE->set_context($context);

//setting the page url
$PAGE->set_url('/local/new_local/index.php');

//setting the page title and heading
$title = get_string('pluginname', 'local_new_local');
$PAGE->set_heading($title);
$PAGE->set_title($title);

//getting the setting data
$confdata = get_config('local_new_local', 'showinnavigation');
//print_object($confdata);die;
//setting the page layout
$PAGE->set_pagelayout('standard');

//getting search key from the url it is comming from renderer page
$searchkey = optional_param('searchkey', null, PARAM_TEXT);



//url for the regis_form.php page
$url = $CFG->wwwroot . '/local/new_local/regis_form.php';

//browser output start here
echo $OUTPUT->header();

//getting the instance of the plugin_renderer_base class
$output = $PAGE->get_renderer('local_new_local');

//setting url for ajaxhandler.php page
$url_hit = $CFG->wwwroot . '/local/new_local/ajaxhandler.php';

//invoking javaScript under amd module by this js_call_amd
$PAGE->requires->js_call_amd('local_new_local/search', 'search_func');




//register link on the page
echo html_writer::link($url, get_string('register_link', 'local_new_local'));
echo "  ";



//field for live search
echo html_writer::tag('input', '', ['class' => 'search_user', 'id' => $url_hit]);
echo html_writer::tag('div', '', ['class' => 'show_data']);


echo html_writer::start_div('previous_table', []);

echo $output->local_crud_table($searchkey, $confdata);

echo html_writer::end_div();

//showing download menu
echo $OUTPUT->download_dataformat_selector(get_string('download', 'local_new_local'), 'download.php', 'dataformat');

$url = new moodle_url($CFG->wwwroot . '/local/new_local/index.php');
echo html_writer::link($url, get_string('index', 'local_new_local'));

//code for live search through amd module
$PAGE->requires->js_call_amd('new_local/search', 'search_func');

echo $OUTPUT->footer();
