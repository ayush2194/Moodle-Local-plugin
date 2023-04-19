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
 * Local Library functions for CSV mail.
 *
 * @package    local_csvmail
 * @author     Ayush Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('MAIL_SENT', 1);
define('MAIL_NOTSENT', 2);
define('USER_NOTEXITS', 3);
define('PAGE_LIMIT', 10);

function sendmail_updatedata($csvdata) {

    global $DB, $CFG;

    if (!empty($csvdata)) {
        foreach ($csvdata as $data) {
            if (!empty($data[0]) && !empty($data[1] && !empty($data[2]))) {

                $insertobject = new stdClass;
                $insertobject->firstname = $data[0];
                $insertobject->lastname = $data[1];
                $insertobject->email = $data[2];
                $insertobject->timecreate = time();
                $user = core_user::get_user_by_email($data[2]);
                if (!empty($user)) {
                    $userfrom = \core_user::get_support_user();
                    $subject = get_string('sample_subject', 'local_csvmail');
                    $messagetext = get_string('sample_message', 'local_csvmail');
                    if (email_to_user($user, $userfrom, $subject, $messagetext)) {
                        $insertobject->status = MAIL_SENT;
                    } else {
                        $insertobject->status = MAIL_NOTSENT;
                    }
                    $DB->insert_record('local_csvmail', $insertobject);
                } else {
                    $insertobject->status = USER_NOTEXITS;
                    $DB->insert_record('local_csvmail', $insertobject);
                }
            }
        }
        return true;
    } else {
        return false;
    }
}


function verify_data($csvdata) {

    if (!empty($csvdata)) {
        $tabledata = array();
        $sn = 1;
        foreach ($csvdata as $data) {

            $dataobject = new stdClass;
            $dataobject->index = $sn;
            $dataobject->firstname = $data[0];
            $dataobject->lastname = $data[1];
            $dataobject->email = $data[2];
            $tabledata[] = $dataobject;
            $sn++;
        }
        return ['data' => $tabledata];
    } else {
        return false;
    }
}


function review_data($page) {

    global $DB;
    $sn = 1;
    $page = $page * PAGE_LIMIT;
    if ($page) {
        $sn = $page + 1;
    }
    $alldata = $DB->get_records('local_csvmail', array(), 'id DESC', '*', $page, PAGE_LIMIT);
    $datacount = $DB->count_records('local_csvmail', null);
    if (!empty($alldata)) {
        $tabledata = array();
        foreach ($alldata as $data) {

            $dataobject = new stdClass;
            $dataobject->index = $sn;
            $dataobject->firstname = $data->firstname;
            $dataobject->lastname = $data->lastname;
            $dataobject->email = $data->email;
            if ($data->status == 1) {
                $dataobject->status = get_string('sendmailsuccess', 'local_csvmail');
            } else if ($data->status == 2) {
                $dataobject->status = get_string('mailotsent', 'local_csvmail');
            } else if ($data->status == 3) {
                $dataobject->status = get_string('usernotexits', 'local_csvmail');
            }
            $dataobject->time = date("d-m-Y H:i:s", $data->timecreate);
            $tabledata[] = $dataobject;
            $sn++;
        }
        return ['data' => $tabledata, 'datacount' => $datacount, 'resultview' => true];
    } else {
        return false;
    }
}
