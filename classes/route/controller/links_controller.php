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

namespace local_shortlinks\route\controller;

use core\context\system;
use core\notification;
use core\param;
use core\router\require_login;
use core\router\route;
use core\router\route_controller;
use core\router\schema\parameters\path_parameter;
use core\router\schema\parameters\query_parameter;
use core\router\util;
use local_shortlinks\local\link;
use local_shortlinks\output\pages\home;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Plugin links controller.
 *
 * @package   local_shortlinks
 * @copyright 2026 Felix Yeung
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class links_controller {
    use route_controller;

    /**
     * Home page.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '',
        method: ['GET'],
        requirelogin: new require_login(requirelogin: true),
    )]
    public function home(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        global $OUTPUT;

        require_capability('local/shortlinks:create', system::instance());

        $page = new home();

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->render($page));
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Home page.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/{link}/delete',
        method: ['GET', 'POST'],
        pathtypes: [
            new path_parameter(
                name: 'link',
                type: param::INT,
            ),
        ],
        requirelogin: new require_login(requirelogin: true),
    )]
    public function delete_link(
        ServerRequestInterface $request,
        ResponseInterface $response,
        int $link,
    ): ResponseInterface {
        global $OUTPUT;
        require_capability('local/shortlinks:delete', system::instance());

        $link = link::get_record(['id' => $link]);
        if (!$link) {
            return self::page_not_found($request, $response);
        }

        $confirmed = optional_param('confirm', false, param::BOOL->value);
        if ($confirmed) {
            require_sesskey();
            $link->delete();
            notification::add('Deleted', \core\output\notification::NOTIFY_SUCCESS); // TODO: Translate.
            return self::redirect_to_callable(
                $request,
                $response,
                [self::class, 'home'],
            );
        }

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->confirm(
            'Are you sure?', // TODO: Translate.
            util::get_path_for_callable(
                [self::class, 'delete_link'],
                params: ['link' => $link->get('id')],
                queryparams: ['confirm' => true],
            ),
            util::get_path_for_callable([self::class, 'home']),
        ));
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }
}
