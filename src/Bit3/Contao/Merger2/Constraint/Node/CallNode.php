<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG
 *
 * @package merger2
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license LGPL-3.0+
 */

namespace Bit3\Contao\Merger2\Constraint\Node;

class CallNode implements NodeInterface
{
	/**
	 * @var NodeInterface
	 */
	protected $name;

	protected $parameters;

	function __construct($name, array $parameters)
	{
		$this->name       = $name;
		$this->parameters = $parameters;
	}

	/**
	 * @return NodeInterface
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function evaluate()
	{
		return $this->name; // TODO read variable
	}
}
