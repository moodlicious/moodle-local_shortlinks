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
use core\exception\moodle_exception;
use core\url;
use core_form\dynamic_form;
use core\context\system;
use local_shortlinks\local\link;

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
        $form = $this->_form;

        $form->addElement('text', 'destinationurl', get_string('destinationurl', 'local_shortlinks'));
        $form->setType('destinationurl', PARAM_URL);
    }

    #[\Override]
    protected function get_context_for_dynamic_submission(): context {
        return system::instance();
    }

    #[\Override]
    protected function check_access_for_dynamic_submission(): void {
        if (!isloggedin()) {
            throw new moodle_exception('unauthorised');
        }
        return;
    }

    #[\Override]
    public function process_dynamic_submission() {
        global $CFG, $USER;

        $data = $this->get_data();
        if (!$data) {
            return ['success' => false];
        }

        $api = \core\di::get(\core\shortlink::class);
        $db = \core\di::get(\moodle_database::class);

        $transaction = $db->start_delegated_transaction();

        $link = new link(record: (object) [
            'userid' => $USER->id,
            'destinationurl' => $data->destinationurl,
            'shorturl' => $CFG->wwwroot, // Temporary until the actual shorturl is generated.
        ]);
        $link->save();
        $shorturl = $api->create_public_shortlink('local_shortlinks', 'url', $link->get('id'));
        $link->set('shorturl', $shorturl->out_as_local_url(false));
        $link->save();

        $transaction->allow_commit();

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

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        try {
            new url($data['destinationurl']);
        } catch (\Exception $th) {
            $errors['destinationurl'] = get_string('invalidurl', 'error');
        }

        return $errors;
    }
}
