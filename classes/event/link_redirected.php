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

namespace local_shortlinks\event;

use core\context\system;
use core\event\base;
use core\router\util;
use core\url;
use local_shortlinks\local\link;
use local_shortlinks\route\controller\links_controller;

/**
 * Link redirected event.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link_redirected extends base {
    #[\Override]
    protected function init() {
        $this->data['objecttable'] = link::TABLE;
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an instance from link class.
     *
     * @param link $link
     * @return self
     */
    public static function create_from_object(link $link): self {
        $eventparams = [
            'context' => system::instance(),
            'objectid' => $link->get('id'),
        ];
        $event = self::create($eventparams);
        $event->add_record_snapshot($event->objecttable, $link->to_record());
        return $event;
    }

    #[\Override]
    public static function get_name() {
        return get_string('events:link_redirected', 'local_shortlinks');
    }

    #[\Override]
    public function get_url(): url {
        return util::get_path_for_callable([links_controller::class, 'home']);
    }
}
