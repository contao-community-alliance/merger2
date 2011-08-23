<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


/**
 * Class MergerModuleWizard
 *
 * Provide methods to handle modules of a module merger.
 */
class MergerModuleWizard extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = false;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'value':
				$this->varValue = deserialize($varValue);
				break;

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	protected function generateOptions($items, $value) {
		$options = '';
		foreach ($items as $item)
		{
			if (isset($item['label'])) {
				$options .= '<optgroup label="'.specialchars($item['label']).'">';
				$options .= $this->generateOptions($item['items'], $value);
				$options .= '</optgroup>';
			} else {
				$options .= '<option value="'.specialchars($item['id']).'"'.$this->optionSelected($item['id'], $value).'>'.$item['name'].'</option>';
			}
		}
		return $options;
	}

	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$this->import('Database');

		$arrButtons = array('copy', 'up', 'down', 'delete');
		$strCommand = 'cmd_' . $this->strField;

		// Change the order
		if ($this->Input->get($strCommand) && is_numeric($this->Input->get('cid')) && $this->Input->get('id') == $this->currentRecord)
		{
			switch ($this->Input->get($strCommand))
			{
				case 'copy':
					$this->varValue = array_duplicate($this->varValue, $this->Input->get('cid'));
					break;

				case 'up':
					$this->varValue = array_move_up($this->varValue, $this->Input->get('cid'));
					break;

				case 'down':
					$this->varValue = array_move_down($this->varValue, $this->Input->get('cid'));
					break;

				case 'delete':
					$this->varValue = array_delete($this->varValue, $this->Input->get('cid'));
					break;
			}
		}

		// Get all modules from DB
		$modules = array(
			array('id' => '-', 'name' => '-'),
			array('label' => &$GLOBALS['TL_LANG']['merger2']['legend_article'], 'items' => array(
				array('id' => 'article', 'name' => &$GLOBALS['TL_LANG']['merger2']['article']),
				array('id' => 'inherit_articles', 'name' => &$GLOBALS['TL_LANG']['merger2']['inherit_articles']),
				array('id' => 'inherit_all_articles', 'name' => &$GLOBALS['TL_LANG']['merger2']['inherit_all_articles']),
				array('id' => 'inherit_articles_fallback', 'name' => &$GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback']),
				array('id' => 'inherit_all_articles_fallback', 'name' => &$GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback'])
			)),
			array('label' => &$GLOBALS['TL_LANG']['merger2']['legend_inherit_module'], 'items' => array(
				array('id' => 'inherit_modules', 'name' => &$GLOBALS['TL_LANG']['merger2']['inherit_modules']),
				array('id' => 'inherit_all_modules', 'name' => &$GLOBALS['TL_LANG']['merger2']['inherit_all_modules'])
			))
		);

		$objTheme = $this->Database->execute("SELECT * FROM tl_theme ORDER BY name");
		while ($objTheme->next())
		{
			$arrThemeModules = array();
			$objModules = $this->Database->prepare("SELECT id, name FROM tl_module WHERE pid=? AND id!=? ORDER BY name")->execute($objTheme->id, $this->currentRecord);
			while ($objModules->next())
			{
				$arrThemeModules[] = $objModules->row();
			}
			if (count($arrThemeModules))
			{
				$modules[] = array(
					'label' => $objTheme->name,
					'items' => $arrThemeModules
				);
			}
		}

		$objRow = $this->Database->prepare("SELECT * FROM " . $this->strTable . " WHERE id=?")
								 ->limit(1)
								 ->execute($this->currentRecord);

		$strField = $this->strField;
		$arrModules = deserialize($objRow->$strField);

		// Get new value
		if ($this->Input->post('FORM_SUBMIT') == $this->strTable)
		{
			$this->varValue = $this->Input->post($this->strId);
		}

		// Make sure there is at least an empty array
		if (!is_array($this->varValue) || !$this->varValue[0])
		{
			$this->varValue = array('');
		}

		// Save the value
		if ($this->Input->get($strCommand) || $this->Input->post('FORM_SUBMIT') == $this->strTable)
		{
			$this->Database->prepare("UPDATE " . $this->strTable . " SET " . $this->strField . "=? WHERE id=?")
						   ->execute(serialize($this->varValue), $this->currentRecord);

			// Reload the page
			if (is_numeric($this->Input->get('cid')) && $this->Input->get('id') == $this->currentRecord)
			{
				$this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($strCommand, '/') . '=[^&]*/i', '', $this->Environment->request)));
			}
		}

		$return = "<script type='text/javascript'>
	/**
	 * Merger Module wizard
	 * @param object
	 * @param string
	 * @param string
	 */
	if (!Backend.mergerModuleWizard) Backend.mergerModuleWizard = function(el, command, id)
	{
		var table = $(id);
		var tbody = table.getFirst().getNext();
		var parent = $(el).getParent('tr');
		var rows = tbody.getChildren();

		Backend.getScrollOffset();

		switch (command)
		{
			case 'copy':
				var tr = new Element('tr');
				var childs = parent.getChildren();

				for (var i=0; i<childs.length; i++)
				{
					var next = childs[i].clone(true).injectInside(tr);
					next.getFirst().value = childs[i].getFirst().value;
				}

				tr.injectAfter(parent);
				break;

			case 'up':
				parent.getPrevious() ? parent.injectBefore(parent.getPrevious()) : parent.injectInside(tbody);
				break;

			case 'down':
				parent.getNext() ? parent.injectAfter(parent.getNext()) : parent.injectBefore(tbody.getFirst());
				break;

			case 'delete':
				(rows.length > 1) ? parent.destroy() : null;
				break;
		}

		rows = tbody.getChildren();

		for (var i=0; i<rows.length; i++)
		{
			var childs = rows[i].getChildren();

			for (var j=0; j<childs.length; j++)
			{
				var first = childs[j].getFirst();

				if (first.type == 'text' || first.type == 'select-one')
				{
					first.name = first.name.replace(/\[[0-9]+\]/ig, '[' + i + ']');
				}
			}
		}
	}; </script>";

		// Add label and return wizard
		$return .= '<table cellspacing="0" cellpadding="0" id="ctrl_'.$this->strId.'" class="tl_modulewizard" summary="Module wizard">
  <thead>
  <tr>
    <th>'.$GLOBALS['TL_LANG'][$this->strTable]['label_content'].'</th>
    <th>'.$GLOBALS['TL_LANG'][$this->strTable]['label_condition'].'</th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>';

		// Load tl_article language file
		$this->loadLanguageFile('tl_article');

		// Add input fields
		for ($i=0; $i<count($this->varValue); $i++)
		{
			// Add modules
			$options = $this->generateOptions($modules, $this->varValue[$i]['content']);

			$return .= '
  <tr>
    <td><select name="'.$this->strId.'['.$i.'][content]" class="tl_select" onfocus="Backend.getScrollOffset();" style="width: 290px;">'.$options.'</select></td>
    <td><input name="'.$this->strId.'['.$i.'][condition]" type="text" class="tl_text" onfocus="Backend.getScrollOffset();" value="'.specialchars($this->varValue[$i]['condition']).'" style="width: 290px;" /></td>
    <td>';

			foreach ($arrButtons as $button)
			{
				$return .= '<a href="'.$this->addToUrl('&amp;'.$strCommand.'='.$button.'&amp;cid='.$i.'&amp;id='.$this->currentRecord).'" title="'.specialchars($GLOBALS['TL_LANG'][$this->strTable]['wz_'.$button]).'" onclick="Backend.mergerModuleWizard(this, \''.$button.'\',  \'ctrl_'.$this->strId.'\'); return false;">'.$this->generateImage($button.'.gif', $GLOBALS['TL_LANG'][$this->strTable]['wz_'.$button], 'class="tl_listwizard_img"').'</a> ';
			}

			$return .= '</td>
  </tr>';
		}

		return $return.'
  </tbody>
  </table>';
	}
}
