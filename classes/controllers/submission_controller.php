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
 * This file contains the definition for the library class for author submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_author
 * @copyright 2013 Rene Roepke
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace assign_submission_author;

defined('MOODLE_INTERNAL') || die();

use stdClass;

class submission_controller {

    /**
     * Create submission record of a user for submission
     *
     * @param int $userid
     * @param stdClass $submission
     * @return bool|int <boolean, number>
     * @throws \dml_exception
     */
    public function create_submission($userid, $submission) {

        global $DB;
        $newsubmission = new stdClass();
        $newsubmission->assignment = $submission->assignment;
        $newsubmission->userid = $userid;
        $newsubmission->status = $submission->status;
        $newsubmission->timecreated = time();
        $newsubmission->timemodified = time();
        $newsubmission->groupid = (isset($submission->groupid)) ? $submission->groupid : 0;
        $newsubmission->attemptnumber = $submission->attemptnumber;
        $newsubmission->latest = 1;
        return $DB->insert_record('assign_submission', $newsubmission, false);
    }

    /**
     * Get submission record of a user for an assignment
     *
     * @param int $userid
     * @param int $assignment
     * @return <stdClass or false>
     * @throws \dml_exception
     */
    public function get_submission($userid, $assignment) {
        global $DB;
        return $DB->get_record('assign_submission', array(
                'userid' => $userid,
                'assignment' => $assignment
        ));
    }

    /**
     * Create the author submission record of a submission for an assignment
     *
     * @param int $assignment
     * @param int $submission
     * @param int $author
     * @param string $authorlist
     * @return boolean
     * @throws \dml_exception
     */
    public function create_author_submission($assignment, $submission, $author, $authorlist) {
        global $DB;
        $authorsubmission = new stdClass();
        $authorsubmission->assignment = $assignment;
        $authorsubmission->submission = $submission;
        $authorsubmission->author = $author;
        $authorsubmission->authorlist = $authorlist;
        return $DB->insert_record('assignsubmission_author', $authorsubmission, false) > 0;
    }

    /**
     * Update author submission record
     *
     * @param stdClass $authorsubmission
     * @param int $author
     * @param string $authorlist
     * @return boolean
     * @throws \dml_exception
     */
    public function update_author_submission($authorsubmission, $author, $authorlist) {
        global $DB;
        $authorsubmission->author = $author;
        $authorsubmission->authorlist = $authorlist;
        return $DB->update_record('assignsubmission_author', $authorsubmission, false);
    }

    /**
     * Delete author submission record
     *
     * @param number $userid
     * @param number $assignment the id of the assignment ind mdl_assign
     * @return boolean
     * @throws \dml_exception
     */
    public function delete_author_submission($userid, $assignment) {
        global $DB;
        $submission = $this->get_submission($userid, $assignment);
        return $DB->delete_records('assignsubmission_author', array(
                'submission' => $submission->id
        ));
    }

    /**
     * Get the author submission record of a submission for an assignment
     *
     * @param int $assignment
     * @param int $submission
     * @return <stdClass or false>
     * @throws \dml_exception
     */
    public function get_author_submission($assignment, $submission) {
        global $DB;
        return $DB->get_record('assignsubmission_author', array(
                'assignment' => $assignment,
                'submission' => $submission
        ));
    }
}