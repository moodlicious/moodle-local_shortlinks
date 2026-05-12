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

namespace local_shortlinks\local;

use core\context\user;
use core_tag_tag;
use local_shortlinks\local\enums\type;

/**
 * API Interface for managing user defined shortlinks via this plugin.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {
    /**
     * Maps the type of link to the length of shortcode.
     *
     * - Short links use length of 6.
     * - Long unguessable links use length of 12, that is the maximum length supported by core\shortlinks.
     * @var array<type, array{int, int}>
     */
    private const TYPE_TO_LENGTH = [
        type::SHORT->value => [6, 6],
        type::LONG->value => [12, 12],
        type::UNGUESSABLE->value => [12, 12],
    ];

    /**
     * Creates a short link for the current user.
     * @param string $destinationurl
     * @param type $type
     * @param string[] $tags
     * @return void
     */
    public static function create(string $destinationurl, type $type = type::SHORT, array $tags = []): void {
        global $CFG, $USER;
        $api = \core\di::get(\core\shortlink::class);
        $db = \core\di::get(\moodle_database::class);

        [$minlength, $maxlength] = static::TYPE_TO_LENGTH[$type->value];
        $unguessablecode = $type === type::UNGUESSABLE ? link::generate_unguessable_code() : null;

        $transaction = $db->start_delegated_transaction();

        $link = new link(record: (object) [
            'userid' => $USER->id,
            'destinationurl' => $destinationurl,
            'shorturl' => $CFG->wwwroot, // Temporary until the actual shorturl is generated.
            'unguessablecode' => $unguessablecode,
        ]);
        $link->save();
        core_tag_tag::set_item_tags(
            'local_shortlinks',
            'local_shortlinks',
            $link->get('id'),
            user::instance($USER->id),
            $tags,
        );
        $shorturl = $api->create_public_shortlink('local_shortlinks', 'url', $link->get('id'), $minlength, $maxlength);
        if ($unguessablecode) {
            $shorturl->param('c', $unguessablecode);
        }
        $link->set('shorturl', $shorturl->out_as_local_url(false));
        $link->save();

        $transaction->allow_commit();
        return;
    }
}
