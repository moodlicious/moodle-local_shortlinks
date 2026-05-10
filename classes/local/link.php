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

use core\persistent;

/**
 * Short link handler.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link extends persistent {
    /** @var string The table name. */
    public const TABLE = 'local_shortlinks';

    /**
     * {@inheritDoc}
     * @return array<string, mixed>[]
     */
    #[\Override]
    protected static function define_properties(): array {
        return [
            'userid' => [
                'type' => PARAM_INT,
            ],
            'destinationurl' => [
                'type' => PARAM_URL,
            ],
            'shorturl' => [
                'type' => PARAM_URL,
            ],
        ];
    }
}
