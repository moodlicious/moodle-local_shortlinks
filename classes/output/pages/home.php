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

namespace local_shortlinks\output\pages;

use core\output\html_writer;
use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;

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

    /**
     * Constructor.
     */
    public function __construct() {
        global $PAGE;
        $PAGE->set_url(self::URL);
        $title = get_string('pluginname', 'local_shortlinks');
        $PAGE->set_title($title);
        $PAGE->set_heading($title);

        $PAGE->add_header_action(
            html_writer::link('#', get_string('create'), ['id' => 'create_shortlink', 'class' => 'btn btn-primary']),
        );
        $PAGE->requires->js_call_amd('local_shortlinks/main', 'init', []);
    }

    /**
     * {@inheritDoc}
     * @return mixed[]
     */
    #[\Override]
    public function export_for_template(renderer_base $renderer): array {
        $table = new \local_shortlinks\output\tables\links('links');

        ob_start();
        $table->out(10, true);
        $tablehtml = ob_get_contents() ?: '';
        ob_end_clean();

        return ['html' => implode([
            html_writer::tag('section', $tablehtml),
        ])];
    }

    #[\Override]
    public function get_template_name(renderer_base $renderer): string {
        return 'local_shortlinks/pages/home';
    }
}
