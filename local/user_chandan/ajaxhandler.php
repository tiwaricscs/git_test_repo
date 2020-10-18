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
 * This is ajax handler file used for searching in the page.
 *
 * @package local_user_chandan
 * @author Ballistic Learning
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
// define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once('./lib.php');
$context = context_user::instance();
$PAGE->set_context($context);
$url = $CFG->wwwroot . '/local/user_chandan/index.php';
$search_key = optional_param('search_key', '', PARAM_RAW);
$output = $PAGE->get_renderer('local_user_chandan');
echo $output->search_box($search_key);