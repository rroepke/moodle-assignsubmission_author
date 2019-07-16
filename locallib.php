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
defined('MOODLE_INTERNAL') || die();

define('ASSIGNSUBMISSION_ONLINETEXT', 'onlinetext');
define('ASSIGNSUBMISSIONAUTHOR_MAXAUTHORS', 20);

require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/submission/author/classes/controllers/submission_controller.php');
require_once($CFG->dirroot . '/mod/assign/submission/author/classes/controllers/author_group_controller.php');
require_once($CFG->dirroot . '/mod/assign/submission/author/classes/utilities.php');

use assign_submission_author\utilities;
use assign_submission_author\submission_controller;
use assign_submission_author\author_group_controller;

/**
 * Library class for author submission plugin extending submission plugin base class
 *
 * @package assignsubmission_author
 * @author Rene Roepke
 * @author Guido Roessling
 * @copyright 2013 Rene Roepke
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_author extends assign_submission_plugin
{

    /**
     * Get the name of the author submission plugin
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name() {
        return get_string('author', 'assignsubmission_author');
    }

    /**
     * Get the default setting for author submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     * @throws coding_exception
     */
    public function get_settings(MoodleQuickForm $mform) {
        // Get config infos.
        if ($this->assignment->has_instance()) {
            // There is no config if a new submission is created.
            $settings = $this->get_config();
        } else {
            // Default settings for new submissions.
            $settings = new stdClass();
            $settings->enabled = true;
            $settings->maxauthors = 1;
            $settings->notification = true;
            $settings->groupsused = false;
            $settings->ingroupsonly = false;
            $settings->displaymail = true;
            $settings->duplicatesubmission = true;
            $settings->removesubmission = true;
        }

        // Generate maxauthors setting.
        $options = array();
        for ($i = 1; $i <= ASSIGNSUBMISSIONAUTHOR_MAXAUTHORS; $i++) {
            $options[$i] = $i;
        }

        // Add a new header for this plugin. Only the "enabled" setting is part of the submission types above.
        $mform->addElement('header', 'assignsubmissionauthor_header', get_string('pluginname', 'assignsubmission_author'));
        $mform->disabledIf('assignsubmissionauthor_header', 'assignsubmission_author_enabled', 'notchecked');
        if ($settings->enabled) { // Automatically expand it if the plugin is used.
            $mform->setExpanded('assignsubmissionauthor_header');
        }

        // Explanation for this part.
        $a = new stdClass();
        $a->submisiontypesstring = get_string('submissiontypes', 'assign');
        $explanation = get_string('setting_explanation', 'assignsubmission_author', $a);
        $mform->addElement('static', '', $explanation);

        // Display maxauthors setting.
        $mform->addElement('select', 'assignsubmissionauthor_maxauthors', get_string('maxauthors', 'assignsubmission_author'), $options);
        $mform->setType('assignsubmissionauthor_notification', PARAM_INT);
        $mform->addHelpButton('assignsubmissionauthor_maxauthors', 'maxauthors', 'assignsubmission_author');
        $mform->setDefault('assignsubmissionauthor_maxauthors', $settings->maxauthors);
        $mform->disabledIf('assignsubmissionauthor_maxauthors', 'assignsubmission_author_enabled', 'notchecked');

        // Display notification setting.
        $mform->addElement('advcheckbox', 'assignsubmissionauthor_notification', get_string('notification', 'assignsubmission_author'), '', 0);
        $mform->setType('assignsubmissionauthor_notification', PARAM_BOOL);
        $mform->setDefault('assignsubmissionauthor_notification', $settings->notification);
        $mform->addHelpButton('assignsubmissionauthor_notification', 'notification', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_notification', 'assignsubmission_author_enabled', 'notchecked');

        // Display groupsused setting.
        $mform->addElement('advcheckbox', 'assignsubmissionauthor_groupsused', get_string('groupsused', 'assignsubmission_author'), '', 0);
        $mform->setType('assignsubmissionauthor_groupsused', PARAM_BOOL);
        $mform->setDefault('assignsubmissionauthor_groupsused', $settings->groupsused);
        $mform->addHelpButton('assignsubmissionauthor_groupsused', 'groupsused', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_groupsused', 'assignsubmission_author_enabled', 'notchecked');

        // Display ingroupsonly setting.
        $mform->addElement('advcheckbox', 'assignsubmissionauthor_ingroupsonly', get_string('ingroupsonly', 'assignsubmission_author'), '', 0);
        $mform->setType('assignsubmissionauthor_ingroupsonly', PARAM_BOOL);
        $mform->setDefault('assignsubmissionauthor_ingroupsonly', $settings->ingroupsonly);
        $mform->addHelpButton('assignsubmissionauthor_ingroupsonly', 'ingroupsonly', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_ingroupsonly', 'assignsubmissionauthor_groupsused', 'notchecked');
        $mform->disabledIf('assignsubmissionauthor_ingroupsonly', 'assignsubmission_author_enabled', 'notchecked');

        // Display option to show emails of user in selection.
        $mform->addElement('advcheckbox', 'assignsubmissionauthor_displaymail', '', get_string('displaymail', 'assignsubmission_author'));
        $mform->setType('assignsubmissionauthor_displaymail', PARAM_BOOL);
        $mform->setDefault('assignsubmissionauthor_displaymail', $settings->displaymail);
        $mform->addHelpButton('assignsubmissionauthor_displaymail', 'displaymail', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_displaymail', 'assignsubmission_author_enabled', 'notchecked');

        // Display option to duplicate each submission so co authors can see and edit them.
        $mform->addElement('advcheckbox', 'assignsubmissionauthor_duplicatesubmission', '', get_string('duplicatesubmission', 'assignsubmission_author'));
        $mform->setType('assignsubmissionauthor_duplicatesubmission', PARAM_BOOL);
        $mform->setDefault('assignsubmissionauthor_duplicatesubmission', $settings->duplicatesubmission);
        $mform->addHelpButton('assignsubmissionauthor_duplicatesubmission', 'duplicatesubmission', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_duplicatesubmission', 'assignsubmission_author_enabled', 'notchecked');

        // Display option to remove the submissions of removed co authors.
        $mform->addElement('advcheckbox', 'assignsubmissionauthor_removesubmission', '', get_string('removesubmission', 'assignsubmission_author'));
        $mform->setType('assignsubmissionauthor_removesubmission', PARAM_BOOL);
        $mform->setDefault('assignsubmissionauthor_removesubmission', $settings->removesubmission);
        $mform->addHelpButton('assignsubmissionauthor_removesubmission', 'removesubmission', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_removesubmission', 'assignsubmission_author_enabled', 'notchecked');
        $mform->disabledIf('assignsubmissionauthor_removesubmission', 'assignsubmissionauthor_duplicatesubmission', 'notchecked');
    }

    /**
     * Save the settings for author submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        $this->set_config('maxauthors', $data->assignsubmissionauthor_maxauthors);
        $this->set_config('notification', $data->assignsubmissionauthor_notification);

        $this->set_config('groupsused', $data->assignsubmissionauthor_groupsused);
        $setable = ($data->assignsubmissionauthor_groupsused == true && $data->assignsubmissionauthor_ingroupsonly == true);
        $this->set_config('ingroupsonly', $setable); // Can only be set if groupsused is also set.

        $this->set_config('displaymail', $data->assignsubmissionauthor_displaymail);

        $this->set_config('duplicatesubmission', $data->assignsubmissionauthor_duplicatesubmission);
        $setable = ($data->assignsubmissionauthor_removesubmission == true && $data->assignsubmissionauthor_duplicatesubmission == true);
        $this->set_config('removesubmission', $setable); // Can only be set if duplicatesubmission is also set.
        return true;
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $USER, $COURSE;

        $authorgroupcontroller = new author_group_controller($this->assignment);
        $submissioncontroller = new submission_controller();

        // Get maxauthors config info.
        $maxauthors = $this->get_config('maxauthors');

        // If maxauthors <= 1 then return comment and no more content.
        if ($maxauthors <= 1) {
            $mform->addElement('static', '', '', get_string('oneauthoronly', 'assignsubmission_author'), 1);
            return true;
        }

        // If team assignment is activated then return comment and no more content.
        if ($this->assignment->get_instance()->teamsubmission == 1) {
            $mform->addElement('static', '', '', get_string('noteamsubmission', 'assignsubmission_author'), 1);
            return true;
        }

        // Start generating content.
        $courseid = $COURSE->id;
        $userid = $USER->id;
        $selectedauthors = array();
        $alreadyinauthorgroup = false;
        $assignment = $this->assignment->get_instance()->id;

        // If authorsubmission then get it.
        if ($submission) {
            $authorsubmission = $submissioncontroller->get_author_submission(
                    $assignment,
                    $submission->id);
            if ($authorsubmission) {
                $alreadyinauthorgroup = $authorsubmission->author != $userid;
                $selectedauthors = utilities::get_author_array(
                        $authorsubmission->author . ',' . $authorsubmission->authorlist,
                        $this->assignment->get_course()->id,
                        true);
                $origauthor = utilities::get_author_array($authorsubmission->author, $this->assignment->get_course()->id, true);
            }
        }

        $ingroupsonly = $this->get_config('ingroupsonly'); // Get ingroupsonly config info.
        $groupsused = $this->get_config('groupsused'); // Get config info about groups.

        // Get possible coauthors.
        $displaymail = $this->get_config('displaymail');
        $possiblecoauthors = $authorgroupcontroller->get_possible_co_authors($courseid, $userid, $ingroupsonly, $assignment, $groupsused, $displaymail);

        $userarr = null;
        $userarr[$userid] = '';

        // Get record for default co authors.
        $authordefaultsubmission = $authorgroupcontroller->get_author_default($userid, $courseid);

        if ($authordefaultsubmission) {
            $default = $authordefaultsubmission->coauthors;
            $array = utilities::get_author_array($default, $this->assignment->get_course()->id, true);
            $array = array_diff_key($array, $userarr);
            $showdefault = utilities::is_default_usable($array, $possiblecoauthors, $maxauthors);
            $default = implode(', ', $array);
        }

        // Get preselected authors.
        $selectedauthors = array_diff_key($selectedauthors, $userarr);

        // Set reactive behaviour for all options.
        $mform->disabledIf('defcoauthors', 'selcoauthors', 'checked');
        $mform->disabledIf('defcoauthors', 'nocoauthors', 'checked');
        $mform->disabledIf('defcoauthors', 'groupcoauthors', 'checked');
        $mform->disabledIf('selcoauthors', 'defcoauthors', 'checked');
        $mform->disabledIf('selcoauthors', 'nocoauthors', 'checked');
        $mform->disabledIf('selcoauthors', 'groupcoauthors', 'checked');
        $mform->disabledIf('nocoauthors', 'defcoauthors', 'checked');
        $mform->disabledIf('nocoauthors', 'selcoauthors', 'checked');
        $mform->disabledIf('nocoauthors', 'groupcoauthors', 'checked');
        $mform->disabledIf('groupcoauthors', 'defcoauthors', 'checked');
        $mform->disabledIf('groupcoauthors', 'selcoauthors', 'checked');
        $mform->disabledIf('groupcoauthors', 'nocoauthors', 'checked');

        // Show existing author group for co authors.
        if ($alreadyinauthorgroup) {
            $mform->setDefault('groupcoauthors', 'checked');
            $mform->addElement('checkbox', 'groupcoauthors', '', get_string('choose_group', 'assignsubmission_author'), 1);
            $mform->addElement('static', 'group2coauthors', get_string('group', 'assignsubmission_author'),
                $this->get_summary($origauthor, array_diff_key($selectedauthors, $origauthor)), null);
            $mform->addElement('static', '', '', '');
        } else {
            $mform->setDefault('selcoauthors', 'checked'); // Pre-select "choose co authors" for author only.
        }

        $mform->addElement('header', 'header', get_string('header', 'assignsubmission_author'));

        // Select the right string to display the "choose new co authors" option, depending on the settings done by the teacher.
        $settings = $this->get_config();
        $concequencesstring = '';
        if ($alreadyinauthorgroup) { // Co author perspective.
            $chooseauthorsstring = get_string('choose_new_coauthors', 'assignsubmission_author');
            if (isset($settings->duplicatesubmission) && $settings->duplicatesubmission) {
                $concequencesstring = get_string('choose_new_coauthors_no_remove', 'assignsubmission_author'); // Submissions are duplicated and deleted.
            } // No different string when submissions are deleted because this only affects authors.
        } else { // Author perspective.
            $chooseauthorsstring = get_string('choose_coauthors', 'assignsubmission_author');
            if (isset($settings->duplicatesubmission) && $settings->duplicatesubmission && isset($settings->removesubmission) && $settings->removesubmission) {
                $concequencesstring = get_string('choose_coauthors_remove', 'assignsubmission_author'); // Submissions are duplicated and deleted.
            } else if (isset($settings->duplicatesubmission) && $settings->duplicatesubmission) {
                $concequencesstring = get_string('choose_coauthors_no_remove', 'assignsubmission_author'); // Submissions are only duplicated.
            }
        }
        $mform->addElement('checkbox', 'selcoauthors', '', $chooseauthorsstring.$concequencesstring, 1);

        if (count($possiblecoauthors) != 0) {
            // Define content of choice boxes.
            $achoices = array();
            $achoices[0] = get_string('choose', 'assignsubmission_author');
            $achoices = $achoices + $possiblecoauthors;

            // Generate as many choice boxes as necessary.
            $objs = array();
            for ($i = 0; $i < $maxauthors - 1; ++$i) {
                $objs[$i] = &$mform->createElement('select', 'coauthors[' . $i . ']', '', $achoices, null);
            }

            // Add elements.
            $mform->addElement('group', 'coauthorselection', get_string('coauthors', 'assignsubmission_author'), $objs, ' ', false);
            $mform->disabledIf('coauthorselection', 'selcoauthors', 'notchecked');
            $mform->addElement('checkbox', 'asdefault', ' ', get_string('asdefault', 'assignsubmission_author'));
            $mform->disabledIf('asdefault', 'selcoauthors', 'notchecked');

            // Set preselected coauthors for author in choice boxes for author.
            if (!$alreadyinauthorgroup) {
                $i = 0;
                foreach ($selectedauthors as $key => $value) {
                    $mform->setDefault('coauthors[' . $i . ']', $key);
                    $i++;
                }
            }
        } else {
            $mform->addElement('static', '', '', get_string('nopossiblecoauthors', 'assignsubmission_author'), 1);
        }

        $mform->addElement('static', '', '', ''); // Just adds a line break for better reading.

        // Show default co authors if there are any saved.
        if (isset($showdefault) && $showdefault && isset($default)) {
            $mform->addElement('checkbox', 'defcoauthors', '',
                    get_string('choose_defaultcoauthors', 'assignsubmission_author'), 1);
            $mform->addElement('static', 'defaultcoauthors',
                    get_string('defaultcoauthors', 'assignsubmission_author'), $default, 1);
            $mform->addElement('static', '', '', '');
        }

        // Display option for no coauthors.
        $mform->addElement('checkbox', 'nocoauthors', '', get_string('choose_nocoauthors', 'assignsubmission_author').$concequencesstring, 1);

        return true;
    }

    /**
     * Save data to the database
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $COURSE;

        $submissioncontroller = new submission_controller();
        $authorgroupcontroller = new author_group_controller($this->assignment);
        $settings = $this->get_config();

        // If team submission is activated no submission is possible.
        if ($this->assignment->get_instance()->teamsubmission == 1) {
            $this->set_error(get_string('error_teamsubmission', 'assignsubmission_author'));
            return false;
        }

        // Get notification config info.
        $notification = $this->get_config('notification');

        $userid = $USER->id;
        $courseid = $COURSE->id;
        $assignment = $this->assignment->get_instance()->id;

        $currentcoauthors = array();
        if ($submission) {
            $authorsubmission = $submissioncontroller->get_author_submission($assignment, $submission->id);

            if ($authorsubmission) { // Update existing author submission.

                // Get current coauthors as array.
                $currentcoauthors = explode(',', $authorsubmission->authorlist);

                if (isset($data->groupcoauthors) && $data->groupcoauthors == 1) {
                    // Option "Choose group" (which the original author created) - coauthor perspective.
                    $allauthorids = explode(',', $authorsubmission->author . ',' . $authorsubmission->authorlist);

                    // Clone this submission for every other author.
                    foreach ($allauthorids as $authorid) {
                        if ($authorid != $userid) {
                            $submissiontooverwrite = $submissioncontroller->get_submission($authorid, $assignment);
                            $authorgroupcontroller->duplicate_submission($submissiontooverwrite, $data);
                        }
                    }

                    return true;
                } else if ($authorsubmission->author == $userid) { // Author perspective.
                    if (isset($data->selcoauthors) && $data->selcoauthors == 1) {
                        // Option "Choose co-authors" and selecting coauthors - author perspective.

                        // Get new selected coauthors.
                        $selectedcoauthors = utilities::get_selected_coauthors($data);

                        if (count($selectedcoauthors) == 0) {
                            // If no coauthors are selected, delete current authorgroup. Author perspective.
                            $deletecoauthors = $currentcoauthors;

                            if (isset($settings->removesubmission) && $settings->removesubmission) {
                                $this->remove($submission);
                            }
                            $authorgroupcontroller->delete_author_group($deletecoauthors, $submission->assignment);
                            $this->trigger_delete_event($userid, $deletecoauthors, $submission);
                            if ($notification) {
                                $this->send_notifications($userid, array(), $deletecoauthors);
                            }

                            $submissioncontroller->delete_author_submission($userid, $submission->assignment);

                        } else {
                            // There are new coauthors selected. Update author group. Author perspective.
                            $this->trigger_update_event($userid, $currentcoauthors, $selectedcoauthors, $submission);

                            // Distinguish between new coauthors, deleted coauthors, current coauthors.
                            $deletecoauthors = array_diff($currentcoauthors, $selectedcoauthors);
                            $newcoauthors = array_diff($selectedcoauthors, $currentcoauthors);
                            $updatecoauthors = array_diff($selectedcoauthors, $newcoauthors);
                            $currentcoauthors = $selectedcoauthors;

                            // Delete author group with deleted coauthors and remove their submission if set in settings.
                            if (isset($settings->removesubmission) && $settings->removesubmission) {
                                $this->remove($submission);
                            }
                            $authorgroupcontroller->delete_author_group($deletecoauthors, $submission->assignment);

                            $author = $authorsubmission->author;
                            $authorlist = implode(',', $currentcoauthors);

                            // Create and update author group with new and current coauthors.
                            $authorgroupcontroller->create_author_group($newcoauthors, $submission, $authorlist, $data, $settings);
                            $authorgroupcontroller->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist, $data, $settings);

                            // Update own author submission.
                            $submissioncontroller->update_author_submission($authorsubmission, $author, $authorlist);

                            // If default option is set then save this group as default group.
                            if (isset($data->asdefault) && $data->asdefault == 1) {
                                $authorgroupcontroller->set_author_default($authorlist, $userid, $courseid);
                            }

                            // If notifications are on then send notifications to all coauthors.
                            if ($notification) {
                                $this->send_notifications($author, $currentcoauthors, $deletecoauthors);
                            }
                        }

                        return true;
                    } else if (isset($data->defcoauthors) && $data->defcoauthors == 1) {
                        // Option "Use default co authors" - author perspective.
                        // There already is a submission, so the author group gets updated.

                        // Get default coauthors.
                        $defaultcoauthors = $authorgroupcontroller->get_default_coauthors($userid, $courseid);
                        $this->trigger_update_event($userid, $currentcoauthors, $defaultcoauthors, $submission);

                        // Distinguish between new coauthors, deleted coauthors, current coauthors.
                        $deletecoauthors = array_diff($currentcoauthors, $defaultcoauthors);
                        $newcoauthors = array_diff($defaultcoauthors, $currentcoauthors);
                        $updatecoauthors = array_diff($defaultcoauthors, $newcoauthors);
                        $currentcoauthors = $defaultcoauthors;

                        // Delete author group with deleted coauthors.
                        if (isset($settings->removesubmission) && $settings->removesubmission) {
                            $this->remove($submission);
                        }
                        $authorgroupcontroller->delete_author_group($deletecoauthors, $submission->assignment);

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $currentcoauthors);

                        // Create and update author group with new and current coauthors.
                        $authorgroupcontroller->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist, $data, $settings);
                        $authorgroupcontroller->create_author_group($newcoauthors, $submission, $authorlist, $data, $settings);

                        // Update own author submission.
                        $submissioncontroller->update_author_submission($authorsubmission, $author, $authorlist);

                        // If notifications are on then send notifications to all coauthors.
                        if ($notification) {
                            $this->send_notifications($author, $currentcoauthors, $deletecoauthors);
                        }

                        return true;
                    } else if (isset($data->nocoauthors) && $data->nocoauthors == 1) {
                        // Option "No co authors" - author perspective. Deletes the whole author group.

                        // Delete authorgroup.
                        if (isset($settings->removesubmission) && $settings->removesubmission) {
                            $this->remove($submission);
                        }
                        $authorgroupcontroller->delete_author_group($currentcoauthors, $submission->assignment);
                        $this->trigger_delete_event($userid, $currentcoauthors, $submission);
                        if ($notification) {
                            $this->send_notifications($userid, array(), $currentcoauthors);
                        }

                        $submissioncontroller->delete_author_submission($userid, $submission->assignment);
                        return true;
                    }
                } else {
                    if (isset($data->selcoauthors) && $data->selcoauthors == 1) {
                        // Option "Select new co authors" - coauthor perspective.
                        $updatecoauthors = array_diff($currentcoauthors, array($userid));
                        $updateauthor = array($authorsubmission->author);

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $updatecoauthors);

                        // Update or delete remaining author group.
                        if ($authorlist != '') {
                            $authorgroupcontroller->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist, $data, $settings);
                            $authorgroupcontroller->update_author_group($updateauthor, $submission->assignment, $author, $authorlist, $data, $settings);
                        } else {
                            // Delete author group if there is no one remaining, e.g. when there were only 2 members.
                            if (isset($settings->removesubmission) && $settings->removesubmission) {
                                $this->remove($submission);
                            }
                            $authorgroupcontroller->delete_author_group($updatecoauthors, $submission->assignment);
                            $authorgroupcontroller->delete_author_group($updateauthor, $submission->assignment);
                        }

                        $selectedcoauthors = utilities::get_selected_coauthors($data);
                        // Because the co author removes him/herself from the old group and creates a new one, we need to trigger both events.
                        $this->trigger_create_event($selectedcoauthors, $submission);
                        $this->trigger_update_event($author, $currentcoauthors, $updatecoauthors, $submission);

                        // Delete author group and submission.
                        if (count($selectedcoauthors) == 0) {
                            $deletecoauthors = $currentcoauthors;
                            if (isset($settings->removesubmission) && $settings->removesubmission) {
                                $this->remove($submission);
                            }
                            $authorgroupcontroller->delete_author_group($deletecoauthors, $submission->assignment);
                            $submissioncontroller->delete_author_submission($userid, $submission->assignment);

                            return true;
                        }

                        $author = $userid;
                        $authorlist = implode(',', $selectedcoauthors);

                        // Create new author group.
                        $authorgroupcontroller->create_author_group($selectedcoauthors, $submission, $authorlist, $data, $settings);

                        // Update own author submission.
                        $submissioncontroller->update_author_submission($authorsubmission, $author, $authorlist);

                        // If notifications are on then send notifications to all new, current and deleted coauthors.
                        if ($notification) {
                            $this->send_notifications($author, $selectedcoauthors);
                        }

                        // If default option is set then save this group as default group.
                        if (isset($data->asdefault) && $data->asdefault == 1) {
                            $authorgroupcontroller->set_author_default($authorlist, $userid, $courseid);
                        }
                        return true;

                    } else if (isset($data->defcoauthors) && $data->defcoauthors == 1) {
                        // Option "Use default co authors" - co author perspective.
                        $updatecoauthors = array_diff($currentcoauthors, array($userid));
                        $updateauthor = array($authorsubmission->author);

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $updatecoauthors);

                        // Update or delete remaining authorgroup.
                        if ($authorlist != '') {
                            $authorgroupcontroller->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist, $data, $settings);
                            $authorgroupcontroller->update_author_group($updateauthor, $submission->assignment, $author, $authorlist, $data, $settings);
                            $this->trigger_update_event($author, $currentcoauthors, $updatecoauthors, $submission);
                        } else {
                            $authorgroupcontroller->delete_author_group($updatecoauthors, $submission->assignment);
                            $authorgroupcontroller->delete_author_group($updateauthor, $submission->assignment);
                            if (isset($settings->removesubmission) && $settings->removesubmission) {
                                $this->remove($submission);
                            }
                            $this->trigger_delete_event($author, $currentcoauthors, $submission);
                        }

                        // Get default coauthors.
                        $defaultcoauthors = $authorgroupcontroller->get_default_coauthors($userid, $courseid);
                        $author = $userid;
                        $authorlist = implode(',', $defaultcoauthors);

                        // Create new authorgroup by default.
                        $authorgroupcontroller->create_author_group($defaultcoauthors, $submission, $authorlist, $data, $settings);
                        $this->trigger_create_event($defaultcoauthors, $submission);

                        // Update own authorsubmission.
                        $submissioncontroller->update_author_submission($authorsubmission, $author, $authorlist);

                        // If notifications are on then send notifications to all coauthors.
                        $currentcoauthors = $defaultcoauthors;
                        if ($notification) {
                            $this->send_notifications($author, $currentcoauthors);
                        }
                        return true;
                    } else if (isset($data->nocoauthors) && $data->nocoauthors == 1) {
                        // Option "No co-authors" - coauthor perspective.
                        // This only removes the co author from the submission, the author group remains intact.

                        $updatecoauthors = array_diff($currentcoauthors, array($userid));
                        $updateauthor = array($authorsubmission->author);

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $updatecoauthors);

                        // Update current author group.
                        $authorgroupcontroller->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist, $data, $settings);
                        $authorgroupcontroller->update_author_group($updateauthor, $submission->assignment, $author, $authorlist, $data, $settings);
                        $this->trigger_update_event($author, $currentcoauthors, $updatecoauthors, $submission);

                        // Delete own author submission.
                        $submissioncontroller->delete_author_submission($userid, $submission->assignment);

                        return true;
                    }
                }

            } else { // Create new author submission.

                if (isset($data->selcoauthors) && $data->selcoauthors == 1) {
                    // Option "select co authors" when when first submitting in a new submission.
                    // There is no authorgroup yet so this is the author perspective.

                    // Get new coauthors.
                    $currentcoauthors = utilities::get_selected_coauthors($data);

                    if (count($currentcoauthors) == 0) {
                        return true;
                    }

                    $author = $userid;
                    $authorlist = implode(',', $currentcoauthors);

                    // Create new authorgroup.
                    $authorgroupcontroller->create_author_group($currentcoauthors, $submission, $authorlist, $data, $settings);
                    $submissioncontroller->create_author_submission($submission->assignment, $submission->id, $author, $authorlist);
                    $this->trigger_create_event($currentcoauthors, $submission);

                    // If notifications are on then send notifications to all coauthors.
                    if ($notification) {
                        $this->send_notifications($author, $currentcoauthors);
                    }

                    // If default option is set then save this group as default group.
                    if (isset($data->asdefault) && $data->asdefault == 1) {
                        $authorgroupcontroller->set_author_default($authorlist, $userid, $courseid);
                    }
                    return true;

                } else if (isset($data->defcoauthors) && $data->defcoauthors == 1) {
                    // Option "Use default co-authors in this course" when first submitting in a new submission.
                    // There is no authorgroup yet so this is the author perspective.
                    $currentcoauthors = $authorgroupcontroller->get_default_coauthors($userid, $courseid);

                    $author = $userid;
                    $authorlist = implode(',', $currentcoauthors);

                    // Create new authorgroup.
                    $authorgroupcontroller->create_author_group($currentcoauthors, $submission, $authorlist, $data, $settings);
                    $submissioncontroller->create_author_submission($submission->assignment, $submission->id, $author, $authorlist);
                    $this->trigger_create_event($currentcoauthors, $submission);

                    // If notifications are on then send notifications to all coauthors.
                    if ($notification) {
                        $this->send_notifications($author, $currentcoauthors);
                    }

                    return true;
                } else if (isset($data->nocoauthors) && $data->nocoauthors == 1) {
                    // No coauthors, so nothing to create.
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * Display the author and coauthors
     *
     * @param stdClass $submission
     * @param bool $showviewlink
     *            - If the summary has been truncated set this to true
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        $submissioncontroller = new submission_controller();
        $assignment = $this->assignment->get_instance()->id;
        $authorsubmission = $submissioncontroller->get_author_submission($assignment, $submission->id);
        // Always show the view link.
        $showviewlink = false;

        if ($authorsubmission) {
            $author = utilities::get_author_array($authorsubmission->author, $this->assignment->get_course()->id, true);
            $coauthors = utilities::get_author_array($authorsubmission->authorlist, $this->assignment->get_course()->id, true);

            return $this->get_summary($author, $coauthors);
        }
        return get_string('summary_nocoauthors', 'assignsubmission_author');
    }

    /**
     * Display the author and coauthors
     *
     * @param stdClass $submission
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function view(stdClass $submission) {
        $showviewlink = true;
        return $this->view_summary($submission, $showviewlink);
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission
     *            The new submission
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public function format_for_log(stdClass $submission) {
        $submissioncontroller = new submission_controller();
        // Format the info for each submission plugin (will be logged).
        $authorsubmission = $submissioncontroller->get_author_submission($this->assignment->get_instance()->id, $submission->id);
        $authorloginfo = '';

        if ($authorsubmission) {
            $authorloginfo .= $authorsubmission->author . ',';
            $authorloginfo .= $authorsubmission->authorlist;
        }
        return $authorloginfo;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    public function delete_instance() {
        global $DB;
        $DB->delete_records('assignsubmission_author', array(
            'assignment' => $this->assignment->get_instance()->id
        ));

        return true;
    }

    /**
     * No authors are set
     *
     * @param stdClass $submission
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    public function is_empty(stdClass $submission) {
        $submissioncontroller = new submission_controller();
        return ($submissioncontroller->get_author_submission($this->assignment->get_instance()->id, $submission->id) == false);
    }

    /**
     * Creates summary string with author and coauthors
     *
     * @param array $author
     * @param array $coauthors
     * @return string
     * @throws coding_exception
     */
    public function get_summary($author, $coauthors) {
        $summary = get_string('summary_author', 'assignsubmission_author');
        $summary .= ': ';
        $summary .= implode(',', $author);
        $summary .= '<br>';
        $summary .= get_string('summary_coauthors', 'assignsubmission_author');
        $summary .= ': ';
        $summary .= implode(', ', $coauthors);
        return $summary;
    }

    /**
     * Send notifications to all coauthors when they are added to a co author group.
     * And send notifications to deleted co authors.
     *
     * @param int $author
     * @param int[] $coauthors
     * @param int[] $removedauthors array of removed authors
     * @throws coding_exception
     * @throws dml_exception
     */
    private function send_notifications($author, $coauthors, $removedauthors = array()) {
        global $CFG, $USER;
        $course = $this->assignment->get_course();
        $a = new stdClass();
        $a->courseurl = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
        $a->coursename = $course->fullname;
        $a->username = fullname(core_user::get_user($author));
        $a->userurl = $CFG->wwwroot . '/user/profile.php?id=' . $author;
        $a->assignmentname = format_string($this->assignment->get_instance()->name, true,
                array('context' => $this->assignment->get_context()));
        $a->assignmenturl = $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->assignment->get_course_module()->id;
        $subject = get_string('subject', 'assignsubmission_author', $a);

        // Iterate over both arrays at the same time and differentiate later to not duplicate code.
        foreach (array_merge($coauthors, $removedauthors) as $userid) {
            $userto = core_user::get_user($userid);
            $eventdata = new \core\message\message;
            $eventdata->modulename = 'assign';
            $eventdata->userfrom = $USER;
            $eventdata->userto = $userto;
            $eventdata->subject = $subject;
            if (in_array($userid, $coauthors)) { // Distinct message if the user was added or removed.
                $message = get_string('message', 'assignsubmission_author', $a);
            } else {
                $message = get_string('message_deleted', 'assignsubmission_author', $a);
            }
            $eventdata->fullmessage = $message;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml = $message;
            $eventdata->smallmessage = $subject;
            $eventdata->name = 'assign_notification';
            $eventdata->component = 'mod_assign';
            $eventdata->notification = 1;
            $eventdata->contexturl = $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->assignment->get_course_module()->id;
            $eventdata->contexturlname = format_string($this->assignment->get_instance()->name, true, array(
                    'context' => $this->assignment->get_context()
            ));
            message_send($eventdata);
        }

    }

    /**
     * Remove files from the submissions of all other group members.
     * Works like
     * @see assign::remove_submission()
     *
     * @param stdClass $submission The submission
     * @return boolean
     */
    public function remove(stdClass $submission) {
        global $USER;

        $submissioncontroller = new submission_controller();
        $assignmentid = $this->assignment->get_instance()->id;

        if ($submission) {
            $authorsubmission = $submissioncontroller->get_author_submission($assignmentid, $submission->id);
            if ($authorsubmission != false) { // If co authors are even used.
                $allauthorids = explode(',', $authorsubmission->author . ',' . $authorsubmission->authorlist);

                // Also delete this submission for every other author.
                foreach ($allauthorids as $authorid) {
                    if ($authorid != $USER->id) { // The submission was already deleted for this user.
                        $submissiontodelete = $submissioncontroller->get_submission($authorid, $assignmentid);
                        foreach ($this->assignment->get_submission_plugins() as $plugin) {
                            if ($plugin->is_enabled() && $plugin->is_visible() && $plugin->get_type() != 'author' && $submissiontodelete != false) {
                                $plugin->remove($submissiontodelete);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Delete submission record
     *
     * @param int $id
     * @return
     */
    private function delete_submission($id) {
        global $DB;
        return $DB->delete_record('assign_submission', array(
                'id' => $id
        ));
    }

    /**
     * Writes to the logs that an author group has been created.
     * @param array $coauthors of user ids
     * @param stdClass $submission from the 'assign_submission' table
     */
    private function trigger_create_event($coauthors, $submission) {
        // Trigger the event so it is added to the logs.
        $params = array(
                'context' => \context_module::instance($this->assignment->get_course_module()->id),
                'courseid' => $this->assignment->get_course()->id,
                'objectid' => $submission->id,
                'other' => array(
                        'coauthors' => $coauthors
                )
        );
        $event = \assignsubmission_author\event\author_group_created::create($params);
        $event->set_assign($this->assignment);
        $event->trigger();
    }

    /**
     * Writes to the logs that an author group has been updated.
     * @param int $authorid
     * @param array $oldcoauthors of user ids
     * @param array $newcoauthors of user ids
     * @param stdClass $submission from the 'assign_submission' table
     */
    private function trigger_update_event($authorid, $oldcoauthors, $newcoauthors, $submission) {
        $params = array(
                'context' => \context_module::instance($this->assignment->get_course_module()->id),
                'courseid' => $this->assignment->get_course()->id,
                'objectid' => $submission->id,
                'other' => array(
                        'authorid' => $authorid,
                        'oldcoauthors' => $oldcoauthors,
                        'newcoauthors' => $newcoauthors
                )
        );
        $event = \assignsubmission_author\event\author_group_updated::create($params);
        $event->set_assign($this->assignment);
        $event->trigger();
    }

    /**
     * Writes to the logs that an author group has been deleted.
     * @param int $authorid
     * @param array $coauthors of user ids
     * @param stdClass $submission from the 'assign_submission' table
     */
    private function trigger_delete_event($authorid, $oldcoauthors, $submission) {
        $params = array(
                'context' => \context_module::instance($this->assignment->get_course_module()->id),
                'courseid' => $this->assignment->get_course()->id,
                'objectid' => $submission->id,
                'other' => array(
                        'authorid' => $authorid,
                        'oldcoauthors' => $oldcoauthors
                )
        );
        $event = \assignsubmission_author\event\author_group_deleted::create($params);
        $event->set_assign($this->assignment);
        $event->trigger();
    }

}


