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

abstract class ConjunctionNode implements NodeInterface
{
    /**
     * @var NodeInterface
     */
    protected $left;

    /**
     * @var NodeInterface
     */
    protected $right;

    public function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @param NodeInterface $left
     */
    public function setLeft(NodeInterface $left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * @return NodeInterface
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param NodeInterface $right
     */
    public function setRight(NodeInterface $right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * @return NodeInterface
     */
    public function getRight()
    {
        return $this->right;
    }
}
