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

namespace local_shortlinks\form;

use core\context;
use core\url;
use core_form\dynamic_form;
use core\context\system;

/**
 * Form to create new short links.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_shortlink extends dynamic_form {
    #[\Override]
    public function definition() {
    }

    #[\Override]
    protected function get_context_for_dynamic_submission(): context {
        return system::instance();
    }

    #[\Override]
    protected function check_access_for_dynamic_submission(): void {
        return;
    }

    #[\Override]
    public function process_dynamic_submission() {
        return;
    }

    #[\Override]
    public function set_data_for_dynamic_submission(): void {
        return;
    }

    #[\Override]
    protected function get_page_url_for_dynamic_submission(): url {
        return new url(\local_shortlinks\output\pages\home::URL);
    }
}
