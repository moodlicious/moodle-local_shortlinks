<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_shortlinks\local;

use core\event\user_deleted;

/**
 * Event observers.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observers {
    /**
     * Handle user deletion: clean up links owned by or modified by the user.
     *
     * @param user_deleted $event
     */
    public static function user_deleted(user_deleted $event): void {
        global $DB;

        $userid = $event->objectid;

        $DB->set_field(link::TABLE, 'usermodified', 0, ['usermodified' => $userid]);
        $DB->delete_records(link::TABLE, ['userid' => $userid]);
    }
}
