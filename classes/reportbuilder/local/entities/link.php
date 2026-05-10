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

namespace local_shortlinks\reportbuilder\local\entities;

use Closure;
use core\lang_string;
use core\output\html_writer;
use core\url;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\tags;
use core_reportbuilder\local\filters\user;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_tag_tag;
use local_shortlinks\local\link as link_model;

/**
 * Link entity.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link extends base {
    #[\Override]
    protected function get_default_tables(): array {
        return [
            link_model::TABLE,
            'tag_instance',
            'tag',
        ];
    }

    #[\Override]
    protected function get_default_entity_title(): lang_string {
        return new lang_string('pluginname', 'local_shortlinks');
    }

    #[\Override]
    protected function get_available_columns(): array {
        $tablealias = $this->get_table_alias(link_model::TABLE);
        $columns = [];

        $linkcallback = Closure::fromCallable([static::class, 'link_column_callback']);

        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("$tablealias.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        $columns[] = (new column(
            'shorturl',
            new lang_string('shorturl', 'local_shortlinks'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("$tablealias.shorturl")
            ->set_is_sortable(false)
            ->add_callback($linkcallback);

        $columns[] = (new column(
            'destinationurl',
            new lang_string('destinationurl', 'local_shortlinks'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("$tablealias.destinationurl")
            ->set_is_sortable(false)
            ->add_callback($linkcallback);

        return $columns;
    }

    #[\Override]
    protected function get_available_filters(): array {
        $tablealias = $this->get_table_alias(link_model::TABLE);
        $filters = [];

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated'),
            $this->get_entity_name(),
            "{$tablealias}.timecreated",
        ))
            ->add_joins($this->get_joins());

        // User.
        $filters[] = (new filter(
            user::class,
            'userid',
            new lang_string('user'),
            $this->get_entity_name(),
            "{$tablealias}.userid",
        ))
            ->add_joins($this->get_joins());

        // Tags.
        $filters[] = (new filter(
            tags::class,
            'tags',
            new lang_string('tags'),
            $this->get_entity_name(),
            "{$tablealias}.id",
        ))
            ->set_options([
                'component' => 'local_shortlinks',
                'itemtype' => 'local_shortlinks',
            ])
            ->set_is_available(core_tag_tag::is_enabled('local_shortlinks', 'local_shortlinks') === true);

        return $filters;
    }

    /**
     * Return joins necessary for retrieving tags.
     * @return string[]
     */
    public function get_tag_joins(): array {
        $tablealias = $this->get_table_alias(link_model::TABLE);
        return $this->get_tag_joins_for_entity(
            component: 'local_shortlinks',
            itemtype: 'local_shortlinks',
            itemidfield: "$tablealias.id",
        );
    }

    /**
     * Helper function to render a link column.
     * @param string $link
     * @return string
     */
    private static function link_column_callback(string $link): string {
        return self::html_link(new url($link));
    }

    /**
     * Helper function to render URL.
     * @param url $url
     * @return string
     */
    private static function html_link(url $url): string {
        $domain = $url->get_host();
        $favicon = "https://icons.duckduckgo.com/ip3/$domain.ico";

        return html_writer::link($url, implode('', [
            html_writer::img($favicon, $domain),
            html_writer::span($url->out(false)),
        ]), ['class' => 'local_shortlinks-link', 'target' => '_blank']);
    }
}
