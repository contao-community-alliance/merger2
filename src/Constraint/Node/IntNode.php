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

namespace ContaoCommunityAlliance\Merger2\Constraint\Node;

/**
 * Class IntNode
 *
 * @package ContaoCommunityAlliance\Merger2\Constraint\Node
 */
class IntNode implements NodeInterface
{
    /**
     * The value.
     *
     * @var int
     */
    private $value;

    /**
     * IntNode constructor.
     *
     * @param int $value The value.
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Evaluate the value.
     *
     * @return int
     */
    public function evaluate(): int
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
