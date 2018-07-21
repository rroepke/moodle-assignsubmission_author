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
 * This file contains the class for restore of this submission plugin
 *
 * @package assignsubmission_author
 * @copyright 2017 Rene Roepke
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restore subplugin class.
 *
 * @package assignsubmission_author
 * @copyright 2017 Rene Roepke
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_assignsubmission_author_subplugin extends restore_subplugin {

    /**
     * Returns array the paths to be handled by the subplugin at assignment level
     *
     * @return array
     */
    protected function define_submission_subplugin_structure() {

        $paths = array();

        $name = $this->get_namefor('submission');

        $path = $this->get_pathfor('/submission_author');
        $paths[] = new restore_path_element($name, $path);

        return $paths;
    }

    /**
     * Processes one assignsubmission_author element
     *
     * @param mixed $data
     * @throws dml_exception
     */
    public function process_assignsubmission_author_submission($data) {
        global $DB;

        $data = (object)$data;
        $data->assignment = $this->get_new_parentid('assign');
        $oldsubmissionid = $data->submission;

        $data->submission = $this->get_mappingid('submission', $data->submission);
        $DB->insert_record('assignsubmission_author', $data);
        $this->add_related_files('assignsubmission_author', 'submissions_author', 'submission', null, $oldsubmissionid);
    }

}
