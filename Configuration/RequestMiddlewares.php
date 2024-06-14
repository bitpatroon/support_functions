<?php

/**
 * An array consisting of implementations of middlewares for a middleware stack to be registered
 *
 *  'stackname' => [
 *      'middleware-identifier' => [
 *         'target' => classname or callable
 *         'before/after' => array of dependencies
 *      ]
 *   ]
 */
return [
    'frontend' => [
        'spl/library/accept-nc-prefix' => [
            'target' => \BPN\SupportFunctions\Middleware\NoCachePrefixMiddleware::class,
            'after'  => [
                'typo3/cms-frontend/timetracker',
            ],
            'before' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
