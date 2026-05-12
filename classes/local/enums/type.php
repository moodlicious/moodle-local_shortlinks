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

namespace local_shortlinks\local\enums;

/**
 * Type of link to generate.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum type: string {
    case SHORT = 'short';
    case LONG = 'long';
    case UNGUESSABLE = 'unguessable';

    /**
     * Gets an associated array, key is type value, value is language string.
     * Suitable for use in form select.
     * @return string[]
     */
    public static function get_menu() {
        return [
            self::SHORT->value => get_string('linktype:short', 'local_shortlinks'),
            self::LONG->value => get_string('linktype:long', 'local_shortlinks'),
            self::UNGUESSABLE->value => get_string('linktype:unguessable', 'local_shortlinks'),
        ];
    }
}
