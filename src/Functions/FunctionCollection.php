<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Functions;

/**
 * Class contains a set of children collection.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
final class FunctionCollection implements FunctionCollectionInterface
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
    public function supports(string $name): bool
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
    public function execute(string $name, array $arguments)
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
    public function getDescriptions(): array
    {
        $description = [];

        foreach ($this->functions as $function) {
            $description[] = $function->describe();
        }

        return $description;
    }
}
