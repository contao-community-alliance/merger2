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

namespace ContaoCommunityAlliance\Merger2\Constraint\Node;

/**
 * Class FloatNode
 *
 * @package ContaoCommunityAlliance\Merger2\Constraint\Node
 */
class FloatNode implements NodeInterface
{
    /**
     * The value.
     *
     * @var float
     */
    private $value;

    /**
     * FloatNode constructor.
     *
     * @param float $value The value.
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * Evaluate the value.
     *
     * @return float
     */
    public function evaluate(): float
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
