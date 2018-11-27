<?php
/**
 * This file is the helper file of the application.
 * Implements geshi syntax highlighting for cakephp
 * Originally based off of http://www.gignus.com/code/code.phps
 *
 * @author Mark story
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2008-2012 Mark Story <mark@mark-story.com>
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * @package app.View.Helper
 */

App::uses('GeshiHelper', 'Geshi.View/Helper');

/**
 * Implements geshi syntax highlighting for cakephp.
 *
 * @package app.View.Helper
 */
class GeshiExtHelper extends GeshiHelper {

/**
 * Highlight all the provided text as a given language.
 *
 * @param string $text The text to highight.
 * @param string $language The language to highlight as.
 * @param bool $withStylesheet If true will include GeSHi's generated stylesheet.
 * @param mixed $selectLine An array of line numbers to highlight, or just a line number on its own.
 * @return string Highlighted HTML.
 */
	public function highlightText($text = '', $language = '', $withStylesheet = false, $selectLine = []) {
		$this->_getGeshi();
		$this->_geshi->set_source($text);
		$this->_geshi->set_language($language);
		if (!empty($selectLine)) {
			$this->_geshi->highlight_lines_extra($selectLine);
		}

		if (!$withStylesheet) {
			$result = $this->_geshi->parse_code();
		} else {
			$result = $this->_includeStylesheet() . $this->_geshi->parse_code();
		}

		return $result;
	}

}
