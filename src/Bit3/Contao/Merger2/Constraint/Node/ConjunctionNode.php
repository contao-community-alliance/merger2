<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @link      http://bit3.de
 * @package   bit3/contao-merger2
 * @license   LGPL-3.0+
 */

namespace Bit3\Contao\Merger2\Constraint\Node;

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

	function __construct($left, $right)
	{
		$this->left  = $left;
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
