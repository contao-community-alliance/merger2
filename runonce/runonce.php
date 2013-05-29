<?php

/**
 * Merger² - Module Merger
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Merger²
 * @license    LGPL
 * @filesource
 */


class Merger2Runonce extends Frontend
{

	/**
	 * Initialize the object
	 */
	public function __construct()
	{
		parent::__construct();

		$this->import('Database');
	}

	public function run()
	{
		$this->import('Database');
		if ($this->Database->fieldExists('mergerMode', 'tl_module')) {
			$this->Database->execute("ALTER TABLE tl_module CHANGE mergerMode merger_mode varchar(14) NOT NULL default ''");
		}
		if ($this->Database->fieldExists('mergerTemplate', 'tl_module')) {
			$this->Database->execute("ALTER TABLE tl_module CHANGE mergerTemplate merger_template varchar(64) NOT NULL default 'modulemerger_default'");
		}
		if ($this->Database->fieldExists('mergerContainer', 'tl_module')) {
			$this->Database->execute("ALTER TABLE tl_module CHANGE mergerContainer merger_container char(1) NOT NULL default ''");
		}
		if ($this->Database->fieldExists('mergerData', 'tl_module')) {
			$this->Database->execute("ALTER TABLE tl_module CHANGE mergerData merger_data blob NULL");
		}
	}
}

/**
 * Instantiate controller
 */
$objMerger2Runonce = new Merger2Runonce();
$objMerger2Runonce->run();
