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

namespace local_shortlinks\route\shim;

use core\param;
use core\router\route;
use core\router\route_controller;
use core\router\schema\parameters\query_parameter;
use local_shortlinks\route\controller\links_controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Plugin redirects.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redirects {
    use route_controller;

    /**
     * Redirects to the actual delete link controller.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/redirect/delete',
        queryparams: [
            new query_parameter(
                name: 'id',
                type: param::INT,
                required: true,
            ),
        ],
    )]
    public function delete_link(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $params = $request->getQueryParams();
        return $this::redirect_to_callable(
            $request,
            $response,
            [links_controller::class, 'delete_link'],
            pathparams: ['link' => $params['id']],
            excludeparams: ['id'],
        );
    }
}
