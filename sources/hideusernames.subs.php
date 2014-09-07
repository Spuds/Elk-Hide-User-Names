<?php

/**
 * @package "Hide User Names from Guests" Addon for Elkarte
 * @author Spuds
 * @copyright (c) 2011-2014 Spuds
 * @license This Source Code is subject to the terms of the Mozilla Public License
 * version 1.1 (the "License"). You can obtain a copy of the License at
 * http://mozilla.org/MPL/1.1/.
 *
 * @version 1.0
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 * igms_hide_user_names()
 *
 * - Admin Hook, integrate_general_mod_settings, called from AddonSettings.controller.php
 * - used to add simple items to the general addon settings tab
 *
 * @param mixed[] $config_vars
 */
function igms_hide_user_names(&$config_vars)
{
	loadLanguage('hideusernames');
	$config_vars = array_merge($config_vars, array(array('text', 'hideusernames')));
}

/**
 * ob_hide_user_names()
 *
 * - buffer hook, integrate_buffer, called from ob_exit via call_integration_buffer
 * - used to modify the contents of the output buffer before its sent to the browser
 *
 * @param string $buffer
 */
function ob_hide_user_names($buffer)
{
	global $modSettings, $scripturl, $user_info, $txt;

	if ($user_info['is_guest'] && !empty($modSettings['hideusernames']))
	{
		loadLanguage('hideusernames');

		// Another abuse of regex to find anchor tags
		$reg_ex = '~<a(?:\s+|\s[^>]*\s)href=[""\']' . preg_quote($scripturl, '~') . '\?action=profile;(?:[^"]+;)?u=([^"]*)"[^>]*>(.*?[^<]+[.]*)</a>~';
		$buffer = preg_replace($reg_ex, $modSettings['hideusernames'], $buffer);

		// Quotes, oh my, leave the link just remove the name ....
		$reg_ex = '~(<a(?:\s+|\s[^>]*\s)href=[""\']' . preg_quote($scripturl, '~') . '\?topic=(?:[^"]+)?"[^>]*>' . $txt['quote_from'] . ':)(.*?)(' . $txt['on'] . '.*?[^<]+[.]*</a>)~';
		$buffer = preg_replace($reg_ex, "\\1 " . $modSettings['hideusernames'] . " \\3", $buffer);

		// Hummm this too for users who add author= to a quote
		$reg_ex = '~(<div(?:\s+|\s[^>]*\s)class=[""\']topslice_quote[""\'][^>]*>' . $txt['quote_from'] . ':)(.*?)(</div>)~';
		$buffer = preg_replace($reg_ex, "\\1 " . $modSettings['hideusernames'] . " \\3", $buffer);

		// The Print page too? the pain the pain
		$reg_ex = '~(' . $txt['post_by'] . ':\s<strong>)(.*?)(</strong>\s' . $txt['on'] . '\s<strong>.*?</strong>)~';
		$buffer = preg_replace($reg_ex, "\\1 " . $modSettings['hideusernames'] . " \\3", $buffer);
	}

	// All done
	return $buffer;
}