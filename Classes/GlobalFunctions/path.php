<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 14-11-2023 09:40
 *
 *  All rights reserved
 *
 *  This script is part of a client or private project.
 *  You can redistribute it and/or modify it under the terms of
 *  the GNU General Public License as published by the Free
 *  Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!function_exists('project_path')) {
    /**
     * Gets the sql statement
     *
     * @param string $append
     *
     * @return string
     */
    function project_path(string $append)
    {
        $append = ltrim($append, '/');
        return dirname(__DIR__, 6) . ($append ? '/' . $append : '');
    }
}

if (!function_exists('temp_path')) {
    /**
     * Gets the sql statement
     *
     * @param string $append
     *
     * @return string
     */
    function temp_path(string $append)
    {
        return project_path('/typo3temp') . ($append ? '/' . $append : '');
    }
}
