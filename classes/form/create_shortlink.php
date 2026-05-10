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
use Exception;
use local_shortlinks\local\api;
use local_shortlinks\local\enums\type;

/**
 * Form to create new short links.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_shortlink extends dynamic_form {
    #[\Override]
    public function definition(): void {
        $form = $this->_form;

        $form->addElement('text', 'destinationurl', get_string('destinationurl', 'local_shortlinks'));
        $form->setType('destinationurl', PARAM_URL);

        $form->addElement('checkbox', 'unguessable', get_string('unguessable', 'local_shortlinks'));
        $form->addHelpButton('unguessable', 'unguessable', 'local_shortlinks');

        $form->addElement('header', 'advanced', get_string('advanced'));
        $form->setExpanded('advanced', false);

        $form->addElement('tags', 'tags', get_string('tags'), [
            'itemtype' => 'local_shortlinks',
            'component' => 'local_shortlinks',
        ]);
    }

    #[\Override]
    protected function get_context_for_dynamic_submission(): context {
        return system::instance();
    }

    #[\Override]
    protected function check_access_for_dynamic_submission(): void {
        require_capability('local/shortlinks:create', system::instance());
    }

    #[\Override]
    public function process_dynamic_submission() {
        $data = $this->get_data();
        if (!$data) {
            return ['success' => false];
        }

        try {
            $destinationurl = $data->destinationurl;
            $type = $data->unguessable ? type::LONG : type::SHORT;
            $tags = $data->tags;
            api::create($destinationurl, type: $type, tags: $tags);
        } catch (Exception $th) {
            return ['success' => false];
        }

        return ['success' => true];
    }

    #[\Override]
    public function set_data_for_dynamic_submission(): void {
        return;
    }

    #[\Override]
    protected function get_page_url_for_dynamic_submission(): url {
        return new url(\local_shortlinks\output\pages\home::URL);
    }

    /**
     * Validate destination URL is acceptable.
     * @param array<string, mixed> $data
     * @param array<string, mixed> $files
     * @return array<string, string>
     */
    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        try {
            $destinationurl = $data['destinationurl'];
            if (!trim($destinationurl)) {
                throw new Exception('URL required..');
            }

            if (!str_starts_with(strtolower($destinationurl), 'https://')) {
                throw new Exception('URL must start with https://');
            }

            // Url constructor will throw error on invalid URLs.
            new url($destinationurl);
        } catch (Exception $th) {
            $errors['destinationurl'] = get_string('invalidurl', 'error');
        }

        return $errors;
    }
}
