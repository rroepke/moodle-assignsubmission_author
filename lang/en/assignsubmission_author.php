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
 * Strings for component 'assignsubmission_author', language 'en'
 *
 * @package assignsubmission_author
 * @copyright 2013 Rene Roepke
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['author'] = 'Author groups';

// Shown during submission.
$string['choose'] = 'Choose co-author';
$string['choose_group'] = 'Choose existing author group';
$string['choose_coauthors'] = 'Choose co-authors';
$string['choose_coauthors_remove'] = '. When changing the co authors after a submission has been made, the submission will be deleted for the old co authors.';
$string['choose_coauthors_no_remove'] = '. When changing the co authors after a submission has been made, the old submission will remain with the old co authors.';
$string['choose_new_coauthors'] = 'Choose new co-authors';
$string['choose_new_coauthors_no_remove'] = '. If something has already been submitted, the old submission will remain with the old authors.';
$string['choose_defaultcoauthors'] = 'Choose your default co-authors';
$string['choose_nocoauthors'] = 'No co-authors';

$string['summary_author'] = 'Author';
$string['summary_coauthors'] = 'Co-authors';
$string['summary_nocoauthors'] = 'No co-authors';

$string['group'] = 'Group';
$string['coauthors'] = 'Co-authors';
$string['defaultcoauthors'] = 'Defaul co-authors';

$string['oneauthoronly'] = 'The maximum number of coauthors has been set to 1.';
$string['noteamsubmission'] = 'The submission type "Author groups" cannot be used with the group submission settings. Please contact the course manager if you have any questions.';

$string['subject'] = 'Author group submission in course "{$a->coursename}"';
$string['subject_deleted'] = 'You have been removed from an author group in course "{$a->coursename}"';
$string['message'] = 'Course <a href="{$a->courseurl}">{$a->coursename}</a>: <a href="{$a->userurl}">{$a->username}</a> has done a submission for the assignment <a href="{$a->assignmenturl}">{$a->assignmentname}</a> and added you as a co-author.';
$string['message_deleted'] = 'Course <a href="{$a->courseurl}">{$a->coursename}</a>: The user <a href="{$a->userurl}">{$a->username}</a> deleted you as a co author from the assignment <a href="{$a->assignmenturl}">{$a->assignmentname}</a>.';

// Mod settings for each instance.
$string['setting_explanation'] = 'These settings are disabled as long as the "'.$string['author'].'" checkbox is not set in the "{$a->submisiontypesstring}" section above.';
$string['maxauthors'] = 'Maximum number of authors';
$string['maxauthors_help'] = 'The maximum number of author includes the user himself, e.g. "1" means no other co-authors are allowed, while "2" means one co-author beside the user himself';
$string['ingroupsonly'] = 'Only in the same group';
$string['ingroupsonly_help'] = 'Users can only choose co-authors from members of their own group.';
$string['notification'] = 'Send notifications to co-authors';
$string['notification_help'] = 'If enabled, all co-authors will get a message when a submission was made and when they are removed from an author group';
$string['groupsused'] = 'Users need to be in a group';
$string['groupsused_help'] = 'If groups and this option are enabled, only members of groups are able to choose co-authors.';
$string['displaymail'] = 'Display Mails of users while selecting co-authors';
$string['displaymail_help'] = "This helps users identify their desired co authors when there are multiples of same names. <br><br>Please be aware of users' privacy.";
$string['duplicatesubmission'] = 'Duplicate submission for all group members';
$string['duplicatesubmission_help'] = 'When enabled, the submission from the original author will be duplicated for every co author and vise versa.<br>'
.'This way all authors of a co author group can see and edit the submission. <br><br>'
.'Be aware that this could result in a user multiplying his/her submission for every user in the course if he/she juggles through all available co authors. '
.'Plagiarism plugins will show false positives because the users have exactly the same submission.';
$string['removesubmission'] = 'Remove submission when co-authors are removed';
$string['removesubmission_help'] = 'When the group members of an author group are changed by the author after a submission was made, the submission is also removed for the removed authors. '
.'The removed authors then have no submission anymore. This can be useful if the author chose wrong co authors. <br><br>'
.'Although this could result in authors "taking away" other users submission, a mail can be sent if they are removed from an author group (see option above).<br><br>'
.'If co authors change their group, the submission will still remain with the original author and the rest of the co authors.';

$string['asdefault'] = 'Save as new default co-authors in this course';
$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';
$string['enabled'] = 'Multiple Authors';
$string['enabled_help'] = 'If enabled, users are able to choose co-authors and save a default.';
$string['nosubmission'] = 'Nothing has been submitted for this assignment';

$string['pluginname'] = 'Author groups submission';
$string['nopossiblecoauthors'] = 'You cannot choose co-authors because you are not in a group. Please contact the course manager.';

$string['error_teamsubmission'] = 'You cannot submit a solution. <br><br>The submission type "Author groups" cannot be used with the group submission settings. Please contact the course manager if you have any questions.';

$string['header'] = 'Selection of Co-Authors';

// Capability.
$string['author:canbecoauthor'] = 'The user is allowed to assign co authors.';

// Privacy API.
$string['privacy:metadata:assignsubmission_author'] = 'Information about author submission, including co authors.';
$string['privacy:assignsubmission_author:id'] = 'Identifier of this table entry.';
$string['privacy:assignsubmission_author:assignment'] = 'ID of corresponding assignment in the "assign" table.';
$string['privacy:assignsubmission_author:submission'] = 'ID of corresponding assignment in the "assign_submission" table.';
$string['privacy:assignsubmission_author:author'] = 'ID of student who submitted the original assignment.';
$string['privacy:assignsubmission_author:authorlist'] = 'Comma-separated list of studend IDs the  student declared as co-authors.';

$string['privacy:metadata:assignsubmission_author_def'] = 'Information about default values for co authors.';
$string['privacy:assignsubmission_author_def:id'] = 'Identifier of this table entry.';
$string['privacy:assignsubmission_author_def:course'] = 'The course ID in which these defaults should be applied.';
$string['privacy:assignsubmission_author_def:userid'] = 'ID of student who submitted the original assignment.';
$string['privacy:assignsubmission_author_def:coauthors'] = 'Comma-separated list of studend IDs the student declared as his/her default co-authors.';

// Event 2.
$string['eventauthorgroupcreated'] = 'Author group created';
$string['eventauthorgroupupdated'] = 'Author group updated';
$string['eventauthorgroupdeleted'] = 'Author group deleted';