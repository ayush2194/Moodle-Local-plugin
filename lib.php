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
 * Library functions for CSV mail.
 *
 * @package    local_csvmail
 * @author     Ayush Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_csvmail_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;
    require_once("$CFG->libdir/resourcelib.php");
    $filename = array_pop($args);
    $itemid = array_shift($args);
    $fs = get_file_storage();
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';
    if (!$file = $fs->get_file($context->id, 'local_csvmail', $filearea, $itemid, $filepath, $filename) || $file->is_directory()) {
        send_file_not_found();
    }
    // Finally send the file.
    send_stored_file($file, null, 0, $forcedownload, $options);
}
