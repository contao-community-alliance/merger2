<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @copyright 2013,2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @author    David Molineus <david.molineus@netzmacht.de>
 *
 * @link      https://github.com/contao-community-alliance/merger2
 *
 * @license   LGPL-3.0+
 */

namespace ContaoCommunityAlliance\Merger2\Functions;

/**
 * Interface MergerFunctionInterface
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
interface FunctionCollectionInterface
{
    /**
     * Check if function is supported.
     *
     * @param string $name Name of the function being handled.
     *
     * @return bool
     */
    public function supports($name);

    /**
     * Execute the function
     *
     * @param string $name      Function name.
     * @param array  $arguments Given attributes.
     *
     * @return mixed
     */
    public function execute($name, array $arguments);
}
