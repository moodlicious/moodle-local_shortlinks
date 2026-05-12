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

namespace local_shortlinks;

use core\shortlink_handler_interface;
use core\shutdown_manager;
use core\url;
use local_shortlinks\local\link;

/**
 * Short link handler.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shortlink_handler implements shortlink_handler_interface {
    /**
     * {@inheritDoc}
     * @return string[]
     */
    #[\Override]
    public function get_valid_linktypes(): array {
        return ['url'];
    }

    #[\Override]
    public function process_shortlink(string $type, string $identifier): ?url {
        return match ($type) {
            'url' => $this->get_url($identifier),
            default => null,
        };
    }

    /**
     * Resolves the destination URL.
     * @param string $identifier
     * @return url|null
     */
    private function get_url(string $identifier): ?url {
        $link = link::get_record(['id' => $identifier]);
        if ($link === false) {
            return null;
        }

        // Analytics tracking after shutdown so client don't need to feel the effect
        // of the potentially slow tracking function.
        shutdown_manager::register_function(function () use ($link) {
            // Flush everything.
            @ob_end_flush();
            @flush();
            @fastcgi_finish_request();

            $link->track();
            return;
        });

        try {
            return new url($link->get('destinationurl'));
        } catch (\Exception $th) {
            debugging($th->getMessage(), DEBUG_DEVELOPER, $th->getTrace());
            return null;
        }
    }
}
