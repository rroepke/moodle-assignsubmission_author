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

global $CFG;
require_once($CFG->dirroot . '/mod/assign/submission/author/classes/controllers/submission_controller.php');

use stdClass;

class author_group_controller {

    private $assignment;

    /** @var submission_controller Submission Controller */
    private $submissioncontroller;

    /**
     * author_group_controller constructor.
     *
     * @param $assignment
     */
    public function __construct($assignment) {
        $this->assignment = $assignment;
        $this->submissioncontroller = new submission_controller();
    }

    /**
     * Create the submission and all related parts for all coauthors
     *
     * @param array $coauthors
     * @param stdClass $submission
     * @param string $authorlist
     * @throws \dml_exception
     */
    public function create_author_group($coauthors, $submission, $authorlist) {
        global $CFG;
        $submissioncontroller = $this->submissioncontroller;
        $assignment = $submission->assignment;
        $author = $submission->userid;
        foreach ($coauthors as $key => $coauthor) {

            $coauthorsubmission = $submissioncontroller->get_submission($coauthor, $assignment);

            if (!$coauthorsubmission) {

                $submissioncontroller->create_submission($coauthor, $submission);
                require_once($CFG->dirroot . '/mod/assign/lib.php');
                $assign = clone $this->assignment->get_instance();
                $assign->cmidnumber = $this->assignment->get_course_module()->idnumber;
                assign_update_grades($assign, $coauthor);
                $coauthorsubmission = $submissioncontroller->get_submission($coauthor, $assignment);

            }

            $id = $coauthorsubmission->id;
            $submissioncontroller->create_author_submission($assignment, $id, $author, $authorlist);

        }
    }

    /**
     * Update all related parts for all coauthors
     *
     * @param int[] $coauthors
     * @param int $assignment
     * @param int $author
     * @param string $authorlist
     * @throws \dml_exception
     */
    public function update_author_group($coauthors, $assignment, $author, $authorlist) {
        global $DB;
        $submissioncontroller = $this->submissioncontroller;
        foreach ($coauthors as $coauthor) {
            $coauthorsubmission = $submissioncontroller->get_submission($coauthor, $assignment);
            if ($coauthorsubmission) {
                $submissionid = $coauthorsubmission->id;
                $authorsubmission = $submissioncontroller->get_author_submission($assignment, $submissionid);
                if ($authorsubmission) {
                    $authorsubmission->author = $author;
                    $authorsubmission->authorlist = $authorlist;
                    $DB->update_record('assignsubmission_author', $authorsubmission, false);
                }
            }
        }
    }

    /**
     * Get all possible coauthors for assignment
     *
     * @param int $courseid
     * @param int $userid
     * @param boolean $ingroupsonly
     * @param int $assignment
     * @param $groupsused
     * @return array:
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_possible_co_authors($courseid, $userid, $ingroupsonly, $assignment, $groupsused) {
        $submissioncontroller = $this->submissioncontroller;

        if ($groupsused) {

            // Get right groups -> all or user-specific ones.
            if ($ingroupsonly) {
                $groups = groups_get_all_groups($courseid, $userid);
            } else {
                $groups = groups_get_all_groups($courseid);
            }

            // Get all members of the groups.
            $members = array();
            foreach ($groups as $a) {
                $members = $members + groups_get_members($a->id);
            }

            // Collect coauthors.
            $coauthors = array();
            $seen = array();
            foreach ($members as $id => $r) {
                $submission = $submissioncontroller->get_submission($r->id, $assignment);
                if ($submission) {
                    $bool = $this->assignment->get_instance()->submissiondrafts == true;
                    if (!$bool || $submission->status != 'submitted') {
                        $authorsubmission = $submissioncontroller->get_author_submission($assignment, $submission->id);
                        if (!($authorsubmission && $authorsubmission->author != $userid)) {
                            $coauthors[$r->id] = fullname($r);
                        }
                    }
                } else {
                    $coauthors[$id] = fullname($r);
                }
                $seen[$id] = '';
            }

            // User is no group -> return empty array.
            if (!array_key_exists($userid, $seen)) {
                return array();
            }
        } else {

            $us = enrol_get_course_users($courseid);
            $context = \context_course::instance($courseid);
            $users = array();
            foreach($us as $u) {
                if(has_capability('mod/assign:canbecoauthor', $context, $u->id)){
                    $users[$u->id] = $u;
                };
            }

            $records = $users;

            // Collect coauthors.
            $coauthors = array();
            foreach ($records as $id => $r) {
                $submission = $submissioncontroller->get_submission($id, $assignment);
                if ($submission) {
                    $bool = $this->assignment->get_instance()->submissiondrafts == true;
                    if (!$bool || $submission->status != 'submitted') {
                        $authorsubmission = $submissioncontroller->get_author_submission($assignment, $submission->id);
                        if (!($authorsubmission && $authorsubmission->author != $userid)) {
                            $coauthors[$id] = fullname($r);
                        }
                    }
                } else {
                    $coauthors[$id] = fullname($r);
                }
            }
        }

        // Remove user.
        $userarr[$userid] = '';
        $coauthors = array_diff_key($coauthors, $userarr);

        // Sorting coauthors.
        asort($coauthors);

        return $coauthors;
    }

    /**
     * Set author default record of a user in course
     *
     * @param string $coauthors
     * @param int $userid
     * @param int $courseid
     * @return boolean
     * @throws \dml_exception
     */
    public function set_author_default($coauthors, $userid, $courseid) {
        global $DB;
        $authordefault = $DB->get_record('assignsubmission_author_def', array(
                'user' => $userid,
                'course' => $courseid
        ));
        if ($authordefault) {
            $authordefault->coauthors = $coauthors;
            return $DB->update_record('assignsubmission_author_def', $authordefault, false);
        } else {
            $authordefault = new stdClass();
            $authordefault->coauthors = $coauthors;
            $authordefault->course = $courseid;
            $authordefault->user = $userid;
            return $DB->insert_record('assignsubmission_author_def', $authordefault, false) > 0;
        }
    }

    /**
     * Delete all related parts for author group
     *
     * @param int[] $coauthors
     * @param int $assignment
     * @throws \dml_exception
     */
    public function delete_author_group($coauthors, $assignment) {
        $submissioncontroller = $this->submissioncontroller;
        foreach ($coauthors as $coauthor) {
            $submissioncontroller->delete_author_submission($coauthor, $assignment);
        }
    }

    /**
     * Get the author default record of a user in course
     *
     * @param int $user
     * @param int $course
     * @return Ambigous <mixed, stdClass, false, boolean>
     * @throws \dml_exception
     */
    public function get_author_default($user, $course) {
        global $DB;
        return $DB->get_record('assignsubmission_author_def', array(
                'user' => $user,
                'course' => $course
        ));
    }

    /**
     * Get the default authors
     *
     * @param int $userid
     * @param int $courseid
     * @return int[] ids of coauthors
     * @throws \dml_exception
     */
    public function get_default_coauthors($userid, $courseid) {
        $rec = $this->get_author_default($userid, $courseid);
        return explode(',', $rec->coauthors);
    }

    /**
     * Set onlinetext submission records for all coauthors
     *
     * @param int[] $coauthors
     * @param stdClass $data
     * @throws \dml_exception
     */
    public function set_onlinetext_submission_for_coauthors($coauthors, $data) {
        global $DB;

        $submissioncontroller = $this->submissioncontroller;

        // Imitate behaviour of the onlinetext editor plugin for submission.
        if (isset($data->onlinetext_editor)) {
            $assignment = $this->assignment->get_instance()->id;
            $text = $data->onlinetext_editor['text'];
            $format = $data->onlinetext_editor['format'];
            foreach ($coauthors as $coauthor) {
                $submission = $submissioncontroller->get_submission($coauthor, $assignment);
                $onlinetextsubmission = $DB->get_record('assignsubmission_onlinetext', array(
                        'assignment' => $assignment,
                        'submission' => $submission->id
                ));
                if ($onlinetextsubmission) {
                    $onlinetextsubmission->onlinetext = $text;
                    $onlinetextsubmission->onlineformat = $format;
                    $DB->update_record('assignsubmission_onlinetext', $onlinetextsubmission);
                } else {
                    $onlinetextsubmission = new stdClass();
                    $onlinetextsubmission->assignment = $assignment;
                    $onlinetextsubmission->submission = $submission->id;
                    $onlinetextsubmission->onlinetext = $text;
                    $onlinetextsubmission->onlineformat = $format;
                    $DB->insert_record('assignsubmission_onlinetext', $onlinetextsubmission);
                }
            }
        }
    }
}