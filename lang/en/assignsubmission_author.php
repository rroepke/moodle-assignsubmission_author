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

$string['choose'] = 'Choose co-author';
$string['choose_group'] = 'Choose group';
$string['choose_coauthors'] = 'Choose co-authors';
$string['choose_defaultcoauthors'] = 'Choose your default co-authors';
$string['choose_nocoauthors'] = 'No co-authors';

$string['summary_author'] = 'Author';
$string['summary_coauthors'] = 'Co-authors';
$string['summary_nocoauthors'] = 'No co-authors';

$string['group'] = 'Group';
$string['coauthors'] = 'Co-authors';
$string['defaultcoauthors'] = 'Defaul co-authors';

$string['noteamsubmission'] = 'The submission type "Author groups" cannot be used with the group submission settings. Please contact the course manager if you have any questions.';

$string['subject'] = 'Author group submission in course <a href="{$a->courseurl}">{$a->coursename}</a>';
$string['message'] = '{$a->username} has done a submission for the assignment <a href="{$a->assignmenturl}">{$a->assignmentname}</a> and added you as a co-author.';

$string['maxauthors'] = 'Maximum number of authors';
$string['maxauthors_help'] = 'The maximum number of author includes the user himself, e.g. "1" means no other co-authors are allowed, while "2" means one co-author beside the user himself';
$string['ingroupsonly'] = 'Only in the same group';
$string['ingroupsonly_help'] = 'If enabled, then the user can only choose co-authors out of his own group, else out of the whole course';
$string['notification'] = 'Inform all co-authors';
$string['notification_help'] = 'If enabled, then all co-authors will get a message after a submission';
$string['groupsused'] = 'Groups enabled?';
$string['groupsused_help'] = 'If groups and this option are enabled, only members of groups are able to choose co-authors. Others won\'t be able to choose co-authors.';
$string['asdefault'] = 'Save as new default co-authors in this course';

$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';
$string['enabled'] = 'Multiple Authors';
$string['enabled_help'] = 'If enabled, users are able to choose co-authors and save a default.';
$string['nosubmission'] = 'Nothing has been submitted for this assignment';

$string['pluginname'] = 'Author groups submission';
$string['nopossiblecoauthors'] = 'You cannot choose co-authors because you are not in a group. Please contact the course manager.';

$string['error_teamsubmission'] = 'You cannot submit a solution. <br><br>The submission type "Author groups" cannot be used with the group submission settings. Please contact the course manager if you have any questions.';
