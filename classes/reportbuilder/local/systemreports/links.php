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

namespace local_shortlinks\reportbuilder\local\systemreports;

use core_reportbuilder\local\filters\user;
use core_reportbuilder\system_report;
use core_tag\reportbuilder\local\entities\tag;
use local_shortlinks\local\link;
use local_shortlinks\reportbuilder\local\entities\link as link_entity;

/**
 * Links report.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class links extends system_report {
    #[\Override]
    protected function initialise(): void {
        $entitymain = new link_entity();
        $entitymainalias = $entitymain->get_table_alias(link::TABLE);

        $this->set_main_table(link::TABLE, $entitymainalias);
        $this->add_entity($entitymain);

        // Join the tag entity.
        $tagentity = (new tag())
            ->set_table_alias('tag', $entitymain->get_table_alias('tag'));
        $this->add_entity($tagentity->add_joins($entitymain->get_tag_joins()));

        $this->add_columns();
        $this->add_filters();
        $this->set_conditions();

        $this->set_initial_sort_column('link:timecreated', SORT_DESC);

        $this->set_default_per_page(25);
        $this->set_downloadable(true);

        return;
    }

    #[\Override]
    protected function can_view(): bool {
        return true;
    }

    /**
     * Adds report columns.
     * @return void
     */
    protected function add_columns(): void {
        $columns = [
            'link:shorturl',
            'link:destinationurl',
            'link:timecreated',
            // Not adding 'tag:name' column here because it doesn't display all tags within the same row,
            // but splits multiple tags for the same link across multiple rows.
        ];
        $this->add_columns_from_entities($columns);
    }

    /**
     * Adds report filters.
     * @return void
     */
    protected function add_filters(): void {
        $columns = [
            // Not adding 'link:tags' filter here because it makes tag suggestions for tags created by other users too.
            'link:timecreated',
        ];
        $this->add_filters_from_entities($columns);
    }

    /**
     * Adds report conditions.
     * @return void
     */
    protected function set_conditions(): void {
        $conditions = [
            'link:userid',
        ];
        $this->add_conditions_from_entities($conditions);
        $this->set_condition_values([
            'link:userid_operator' => user::USER_CURRENT,
        ]);
    }
}
