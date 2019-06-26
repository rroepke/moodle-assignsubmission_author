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
 * Upgrade code for install
 *
 * @package assignsubmission_author
 * @author Rene Roepke
 * @author Guido Roessling
 * @copyright 2013 Rene Roepke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Stub for upgrade code
 *
 * @param int $oldversion
 * @return bool
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_assignsubmission_author_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2018072101) {

        // Define table assignsubmission_author_def to be renamed to assignsubmission_author_def.
        $table = new xmldb_table('assign_author_default');

        // Launch rename table for assignsubmission_author_def.
        $dbman->rename_table($table, 'assignsubmission_author_def');

        // Author savepoint reached.
        upgrade_plugin_savepoint(true, 2018072101, 'assignsubmission', 'author');
    }

    if ($oldversion < 2019062602) {

        // Rename field user on table assignsubmission_author_def to userid.
        $table = new xmldb_table('assignsubmission_author_def');
        $field = new xmldb_field('user', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'coauthors');

        // Launch rename field userid.
        $dbman->rename_field($table, $field, 'userid');

        // Author savepoint reached.
        upgrade_plugin_savepoint(true, 2019062602, 'assignsubmission', 'author');
    }

    return true;
}


