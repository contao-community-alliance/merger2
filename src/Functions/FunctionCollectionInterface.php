<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Functions;

use ContaoCommunityAlliance\Merger2\Functions\Description\Description;

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
    public function supports(string $name): bool;

    /**
     * Execute the function
     *
     * @param string $name      Function name.
     * @param array  $arguments Given attributes.
     *
     * @return mixed
     */
    public function execute(string $name, array $arguments);

    /**
     * Describe all supported functions with their arguments.
     *
     * @return Description[]|array
     */
    public function getDescriptions(): array;
}
