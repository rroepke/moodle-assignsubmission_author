<?php
use core\event\course_updated;

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

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
/*
 * require_once($CFG->dirroot.'/'.$CFG->admin.'/user/lib.php');
 * require_once($CFG->dirroot.'/'.$CFG->admin.'/user/user_bulk_forms.php'); require_once($CFG->dirroot . '/user/selector/lib.php');
 * require_once($CFG->dirroot . '/course/lib.php'); require_once($CFG->libdir . '/filelib.php');
 */
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * library class for author submission plugin extending submission plugin base class
 *
 * @package assignsubmission_author
 * @copyright 2013 Rene Roepke
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_author extends assign_submission_plugin
{

    /**
     * Get the name of the author submission plugin
     *
     * @return string
     */
    public function get_name()
    {
        return get_string('author', 'assignsubmission_author');
    }

    /**
     * Get the default setting for author submission plugin
     *
     * @param MoodleQuickForm $mform
     *            The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform)
    {
        global $CFG, $COURSE;
        // get config infos
        $defaultmaxauthors = $this->get_config('maxauthors');
        $defaultgroupsused = $this->get_config('groupsused');
        $defaultingroupsonly = $this->get_config('ingroupsonly');
        $defaultnotification = $this->get_config('notification');

        // generate maxauthors setting
        $options = array();
        for ($i = 1; $i <= ASSIGNSUBMISSIONAUTHOR_MAXAUTHORS; $i++) {
            $options[$i] = $i;
        }
        // display maxauthors setting
        $name = get_string('maxauthors', 'assignsubmission_author');
        $mform->addElement('select', 'assignsubmissionauthor_maxauthors', $name, $options);
        $mform->addHelpButton('assignsubmissionauthor_maxauthors', 'maxauthors', 'assignsubmission_author');
        $mform->setDefault('assignsubmissionauthor_maxauthors', $defaultmaxauthors);
        $mform->disabledIf('assignsubmissionauthor_maxauthors', 'assignsubmission_author_enabled', 'notchecked');

        // display notification setting
        $name = get_string('notification', 'assignsubmission_author');
        $mform->addElement('checkbox', 'assignsubmissionauthor_notification', $name, '', 0);
        $mform->setDefault('assignsubmissionauthor_notification', $defaultnotification);
        $mform->addHelpButton('assignsubmissionauthor_notification', 'notification', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_notification', 'assignsubmission_author_enabled', 'notchecked');

        // display groupsused setting
        $name = get_string('groupsused', 'assignsubmission_author');
        $mform->addElement('checkbox', 'assignsubmissionauthor_groupsused', $name, '', 0);
        $mform->setDefault('assignsubmissionauthor_groupsused', $defaultgroupsused);
        $mform->addHelpButton('assignsubmissionauthor_groupsused', 'groupsused', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_groupsused', 'assignsubmission_author_enabled', 'notchecked');

        // display ingroupsonly setting
        $name = get_string('ingroupsonly', 'assignsubmission_author');
        $mform->addElement('checkbox', 'assignsubmissionauthor_ingroupsonly', $name, '', 0);
        $mform->setDefault('assignsubmissionauthor_ingroupsonly', $defaultingroupsonly);
        $mform->addHelpButton('assignsubmissionauthor_ingroupsonly', 'ingroupsonly', 'assignsubmission_author');
        $mform->disabledIf('assignsubmissionauthor_ingroupsonly', 'assignsubmissionauthor_groupsused', 'notchecked');
        $mform->disabledIf('assignsubmissionauthor_ingroupsonly', 'assignsubmission_author_enabled', 'notchecked');
    }

    /**
     * Save the settings for author submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data)
    {
        // set config info
        $this->set_config('maxauthors', isset($data->assignsubmissionauthor_maxauthors) ? $data->assignsubmissionauthor_maxauthors : 0);
        $this->set_config('ingroupsonly', (isset($data->assignsubmissionauthor_groupsused)
            && $data->assignsubmissionauthor_groupsused == 1) ? (isset($data->assignsubmissionauthor_ingroupsonly) ? $data->assignsubmissionauthor_ingroupsonly : 0) : 0);
        $this->set_config('notification', isset($data->assignsubmissionauthor_notification) ? $data->assignsubmissionauthor_notification : 0);
        $this->set_config('groupsused', isset($data->assignsubmissionauthor_groupsused) ? $data->assignsubmissionauthor_groupsused : 0);
        return true;
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission
     *            can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data)
    {
        global $USER, $CFG, $COURSE;

        // get maxauthors config info
        $maxauthors = $this->get_config('maxauthors');

        // if maxauthors <= 1 then return comment and no more content
        if ($maxauthors <= 1) {
            $mform->addElement('static', '', '', get_string('oneauthoronly', 'assignsubmission_author'), 1);
            return true;
        }

        // if team assignment is activated then return comment and no more content
        if ($this->assignment->get_instance()->teamsubmission == 1) {
            $mform->addElement('static', '', '', get_string('noteamsubmission', 'assignsubmission_author'), 1);
            return true;
        }

        // start generating content
        $courseid = $COURSE->id;
        $userid = $USER->id;
        $selectedauthors = array();
        $alreadyinauthorgroup = false;
        $assignment = $this->assignment->get_instance()->id;

        // if authorsubmission then get it
        if ($submission) {
            $authorsubmission = $this->get_author_submission($assignment, $submission->id);
            if ($authorsubmission) {
                $alreadyinauthorgroup = $authorsubmission->author != $userid;
                $selectedauthors = $this->get_author_array($authorsubmission->author . ',' . $authorsubmission->authorlist, true);
                $origauthor = $this->get_author_array($authorsubmission->author, true);
            }
        }

        // get ingroupsonly config info
        $ingroupsonly = $this->get_config('ingroupsonly');

        // get possible coauthors
        $possiblecoauthors = $this->get_possible_co_authors($courseid, $userid, $ingroupsonly, $assignment);

        $userarr[$userid] = '';

        // get author default
        $authordefaultsubmission = $this->get_author_default($userid, $courseid);

        if ($authordefaultsubmission) {
            $default = $authordefaultsubmission->coauthors;
            $array = $this->get_author_array($default, true);
            $array = array_diff_key($array, $userarr);
            $showdefault = $this->is_default_usable($array, $possiblecoauthors, $maxauthors);
            $default = implode(', ', $array);
        }

        // get preselected authors
        $selectedauthors = array_diff_key($selectedauthors, $userarr);

        // set reactive behaviour for all options
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

        // if already in authorgroup then 4th option
        if ($alreadyinauthorgroup) {
            $mform->setDefault('groupcoauthors', 'checked');
            $mform->addElement('checkbox', 'groupcoauthors', '', get_string('choose_group', 'assignsubmission_author'), 1);
            $mform->addElement('static', 'group2coauthors', get_string('group', 'assignsubmission_author'), $this->get_summary($origauthor, array_diff_key($selectedauthors, $origauthor)), null);
            $mform->addElement('static', '', '', '');
        } else {
            $mform->setDefault('selcoauthors', 'checked');
        }

        // display 1st option to select co authors
        $mform->addElement('checkbox', 'selcoauthors', '', get_string('choose_coauthors', 'assignsubmission_author'), 1);

        if (count($possiblecoauthors) != 0) {
            // define content of choice boxes
            $achoices = array();
            $achoices[0] = get_string('choose', 'assignsubmission_author');
            $achoices = $achoices + $possiblecoauthors;

            // generate as many choice boxes as necessary
            $objs = array();
            for ($i = 0; $i < $maxauthors - 1; ++$i) {
                $objs[$i] = &$mform->createElement('select', 'coauthors[' . $i . ']', '', $achoices, null);
            }

            // add elements
            $grp = &$mform->addElement('group', 'coauthorselection', get_string('coauthors', 'assignsubmission_author'), $objs, ' ', false);
            $mform->disabledIf('coauthorselection', 'selcoauthors', 'notchecked');
            $mform->addElement('checkbox', 'asdefault', ' ', get_string('asdefault', 'assignsubmission_author'));
            $mform->disabledIf('asdefault', 'selcoauthors', 'notchecked');

            // set preselected coauthors
            if ($alreadyinauthorgroup) {
                $i = 0;
                foreach ($selectedauthors as $key => $value) {
                    $mform->setDefault('coauthors[' . $i . ']', 0);
                    $i++;
                }
            } else {
                $i = 0;
                foreach ($selectedauthors as $key => $value) {
                    $mform->setDefault('coauthors[' . $i . ']', $key);
                    $i++;
                }
            }
        } else {
            $mform->addElement('static', '', '', get_string('nopossiblecoauthors', 'assignsubmission_author'), 1);
        }

        $mform->addElement('static', '', '', '');

        // if default then display 2nd option for default
        if (isset($showdefault) && $showdefault && isset($default)) {
            $mform->addElement('checkbox', 'defcoauthors', '', get_string('choose_defaultcoauthors', 'assignsubmission_author'), 1);
            $mform->addElement('static', 'defaultcoauthors', get_string('defaultcoauthors', 'assignsubmission_author'), $default, 1);
            $mform->addElement('static', '', '', '');
        }

        // display 3rd option for no coauthors
        $mform->addElement('checkbox', 'nocoauthors', '', get_string('choose_nocoauthors', 'assignsubmission_author'), 1);

        return true;
    }

    /**
     * Checks if default coauthors can be used
     *
     * @param array $defaults
     * @param array $possibles
     * @return boolean true if default coauthors can be used
     */
    private function is_default_usable($defaults, $possibles, $count)
    {
        if (count($defaults) > $count - 1)
            return false;
        foreach ($defaults as $author => $value) {
            if (!array_key_exists($author, $possibles)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Look up the plugin status 'enabled'
     *
     * @param string $name
     *            name of the plugin
     * @param string $subtype
     *            subtype of the plugin
     * @return boolean true if plugin is enabled
     */
    private function is_plugin_enabled($name, $subtype)
    {
        global $DB;

        $rec = $DB->get_record('assign_plugin_config', array(
            'assignment' => $this->assignment->get_instance()->id,
            'subtype' => $subtype,
            'plugin' => $name,
            'name' => 'enabled'
        ));

        if ($rec) {
            return $rec->value == 1;
        }

        return false;
    }

    /**
     * Set onlinetext submission records for all coauthors
     *
     * @param int[] $coauthors
     * @param stdClass $data
     */
    private function set_onlinetext_submission_for_coauthors($coauthors, $data)
    {
        global $DB;

        // imitate behaviour of the onlinetext editor plugin for submission
        if (isset($data->onlinetext_editor)) {
            $assignment = $this->assignment->get_instance()->id;
            $text = $data->onlinetext_editor['text'];
            $format = $data->onlinetext_editor['format'];
            foreach ($coauthors as $coauthor) {
                $submission = $this->get_submission($coauthor, $assignment);
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

    /**
     * Save data to the database
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data)
    {
        global $USER, $DB, $COURSE;

        // if team submission is activated no submission is possible
        if ($this->assignment->get_instance()->teamsubmission == 1) {
            $this->set_error(get_string('error_teamsubmission', 'assignsubmission_author'));
            return false;
        }

        // get notification config info
        $notification = $this->get_config('notification');

        $userid = $USER->id;
        $courseid = $COURSE->id;
        $assignment = $this->assignment->get_instance()->id;

        // if already submission then update else create
        $currentcoauthors = array();
        if ($submission) {
            // if already author submission then update else create
            $authorsubmission = $this->get_author_submission($assignment, $submission->id);

            if ($authorsubmission) {
                // UPDATE AUTHORSUBMISSION

                // get current coauthors as array
                $currentcoauthors = explode(',', $authorsubmission->authorlist);

                if (isset($data->groupcoauthors) && $data->groupcoauthors == 1) {
                    // 4th option - coauthor perspective
                    $currentcoauthors = explode(',', $authorsubmission->author . ',' . $authorsubmission->authorlist);

                    // update onlinetext submission
                    if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                        $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                    }

                    return true;
                } else if ($authorsubmission->author == $userid) {
                    if (isset($data->selcoauthors) && $data->selcoauthors == 1) {
                        // 1st option - author perspective

                        // get new selected coauthors
                        $selectedcoauthors = $this->get_selected_coauthors($data);

                        // if no new selected coauthors then delete current authorgroup else just update
                        if (count($selectedcoauthors) == 0) {
                            $deletecoauthors = $currentcoauthors;

                            $this->delete_author_group($deletecoauthors, $submission->assignment);

                            $this->delete_author_submission($userid, $submission->assignment);
                        } else {

                            // distinguish between new coauthors, deleted coauthors, current coauthors
                            $deletecoauthors = array_diff($currentcoauthors, $selectedcoauthors);
                            $newcoauthors = array_diff($selectedcoauthors, $currentcoauthors);
                            $updatecoauthors = array_diff($selectedcoauthors, $newcoauthors);
                            $currentcoauthors = $selectedcoauthors;

                            // delete author group with deleted coauthors
                            $this->delete_author_group($deletecoauthors, $submission->assignment);

                            $author = $authorsubmission->author;
                            $authorlist = implode(',', $currentcoauthors);

                            // create and update author group with new and current coauthors
                            $this->create_author_group($newcoauthors, $submission, $authorlist);
                            $this->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist);

                            // update own author submission
                            $this->update_author_submission($authorsubmission, $author, $authorlist);

                            // if onlinetext plugin is enabled then update/create submissions
                            if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                                $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                            }

                            // if default option is set then save this group as default group
                            if (isset($data->asdefault) && $data->asdefault == 1) {
                                $this->set_author_default($authorlist, $userid, $courseid);
                            }

                            // if notifications are on then send notifications to all new and currend coauthors
                            if ($notification) {
                                $this->send_notifications($author, $currentcoauthors);
                            }
                        }

                        return true;
                    } else if (isset($data->defcoauthors) && $data->defcoauthors == 1) {
                        // 2nd option - author perspective

                        // get default coauthors
                        $defaultcoauthors = $this->get_default_coauthors($userid, $courseid);

                        // distinguish between new coauthors, deleted coauthors, current coauthors
                        $deletecoauthors = array_diff($currentcoauthors, $defaultcoauthors);
                        $newcoauthors = array_diff($defaultcoauthors, $currentcoauthors);
                        $updatecoauthors = array_diff($defaultcoauthors, $newcoauthors);

                        $currentcoauthors = $defaultcoauthors;

                        // delete author group with deleted coauthors
                        $this->delete_author_group($deletecoauthors, $submission->assignment);

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $currentcoauthors);

                        // create and update author group with new and current coauthors
                        $this->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist);
                        $this->create_author_group($newcoauthors, $submission, $authorlist);

                        // update own author submission
                        $this->update_author_submission($authorsubmission, $author, $authorlist);

                        // if onlinetext plugin is enabled then update/create submissions
                        if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                            $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                        }

                        // if notifications are on then send notifications to all new and currend coauthors
                        if ($notification) {
                            $this->send_notifications($author, $currentcoauthors);
                        }

                        return true;
                    } else if (isset($data->nocoauthors) && $data->nocoauthors == 1) {
                        // 3rd option - author perspective

                        $deletecoauthors = $currentcoauthors;

                        // delete authorgroup
                        $this->delete_author_group($deletecoauthors, $submission->assignment);

                        $this->delete_author_submission($userid, $submission->assignment);
                        return true;
                    }
                } else {
                    if (isset($data->selcoauthors) && $data->selcoauthors == 1) {
                        // 1st option - coauthor perspective
                        $userarr = array(
                            $userid
                        );

                        $updatecoauthors = array_diff($currentcoauthors, $userarr);

                        $updateauthor = array(
                            $authorsubmission->author
                        );

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $updatecoauthors);

                        // update or delete remaining author group
                        if ($authorlist != '') {
                            $this->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist);
                            $this->update_author_group($updateauthor, $submission->assignment, $author, $authorlist);
                        } else {
                            $this->delete_author_group($updatecoauthors, $submission->assignment);
                            $this->delete_author_group($updateauthor, $submission->assignment);
                        }
                        $selectedcoauthors = $this->get_selected_coauthors($data);

                        // delete author group and submission
                        if (count($selectedcoauthors) == 0) {

                            $deletecoauthors = $currentcoauthors;
                            $this->delete_author_group($deletecoauthors, $submission->assignment);
                            $this->delete_author_submission($userid, $submission->assignment);

                            return true;
                        }

                        $author = $userid;
                        $authorlist = implode(',', $selectedcoauthors);

                        // create new author group
                        $this->create_author_group($selectedcoauthors, $submission, $authorlist);

                        $currentcoauthors = $selectedcoauthors;

                        // update own author submission
                        $this->update_author_submission($authorsubmission, $author, $authorlist);

                        // if onlinetext plugin is enabled then update/create submissions
                        if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                            $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                        }

                        // if notifications are on then send notifications to all new and currend coauthors
                        if ($notification) {
                            $this->send_notifications($author, $currentcoauthors);
                        }

                        // if default option is set then save this group as default group
                        if (isset($data->asdefault) && $data->asdefault == 1) {
                            $this->set_author_default($authorlist, $userid, $courseid);
                        }
                        return true;
                    } else if (isset($data->defcoauthors) && $data->defcoauthors == 1) {
                        // 2nd option - coauthor perspective

                        $userarr = array(
                            $userid
                        );

                        $updatecoauthors = array_diff($currentcoauthors, $userarr);

                        $updateauthor = array(
                            $authorsubmission->author
                        );

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $updatecoauthors);

                        // update or delete remaining authorgroup
                        if ($authorlist != '') {
                            $this->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist);
                            $this->update_author_group($updateauthor, $submission->assignment, $author, $authorlist);
                        } else {
                            $this->delete_author_group($updatecoauthors, $submission->assignment);
                            $this->delete_author_group($updateauthor, $submission->assignment);
                        }

                        // get default coauthors
                        $defaultcoauthors = $this->get_default_coauthors($userid, $courseid);

                        $author = $userid;
                        $authorlist = implode(',', $defaultcoauthors);

                        // create new authorgroup by default
                        $this->create_author_group($defaultcoauthors, $submission, $authorlist);

                        $currentcoauthors = $defaultcoauthors;

                        // update own authorsubmission
                        $this->update_author_submission($authorsubmission, $author, $authorlist);

                        // if onlinetext plugin is enabled then update/create submissions
                        if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                            $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                        }

                        // if notifications are on then send notifications to all new and currend coauthors
                        if ($notification) {
                            $this->send_notifications($author, $currentcoauthors);
                        }
                        return true;
                    } else if (isset($data->nocoauthors) && $data->nocoauthors == 1) {
                        // 3rd option - coauthor perspective

                        $userarr = array(
                            $userid
                        );

                        $updatecoauthors = array_diff($currentcoauthors, $userarr);

                        $updateauthor = array(
                            $authorsubmission->author
                        );

                        $author = $authorsubmission->author;
                        $authorlist = implode(',', $updatecoauthors);

                        // update current author group
                        $this->update_author_group($updatecoauthors, $submission->assignment, $author, $authorlist);
                        $this->update_author_group($updateauthor, $submission->assignment, $author, $authorlist);

                        // delete own author submission
                        $this->delete_author_submission($userid, $submission->assignment);

                        return true;
                    }
                }

            } else {

                if (isset($data->selcoauthors) && $data->selcoauthors == 1) {

                    // get new coauthors
                    $currentcoauthors = $this->get_selected_coauthors($data);

                    if (count($currentcoauthors) == 0) {
                        return true;
                    }

                    $author = $userid;
                    $authorlist = implode(',', $currentcoauthors);

                    // create new authorgroup
                    $this->create_author_group($currentcoauthors, $submission, $authorlist);
                    $this->create_author_submission($submission->assignment, $submission->id, $author, $authorlist);

                    // if onlinetext plugin is enabled then update/create submissions
                    if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                        $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                    }

                    // if notifications are on then send notifications to all new and currend coauthors
                    if ($notification) {
                        $this->send_notifications($author, $currentcoauthors);
                    }

                    // if default option is set then save this group as default group
                    if (isset($data->asdefault) && $data->asdefault == 1) {
                        $this->set_author_default($authorlist, $userid, $courseid);
                    }
                    return true;

                } else if (isset($data->defcoauthors) && $data->defcoauthors == 1) {
                    // 2nd option - new authorgroup like the default group

                    $currentcoauthors = $this->get_default_coauthors($userid, $courseid);

                    $author = $userid;
                    $authorlist = implode(',', $currentcoauthors);

                    // create new authorgroup
                    $this->create_author_group($currentcoauthors, $submission, $authorlist);
                    $this->create_author_submission($submission->assignment, $submission->id, $author, $authorlist);

                    // if onlinetext plugin is enabled then update/create submissions
                    if ($this->is_plugin_enabled(ASSIGNSUBMISSION_ONLINETEXT, 'assignsubmission')) {
                        $this->set_onlinetext_submission_for_coauthors($currentcoauthors, $data);
                    }

                    // if notifications are on then send notifications to all new and currend coauthors
                    if ($notification) {
                        $this->send_notifications($author, $currentcoauthors);
                    }

                    return true;
                } else if (isset($data->nocoauthors) && $data->nocoauthors == 1) {
                    // no coauthors, so nothing to create
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * Send notifications to all coauthors
     *
     * @param int $author
     * @param int[] $coauthors
     */
    private function send_notifications($author, $coauthors)
    {
        global $CFG, $USER;
        $user = core_user::get_user($author);
        $course = $this->assignment->get_course();
        $a = new stdClass();
        $a->courseurl = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
        $a->coursename = $course->fullname;
        $a->username = fullname(core_user::get_user($author));
        $a->assignmentname = format_string($this->assignment->get_instance()->name, true, array('context' => $this->assignment->get_context()));
        $a->assignmenturl = $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->assignment->get_course_module()->id;
        $subject = get_string('subject', 'assignsubmission_author', $a);
        $message = $subject . ': ' . get_string('message', 'assignsubmission_author', $a);
        foreach ($coauthors as $coauthor) {
            $userto = core_user::get_user($coauthor);
            $eventdata = new stdClass();
            $eventdata->modulename = 'assign';
            $eventdata->userfrom = $USER;
            $eventdata->userto = $userto;
            $eventdata->subject = $subject;
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
            $eventdata = new stdClass();
            $eventdata->modulename       = 'assign';
            $eventdata->userfrom         = $USER;
            $eventdata->userto           = $userto;
            $eventdata->subject          = $subject;
            $eventdata->fullmessage      = $message;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml  = $message;
            $eventdata->smallmessage     = $subject;

            $eventdata->name            = 'assign_notification';
            $eventdata->component       = 'mod_assign';
            $eventdata->notification    = 1;
            $eventdata->contexturl      = $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->assignment->get_course_module()->id;
            $eventdata->contexturlname = format_string($this->assignment->get_instance()->name, true, array(
                'context' => $this->assignment->get_context()
            ));

            message_send($eventdata);
        }
    }

    /**
     * Get the author submission record of a submission for an assignment
     *
     * @param int $assignment
     * @param int $submission
     * @return Ambigous <mixed, stdClass, false, boolean>
     */
    private function get_author_submission($assignment, $submission)
    {
        global $DB;
        return $DB->get_record('assignsubmission_author', array(
            'assignment' => $assignment,
            'submission' => $submission
        ));
    }

    /**
     * Get the author default record of a user in course
     *
     * @param int $user
     * @param int $course
     * @return Ambigous <mixed, stdClass, false, boolean>
     */
    private function get_author_default($user, $course)
    {
        global $DB;
        return $DB->get_record('assign_author_default', array(
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
     */
    private function get_default_coauthors($userid, $courseid)
    {
        global $DB;
        $rec = $this->get_author_default($userid, $courseid);
        return explode(',', $rec->coauthors);
    }

    /**
     * Get the author ids and names as an array
     *
     * @param string $ids
     * @return array
     */
    private function get_author_array($ids, $link = false)
    {
        global $DB, $CFG;
        if ($ids != '') {
            $ids2 = explode(',', $ids);
            $selectedauthors = array();
            foreach ($ids2 as $id) {
                $userrec = $DB->get_record('user', array(
                    'id' => $id
                ));
                if ($link) {
                    $url = $CFG->wwwroot . '/user/view.php?id=' . $userrec->id . '&course=' . $this->assignment->get_course()->id;
                    $selectedauthors[$userrec->id] = "<a href='" . $url . "'>" . fullname($userrec) . "</a>";
                } else {
                    $selectedauthors[$userrec->id] = fullname($userrec);
                }
            }
            return $selectedauthors;
        } else {
            return array();
        }
    }

    /**
     * Get all possible coauthors for assignment
     *
     * @param int $courseid
     * @param int $userid
     * @param boolean $ingroupsonly
     * @param int $assignment
     * @return array:
     */
    private function get_possible_co_authors($courseid, $userid, $ingroupsonly, $assignment)
    {
        global $DB;
        // get config info about groups
        $groupsused = $this->get_config('groupsused');

        if ($groupsused) {

            // get right groups -> all or user-specific ones
            if ($ingroupsonly) {
                $groups = groups_get_all_groups($courseid, $userid);
            } else {
                $groups = groups_get_all_groups($courseid);
            }

            // get all members of the groups
            $members = array();
            foreach ($groups as $a) {
                $members = $members + groups_get_members($a->id);
            }

            // get a record set of all enrolled 'students' (roleid = 5)
            $query = 'select u.id as id, firstname, lastname, picture, imagealt, email from {role_assignments} as a, {user} as u where contextid=' . $this->assignment->get_course_context()->id . ' and roleid=5 and a.userid=u.id;';
            $rs = $DB->get_recordset_sql($query);
            $students = array();
            foreach ($rs as $r) {
                $students[$r->id] = '';
            }

            // collect coauthors
            $coauthors = array();
            $seen = array();
            foreach ($members as $r) {
                if (array_key_exists($r->id, $students)) {
                    $submission = $this->get_submission($r->id, $assignment);
                    if ($submission) {
                        if ($submission->status != 'submitted') {
                            $authorsubmission = $this->get_author_submission($assignment, $submission->id);
                            if (!($authorsubmission && $authorsubmission->author != $userid)) {
                                $coauthors[$r->id] = fullname($r);
                            }
                        }
                    } else {
                        $coauthors[$r->id] = fullname($r);
                    }
                    $seen[$r->id] = '';
                }
            }

            // user is no group -> return empty array
            if (!array_key_exists($userid, $seen)) {
                return array();
            }
        } else {

            // get all enrolled users
            $enroltypes = $DB->get_records('enrol', array(
                'courseid' => $courseid
            ));
            $users = array();
            foreach ($enroltypes as $type) {
                $enrolled_users = $DB->get_records('user_enrolments', array(
                    'enrolid' => $type->id
                ));
                foreach ($enrolled_users as $enrolled_user) {
                    $user = $DB->get_record('user', array(
                        'id' => $enrolled_user->userid
                    ));
                    array_push($users, $user);
                }
            }

            $records = $users;

            // get a record set of all enrolled 'students' (roleid = 5)
            $query = 'select u.id as id, firstname, lastname, picture, imagealt, email from mdl_role_assignments as a, mdl_user as u where contextid=' . $this->assignment->get_course_context()->id . ' and roleid=5 and a.userid=u.id;';
            $rs = $DB->get_recordset_sql($query);
            $students = array();
            foreach ($rs as $r) {
                $students[$r->id] = '';
            }

            // collect coauthors
            $coauthors = array();
            foreach ($records as $r) {
                if (array_key_exists($r->id, $students)) {
                    $submission = $this->get_submission($r->id, $assignment);
                    if ($submission) {
                        $bool = $this->assignment->get_instance()->submissiondrafts == true;
                        if (!$bool || $submission->status != 'submitted') {
                            $authorsubmission = $this->get_author_submission($assignment, $submission->id);
                            if (!($authorsubmission && $authorsubmission->author != $userid)) {
                                $coauthors[$r->id] = fullname($r);
                            }
                        }
                    } else {
                        $coauthors[$r->id] = fullname($r);
                    }
                }
            }
        }
        // remove user
        $userarr[$userid] = '';
        $coauthors = array_diff_key($coauthors, $userarr);

        // sorting coauthors
        asort($coauthors);

        return $coauthors;
    }

    /**
     * Get submission record of a user for an assignment
     *
     * @param int $userid
     * @param int $assignment
     * @return Ambigous <mixed, stdClass, false, boolean>
     */
    private function get_submission($userid, $assignment)
    {
        global $DB;
        return $DB->get_record('assign_submission', array(
            'userid' => $userid,
            'assignment' => $assignment
        ));
    }

    /**
     * Read out the selected coauthors and returns their ids in an array
     *
     * @param stdClass $data
     * @return int[] selected coauthor ids
     */
    private function get_selected_coauthors($data)
    {
        $coauthors = array();
        if (isset($data->coauthors)) {
            $selected = array_unique($data->coauthors);
            do {
                if (($key = array_search(0, $selected)) !== false) {
                    unset($selected[$key]);
                }
            } while ($key !== false);
            $coauthors = $selected;
        }
        return $coauthors;
    }

    /**
     * Set author default record of a user in course
     *
     * @param string $coauthors
     * @param int $userid
     * @param int $courseid
     * @return boolean
     */
    private function set_author_default($coauthors, $userid, $courseid)
    {
        global $DB;
        $authordefaultsubmission = $DB->get_record('assign_author_default', array(
            'user' => $userid,
            'course' => $courseid
        ));
        if ($authordefaultsubmission) {
            $authordefaultsubmission->coauthors = $coauthors;
            return $DB->update_record('assign_author_default', $authordefaultsubmission, false);
        } else {
            $authordefaultsubmission = new stdClass();
            $authordefaultsubmission->coauthors = $coauthors;
            $authordefaultsubmission->course = $courseid;
            $authordefaultsubmission->user = $userid;
            return $DB->insert_record('assign_author_default', $authordefaultsubmission, false) > 0;
        }
    }

    /**
     * Create submission record of a user for submission
     *
     * @param int $userid
     * @param stdClass $submission
     * @return Ambigous <boolean, number>
     */
    private function create_submission($userid, $submission)
    {
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
     * Create the submission and all related parts for all coauthors
     *
     * @param array $coauthors
     * @param stdClass $submission
     * @param string $authorlist
     */
    private function create_author_group($coauthors, $submission, $authorlist)
    {
        global $DB, $CFG;
        $assignment = $submission->assignment;
        $author = $submission->userid;
        // var_dump($assignment);
        foreach ($coauthors as $key => $coauthor) {

            $coauthorsubmission = $this->get_submission($coauthor, $assignment);

            if (!$coauthorsubmission) {

                $this->create_submission($coauthor, $submission);
                require_once($CFG->dirroot . '/mod/assign/lib.php');
                $assign = clone $this->assignment->get_instance();
                // var_dump($assign);
                $assign->cmidnumber = $this->assignment->get_course_module()->idnumber;
                assign_update_grades($assign, $coauthor);
                $coauthorsubmission = $this->get_submission($coauthor, $assignment);

            }

            $id = $coauthorsubmission->id;
            $this->create_author_submission($assignment, $id, $author, $authorlist);

        }
    }

    /**
     * Create the author submission record of a submission for an assignment
     *
     * @param int $assignment
     * @param int $submission
     * @param int $author
     * @param string $authorlist
     * @return boolean
     */
    private function create_author_submission($assignment, $submission, $author, $authorlist)
    {
        global $DB;
        $authorsubmission = new stdClass();
        $authorsubmission->assignment = $assignment;
        $authorsubmission->submission = $submission;
        $authorsubmission->author = $author;
        $authorsubmission->authorlist = $authorlist;
        return $DB->insert_record('assignsubmission_author', $authorsubmission, false) > 0;
    }

    /**
     * Update all related parts for all coauthors
     *
     * @param int[] $coauthors
     * @param int $assignment
     * @param int $author
     * @param string $authorlist
     */
    private function update_author_group($coauthors, $assignment, $author, $authorlist)
    {
        global $DB;
        foreach ($coauthors as $coauthor) {
            $coauthorsubmission = $this->get_submission($coauthor, $assignment);
            if ($coauthorsubmission) {
                $submissionid = $coauthorsubmission->id;
                $authorsubmission = $this->get_author_submission($assignment, $submissionid);
                if ($authorsubmission) {
                    $authorsubmission->author = $author;
                    $authorsubmission->authorlist = $authorlist;
                    $DB->update_record('assignsubmission_author', $authorsubmission, false);
                }
            }
        }
    }

    /**
     * Update author submission record
     *
     * @param stdClass $authorsubmission
     * @param int $author
     * @param string $authorlist
     * @return boolean
     */
    private function update_author_submission($authorsubmission, $author, $authorlist)
    {
        global $DB;
        $authorsubmission->author = $author;
        $authorsubmission->authorlist = $authorlist;
        return $DB->update_record('assignsubmission_author', $authorsubmission, false);
    }

    /**
     * Delete submission record
     *
     * @param int $id
     */
    private function delete_submission($id)
    {
        global $DB;
        return $DB->delete_record('assign_submission', array(
            'id' => $id
        ));
    }

    /**
     * Delete all related parts for author group
     *
     * @param int[] $coauthors
     * @param int $assignment
     */
    private function delete_author_group($coauthors, $assignment)
    {
        foreach ($coauthors as $coauthor) {
            $this->delete_author_submission($coauthor, $assignment);
        }
    }

    /**
     * Delete author submission record
     *
     * @param unknown $userid
     * @param unknown $assignment
     * @return boolean
     */
    private function delete_author_submission($userid, $assignment)
    {
        global $DB;
        $submission = $this->get_submission($userid, $assignment);
        return $DB->delete_records('assignsubmission_author', array(
            'submission' => $submission->id
        ));
    }

    /**
     * Display the author and coauthors
     *
     * @param stdClass $submission
     * @param bool $showviewlink
     *            - If the summary has been truncated set this to true
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink)
    {
        global $CFG, $USER;
        $assignment = $this->assignment->get_instance()->id;
        $authorsubmission = $this->get_author_submission($assignment, $submission->id);
        // Always show the view link.
        $showviewlink = false;

        if ($authorsubmission) {
            $author = $this->get_author_array($authorsubmission->author, true);
            $coauthors = $this->get_author_array($authorsubmission->authorlist, true);

            return $this->get_summary($author, $coauthors);
        }
        return get_string('summary_nocoauthors', 'assignsubmission_author');
    }

    /**
     * Creates summary string with author and coauthors
     *
     * @param unknown $author
     * @param unknown $coauthors
     */
    public function get_summary($author, $coauthors)
    {
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
     * Display the author and coauthors
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission)
    {
        $showviewlink = true;
        return $this->view_summary($submission, $showviewlink);
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission
     *            The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission)
    {
        // Format the info for each submission plugin (will be logged).
        $authorsubmission = $this->get_author_submission($this->assignment->get_instance()->id, $submission->id);
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
     */
    public function delete_instance()
    {
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
     */
    public function is_empty(stdClass $submission)
    {
        return ($this->get_author_submission($this->assignment->get_instance()->id, $submission->id) == false);
    }
}

