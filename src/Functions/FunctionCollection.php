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

/**
 * Class contains a set of children collection.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
class FunctionCollection implements FunctionCollectionInterface
{
    /**
     * Function collections.
     *
     * @var FunctionInterface[]
     */
    private $functions;

    /**
     * Constructor.
     *
     * @param FunctionInterface[]|array $functions Map of functions.
     */
    public function __construct(array $functions)
    {
        $this->functions = $functions;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($name)
    {
        foreach ($this->functions as $function) {
            if ($function->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException If function is not supported.
     */
    public function execute($name, array $arguments)
    {
        foreach ($this->functions as $function) {
            if ($function->getName() === $name) {
                return $function->invoke($arguments);
            }
        }

        throw new \RuntimeException(sprintf('Unsupported function "%s"', $name));
    }

    /**
     * {@inheritDoc}
     */
    public function getDescriptions()
    {
        $description = [];

        foreach ($this->functions as $function) {
            $description[] = $function->describe();
        }

        return $description;
    }
}
