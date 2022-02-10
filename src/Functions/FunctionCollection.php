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
     * @var array<string,FunctionInterface>
     */
    private $functions = [];

    /**
     * Constructor.
     *
     * @param iterable<FunctionInterface> $functions Map of functions.
     */
    public function __construct(iterable $functions)
    {
        foreach ($functions as $function) {
            $this->functions[$function::getName()] = $function;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $name): bool
    {
        return isset($this->functions[$name]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException If function is not supported.
     */
    public function execute(string $name, array $arguments)
    {
        if (isset($this->functions[$name])) {
            return $this->functions[$name]->invoke($arguments);
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
