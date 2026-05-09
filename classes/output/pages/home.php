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

namespace local_shortlinks\output\pages;

use core\output\named_templatable;
use core\output\notification;
use core\output\renderable;
use core\output\renderer_base;
use local_shortlinks\local\link;

/**
 * Short link handler.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class home implements named_templatable, renderable {
    /** @var string */
    public const URL = '/local/shortlinks/index.php';

    /** @var \local_shortlinks\form\create_shortlink */
    private \local_shortlinks\form\create_shortlink $form;

    /**
     * Constructor.
     */
    public function __construct() {
        global $PAGE;
        $PAGE->set_url(self::URL);
        $title = get_string('pages:home:create', 'local_shortlinks');
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        $this->form = new \local_shortlinks\form\create_shortlink();
        $this->handle_form();
    }

    /**
     * Handles form.
     * @return void
     */
    public function handle_form() {
        global $CFG, $USER;

        $data = $this->form->get_data();
        if (!$data) {
            return;
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
        redirect(self::URL, get_string('success:created', 'local_shortlinks'), null, notification::NOTIFY_SUCCESS);
    }

    #[\Override]
    public function export_for_template(renderer_base $renderer): array {
        return ['html' => $this->form->render()];
    }

    #[\Override]
    public function get_template_name(renderer_base $renderer): string {
        return 'local_shortlinks/pages/home';
    }
}
