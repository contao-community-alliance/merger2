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

interface NodeInterface
{
	/**
	 * Evaluate the node and return the next result.
	 * 
	 * @return mixed
	 */
	public function evaluate();
}
