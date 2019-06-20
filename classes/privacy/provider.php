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
 * Privacy class for requesting user data.
 *
 * @package    assignsubmission_author
 * @copyright  2019 Benedikt Schneider (@Nullmann)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_author\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\userlist;
use \mod_assign\privacy\assign_plugin_request_data;
use \core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @package    assignsubmission_author
 * @copyright  2019 Benedikt Schneider (@Nullmann)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements 
    \core_privacy\local\metadata\provider,
    \mod_assign\privacy\assignsubmission_provider,
    \mod_assign\privacy\assignsubmission_user_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'assignsubmission_author',
            [
                'id' => 'privacy:assignsubmission_author:id',
                'assignment' => 'privacy:assignsubmission_author:assignment',
                'submission' => 'privacy:assignsubmission_author:submission',
                'author' => 'privacy:assignsubmission_author:author',
                'authorlist' => 'privacy:assignsubmission_author:authorlist',
            ],
            'privacy:metadata:assignsubmission_author'
            );
        
        $collection->add_database_table(
            'assignsubmission_author_def',
            [
                'id' => 'privacy:assignsubmission_author_def:id',
                'assignment' => 'privacy:assignsubmission_author_def:course',
                'submission' => 'privacy:assignsubmission_author_def:user',
                'author' => 'privacy:assignsubmission_author_def:coauthors',
            ],
            'privacy:metadata:assignsubmission_author_def'
            );
        
        return $collection;
    }

    /**
     * This is covered by mod_assign provider and the query on assign_submissions.
     * 
     * @see \mod_assign\privacy\assignsubmission_provider::get_context_for_userid_within_submission()
     * @param  int $userid The user ID that we are finding contexts for.
     * @param  contextlist $contextlist A context list to add sql and params to for contexts.
     */
    public static function get_context_for_userid_within_submission(int $userid, contextlist $contextlist) {
        // This is already fetched from mod_assign because there cannot because this plugin needs an entyr in assign_submission to work.
    }
    
    /**
     * This is covered by mod_assign provider and the query on assign_submissions.
     *
     * @see \mod_assign\privacy\assignsubmission_provider::get_student_user_ids()
     * @param  \mod_assign\privacy\useridlist $useridlist An object for obtaining user IDs of students, provides teacher id.
     */
    public static function get_student_user_ids(\mod_assign\privacy\useridlist $useridlist) {
        // There is not need to add addtional sql because the submissions are done regularly.
    }
    
    /**
     * Export all user data for this plugin.
     * 
     * @see \mod_assign\privacy\assignsubmission_provider::export_submission_user_data()
     * @param  assign_plugin_request_data $exportdata Data used to determine which context and user to export and other useful
     * information to help with exporting.
     */
    public static function export_submission_user_data(assign_plugin_request_data $exportdata) {
        $userid = ($exportdata->get_user() != null);
        $submission = $exportdata->get_pluginobject();
        
        // TODO!
        
        $context = $exportdata->get_context();
        if ($exportdata->get_user() != null) {
            return null;
        }
        
        /* Taken from file submission
        $user = new \stdClass();
        $assign = $exportdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignsubmission', 'file');
        $files = $plugin->get_files($exportdata->get_pluginobject(), $user);
        foreach ($files as $file) {
            $userid = $exportdata->get_pluginobject()->userid;
            writer::with_context($exportdata->get_context())->export_file($exportdata->get_subcontext(), $file);
            
            // Plagiarism data.
            $coursecontext = $context->get_course_context();
            \core_plagiarism\privacy\provider::export_plagiarism_user_data($userid, $context, $exportdata->get_subcontext(), [
                'cmid' => $context->instanceid,
                'course' => $coursecontext->instanceid,
                'userid' => $userid,
                'file' => $file
            ]);
        }
        */

    }
    
    /**
     * Delete all the coauthors made for this context.
     * @see \mod_assign\privacy\assignsubmission_provider::delete_submission_for_context()
     * @param  assign_plugin_request_data $requestdata Data to fulfill the deletion request.
     */
    public static function delete_submission_for_context(assign_plugin_request_data $requestdata) {
        global $DB;

        // Delete all entries in assignsubmission_author where the assignid matches. Could have also be done with $requestdata->getsubmissionids but is faster this way.
        $assignid = $requestdata->get_assignid();
        $DB->delete_records('assignsubmission_author', array ('assignment' => $assignid));

        // Delete all entries in assignsubmission_author_def where the courseid matches. Should not be done on a user basis as a user can have different settings for different courses.
        $courseid = $DB->get_record('assign', array ('id' => $assignid), 'course');
        $DB->delete_records('assignsubmission_author_def', array ('course' => $courseid));
    }
    
    /**
     * A call to this method should delete user data (where practical) using the userid and submission.
     * @see \mod_assign\privacy\assignsubmission_provider::delete_submission_for_userid()
     * @param  assign_plugin_request_data $exportdata Details about the user and context to focus the deletion.
     */
    public static function delete_submission_for_userid(assign_plugin_request_data $exportdata) {
        global $DB;

        // Can be easily deleted as this is the preference for this user in this course.
        $userdeleteid = $exportdata->get_user()->id;
        $DB->delete_records('assignsubmission_author_def', array ('user' => $userdeleteid));

        /*
        $params = [
            'userid' => $userdeleteid
        ];

        // Get the results in which the user is in the author column.
        $sql = "SELECT * FROM {assignsubmission_author}
                  WHERE author = :userid1";
        $rows = $DB->get_records_sql($sql, $params);
        // TODO: Swap author and one of the ids in authorlist?
        
        // Get the results in which the user could be in the author column. Userid 50 also matches with userid 500.
        $sql = "SELECT * FROM {assignsubmission_author}
                  WHERE authorlist LIKE '%:userid1%'";
        $rows = $DB->get_records_sql($sql, $params);
        
        foreach($rows as $row) {
            // It is a comma-separated list, so we need to explode
            $users = explode(',', $row->authorlist);
            foreach($users as $user) {
                if ($user->id == $userdeleteid) {
                    unset($users[$user->id]); // TODO: Does this work?
                }
            }
        }
        */

    }

    /**
     * Deletes all submissions for the submission ids / userids provided in a context.
     * assign_plugin_request_data contains:
     * - context
     * - assign object
     * - submission ids (pluginids)
     * - user ids
     * @see \mod_assign\privacy\assignsubmission_user_provider::delete_submissions()
     * @param  assign_plugin_request_data $deletedata A class that contains the relevant information required for deletion.
     */
    public static function delete_submissions(assign_plugin_request_data $deletedata) {
        global $DB;

        $submissionids = $deletedata->get_submissionids();

        foreach($submissionids as $submissionid) {
            $DB->delete_records('assignsubmission_author', array ('submission' => $submissionid));
        }
    }

    /**
     * If you have tables that contain userids and you can generate entries in your tables without creating an
     * entry in the assign_submission table then please fill in this method.
     * @see \mod_assign\privacy\assignsubmission_user_provider::get_userids_from_context()
     * @param  userlist $userlist The userlist object
     */
    public static function get_userids_from_context(userlist $userlist) {
        // This is not needed as users for which plugin are also present in the assign_submission table.
    }


}
