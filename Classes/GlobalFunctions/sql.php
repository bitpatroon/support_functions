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

use SPL\SplLibrary\Utility\QueryHelper;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

if (!function_exists('sql')) {
    /**
     * Gets the sql statement
     *
     * @param QueryBuilder $builder
     *
     * @return array|string|string[]
     */
    function sql(QueryBuilder $builder)
    {
        return QueryHelper::new()->sql($builder);
    }
}

if (!function_exists('ddsql')) {
    /**
     *  Dump and die the sql statement with all params
     *
     * @param QueryBuilder $builder
     *
     * @return void
     */
    function ddsql(QueryBuilder $builder)
    {
        $statement = QueryHelper::new()->sql($builder);

        echo $statement;
        die();
    }
}

if (!function_exists('dsql')) {
    /**
     * Dump the sql statement with all params
     *
     * @param QueryBuilder $builder
     *
     * @return void
     */
    function dsql(QueryBuilder $builder)
    {
        $statement = QueryHelper::new()->sql($builder);

        dump($statement);
    }
}

if (!function_exists('makeInstance')) {
    /**
     * Dump the sql statement with all params
     *
     * @param QueryBuilder $builder
     *
     * @return void
     */
    function makeInstance(array $arguments)
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(...$arguments);
    }
}