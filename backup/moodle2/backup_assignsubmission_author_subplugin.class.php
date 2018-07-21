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
 * This file contains the class for backup of this submission plugin
 *
 * @package assignsubmission_author
 * @copyright 2017 Rene Roepke
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides the information to backup author submissions
 *
 * @package assignsubmission_author
 * @copyright 2017 Rene Roepke
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_assignsubmission_author_subplugin extends backup_subplugin {

    /**
     * Returns the subplugin information to attach to submission element
     *
     * @return backup_subplugin_element
     * @throws base_element_struct_exception
     */
    protected function define_submission_subplugin_structure() {

        $subplugin = $this->get_subplugin_element();
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        $subpluginelement = new backup_nested_element('submission_author',
                null,
                array('author',
                        'authorlist',
                        'submission')
        );

        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($subpluginelement);

        $subpluginelement->set_source_table('assignsubmission_author',
                array('submission' => backup::VAR_PARENTID));
        $subpluginelement->annotate_files('assignsubmission_author',
                'submissions_author',
                'submission');
        return $subplugin;
    }

}
