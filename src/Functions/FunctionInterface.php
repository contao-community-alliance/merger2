<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Functions;

use ContaoCommunityAlliance\Merger2\Functions\Description\Description;

/**
 * Interface MergerFunction
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
interface FunctionInterface
{
    /**
     * Get the name of the function.
     *
     * @return string
     */
    public static function getName();

    /**
     * Invoke the function.
     *
     * @param array $arguments List of passed arguments.
     *
     * @return mixed
     */
    public function invoke(array $arguments = []);

    /**
     * Describe the function and return
     *
     * @return Description
     */
    public function describe();
}
