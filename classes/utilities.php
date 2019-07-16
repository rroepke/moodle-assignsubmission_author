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

class utilities {

    /**
     * @param $assignmentid
     * @param $name
     * @param $subtype
     * @return bool
     * @throws \dml_exception
     */
    public static function is_plugin_enabled($assignmentid, $name, $subtype) {
        global $DB;

        $rec = $DB->get_record('assign_plugin_config', array(
                'assignment' => $assignmentid,
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
     * Get the author ids and names as an array
     *
     * @param string $ids
     * @return array
     * @throws \dml_exception
     */
    public static function get_author_array($ids, $assignmentid, $link = false) {
        global $DB, $CFG;
        if ($ids != '') {
            $ids2 = explode(',', $ids);
            $selectedauthors = array();
            foreach ($ids2 as $id) {
                $userrec = $DB->get_record('user', array(
                        'id' => $id
                ));
                $userrec = get_complete_user_data('id', $id);
                if ($userrec) {
                    if ($link) {
                        $url = $CFG->wwwroot . '/user/view.php?id=' . $userrec->id . '&course=' . $assignmentid;
                        $selectedauthors[$userrec->id] = "<a href='" . $url . "'>" . fullname($userrec) . "</a>";
                    } else {
                        $selectedauthors[$userrec->id] = fullname($userrec);
                    }
                }
            }
            return $selectedauthors;
        } else {
            return array();
        }
    }

    /**
     * Read out the selected coauthors and returns their ids in an array
     *
     * @param \stdClass $data
     * @return int[] selected coauthor ids
     */
    public static function get_selected_coauthors($data) {
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
     * Checks if default coauthors can be used
     *
     * @param array $defaults
     * @param array $possibles
     * @param $maxauthors
     * @return boolean true if default coauthors can be used
     */
    public static function is_default_usable($defaults, $possibles, $maxauthors) {
        if (count($defaults) > $maxauthors - 1) {
            return false;
        }
        foreach (array_keys($defaults) as $author) {
            if (!array_key_exists($author, $possibles)) {
                return false;
            }
        }
        return true;
    }
}