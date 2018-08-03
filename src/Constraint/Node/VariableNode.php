<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Constraint\Node;

/**
 * Class VariableNode.
 */
final class VariableNode implements NodeInterface
{
    /**
     * Variable name.
     *
     * @var string
     */
    protected $name;

    /**
     * VariableNode constructor.
     *
     * @param string $name Variable name.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate()
    {
        // TODO read variable
        throw new \RuntimeException('Incomplete implementation');
    }
}
