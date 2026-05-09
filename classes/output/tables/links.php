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

namespace local_shortlinks\output\tables;

use core\output\html_writer;
use core\url;
use local_shortlinks\local\link;
use table_sql;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Links table.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class links extends table_sql {
    /**
     * Constructor.
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        $this->define_columns(['shorturl', 'destinationurl']);
        $this->define_headers([
            get_string('shorturl', 'local_shortlinks'),
            get_string('destinationurl', 'local_shortlinks'),
        ]);
        $this->sortable(false);
        $this->is_downloadable(false);
    }

    /**
     * Generate the short url column.
     * @param link $link
     * @return string
     */
    public function col_shorturl(link $link) {
        return self::html_link(new url($link->get('shorturl')));
    }

    /**
     * Generate the short url column.
     * @param link $link
     * @return string
     */
    public function col_destinationurl(link $link) {
        return self::html_link(new url($link->get('destinationurl')));
    }

    /**
     * Helper function to render URL.
     * @param url $url
     * @return string
     */
    private static function html_link(url $url): string {
        return html_writer::link($url, $url->out(false), ['target' => '_blank']);
    }

    #[\Override]
    public function query_db($pagesize, $useinitialsbar = true) {
        global $USER;
        $filter = ['userid' => $USER->id];
        $total = link::count_records($filter);
        $this->pagesize($pagesize, $total);
        $this->rawdata = link::get_records($filter, 'timecreated', 'DESC');

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }
}
