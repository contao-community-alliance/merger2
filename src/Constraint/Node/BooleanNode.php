<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 *
 * @link      http://bit3.de
 *
 * @license   LGPL-3.0+
 */

namespace ContaoCommunityAlliance\Merger2\Constraint\Node;

class BooleanNode implements NodeInterface
{
    /**
     * @var bool
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = (bool) $value;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate()
    {
        return $this->value;
    }
}
