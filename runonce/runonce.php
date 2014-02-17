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
