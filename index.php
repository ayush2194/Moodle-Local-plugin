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
 * Run CSV Mail the web.
 *
 * @package    local_csvmail
 * @author     Ayush Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->dirroot . '/local/csvmail/locallib.php');
require_once($CFG->dirroot . '/local/csvmail/csvmail_form.php');

defined('MOODLE_INTERNAL') || die();
$iid = optional_param('iid', '', PARAM_INT);
$url = new moodle_url('/local/csvmail/index.php');
$context = context_system::instance();
require_login();
require_capability('moodle/user:create', $context);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('sendrandomemailtousers', 'local_csvmail'));


$mform = new local_csvmail_form();
if (empty($iid)) {
    if ($data = $mform->get_data()) {
        $iid = csv_import_reader::get_new_iid('csvmail');
        $cir = new csv_import_reader($iid, 'csvmail');
        $content = $mform->get_file_content('userfile');
        $rows = explode("\n", $content);
        foreach ($rows as $key => $row) {
            if ($key != 0) {
                $csvdata[] = str_getcsv($row, ",");
            }
        }
        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        $csvloaderror = $cir->get_error();
        if (!is_null($csvloaderror)) {
            throw new \moodle_exception('csvloaderror', '', $returnurl, $csvloaderror);
        }
    } else {
        $PAGE->set_heading(get_string('sendrandomemailtousers', 'local_csvmail'));
        echo $OUTPUT->header();
        $olddataurl = new moodle_url($CFG->wwwroot.'/local/csvmail/verify.php');
        echo html_writer::link($olddataurl, get_string('olddata', 'local_csvmail'), array('class' => 'btn btn-primary'));
        $mform->display();
        echo $OUTPUT->footer();
        die;
    }
}
$mform2 = new local_mailsend_form(null, ['iid' => $iid, 'csvdata' => $csvdata]);
$PAGE->set_heading(get_string('verifydata', 'local_csvmail'));
echo $OUTPUT->header();
$tabaldata = verify_data($csvdata);

if ($mform2->is_cancelled()) {
    redirect($url);
} else if ($data2 = $mform2->get_data()) {
    $unserializecontent = unserialize($data2->my_array);
    if (sendmail_updatedata($unserializecontent)) {
        $returnurl = new moodle_url($CFG->wwwroot.'/local/csvmail/verify.php');
        redirect($returnurl);
    } else {
        redirect($url);
    }
}
echo $OUTPUT->render_from_template('local_csvmail/verify', $tabaldata);
$mform2->display();
echo $OUTPUT->footer();
