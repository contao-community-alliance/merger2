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
 * Class StringNode.
 */
final class StringNode implements NodeInterface
{
    /**
     * Value.
     *
     * @var string
     */
    protected $value;

    /**
     * StringNode constructor.
     *
     * @param string $value String value.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(): string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
