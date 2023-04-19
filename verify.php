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
 * Verify CSV data.
 *
 * @package    local_csvmail
 * @author     Ayush Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/csvmail/locallib.php');

defined('MOODLE_INTERNAL') || die();
$page = optional_param('page', 0, PARAM_INT);
$url = new moodle_url('/local/csvmail/verify.php');
$context = context_system::instance();
require_login();
require_capability('moodle/user:create', $context);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('verifydata', 'local_csvmail'));
$PAGE->set_heading(get_string('verifydata', 'local_csvmail'));
echo $OUTPUT->header();
$data = review_data($page);
echo $OUTPUT->render_from_template('local_csvmail/verify', $data);
echo $OUTPUT->paging_bar($data['datacount'], $page, PAGE_LIMIT, $url);
echo $OUTPUT->footer();
