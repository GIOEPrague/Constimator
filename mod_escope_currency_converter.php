<?php
/**
 * @package	Joomla.Site
 * @subpackage	mod_escope_currency_convertor
 * @copyright	Copyright (C) Jan Linhart aka escope.cz. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';


require JModuleHelper::getLayoutPath('mod_escope_currency_converter', $params->get('layout', 'default'));
