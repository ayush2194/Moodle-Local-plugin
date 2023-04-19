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
 * Form CSV Mail.
 *
 * @package    local_csvmail
 * @author     Ayush Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
defined('MOODLE_INTERNAL') || die();
require_login();
class local_csvmail_form extends moodleform {
    // Add elements to form.
    public function definition() {
        $mform = $this->_form; // Don't forget the underscore!
        $url = new moodle_url('example.csv');
        $link = html_writer::link($url, 'example.csv');
        $mform->addElement('static', 'examplecsv', get_string('examplecsv', 'tool_uploaduser'), $link);
        $mform->addHelpButton('examplecsv', 'examplecsv', 'tool_uploaduser');
        /* Csv upload
        $options = array(
            'maxfiles' => 1,
            'maxbytes' => 10485760, // 5MB (2MB=2097152, 5MB=5242880, 10MB=10485760)
            'subdirs' => 0,
            'accepted_types' => ['csv'],
            'context' => context_system::instance(),
        );
        $mform->addElement('filemanager', 'csvmail_filemanager', get_string('uploadfile', 'local_csvmail'), null, $options);
        $mform->setType('csvmail_filemanager', PARAM_TEXT);
        $mform->addRule('csvmail_filemanager', get_string('mmisscategoryfilemanager', 'local_csvmail'), 'required', null, 'client');
        */
        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $this->add_action_buttons(true, get_string('upload'));
    }
}


class local_mailsend_form extends moodleform {
    // Add elements to form.
    public function definition() {
        global $CFG, $DB;

        $mform = $this->_form; // Don't forget the underscore!
        $csvdata = $this->_customdata['csvdata'];
        $iid = $this->_customdata['iid'];
        $mform->addElement('hidden', 'iid', $iid);
        $mform->setType('iid', PARAM_INT);

        // Serialize the array into a string.
        $datastring = serialize($csvdata);
        // Create a Moodle form element.
        $mform->addElement('hidden', 'my_array');
        $mform->setType('my_array', PARAM_RAW); // Set the type to raw to avoid any data sanitization.
        $mform->hardFreeze('my_array'); // Freeze the element to avoid modifications.
        // Set the value of the element to the serialized array data.
        $mform->setDefault('my_array', $datastring);
        $this->add_action_buttons(true, get_string('submit'));
    }
}
