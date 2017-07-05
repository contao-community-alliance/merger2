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
 * Class FunctionCollectionCollection contains a set of children collection.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
class FunctionCollectionCollection implements FunctionCollectionInterface
{
    /**
     * Function collections.
     *
     * @var FunctionCollectionInterface[]
     */
    private $collections;

    /**
     * FunctionCollectionCollection constructor.
     *
     * @param FunctionCollectionInterface[]|array $collections Function collections.
     */
    public function __construct(array $collections)
    {
        $this->collections = $collections;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($name)
    {
        foreach ($this->collections as $collection) {
            if ($collection->supports($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($name, array $arguments)
    {
        foreach ($this->collections as $collection) {
            if ($collection->supports($name)) {
                return $collection->execute($name, $arguments);
            }
        }

        throw new \RuntimeException(sprintf('Unsupported function "%s"', $name));
    }
}
