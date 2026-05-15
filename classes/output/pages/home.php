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

use core\context\system;
use core\output\html_writer;
use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\url;
use core_reportbuilder\system_report_factory;
use local_shortlinks\reportbuilder\local\systemreports\links;
use local_shortlinks\route\controller\links_controller;

/**
 * Short link handler.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class home implements named_templatable, renderable {
    /**
     * Returns the URL for this page.
     * @return url
     */
    public static function get_url(): url {
        $url = \core\router\util::get_path_for_callable(callable: [links_controller::class, 'home']);
        return $url;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        global $PAGE;

        $PAGE->set_context(system::instance());
        $PAGE->set_url(self::get_url());
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
        $report = system_report_factory::create(links::class, system::instance());

        return ['html' => implode([
            html_writer::tag('section', $report->output()),
        ])];
    }

    #[\Override]
    public function get_template_name(renderer_base $renderer): string {
        return 'local_shortlinks/pages/home';
    }
}
