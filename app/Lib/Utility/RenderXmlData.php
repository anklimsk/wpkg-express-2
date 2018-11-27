<?php
/**
 * This file is the util file of the application.
 * RenderXmlData Utility.
 * Methods for render XML and a lists of schema validation errors.
 *
 * This file is part of wpkgExpress II.
 *
 * wpkgExpress II is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * wpkgExpress II is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with wpkgExpress II. If not, see <https://www.gnu.org/licenses/>.
 *
 * wpkgExpress II: A web-based frontend to WPKG.
 *  Based on wpkgExpress by Brian White.
 * @copyright Copyright 2009, Brian White.
 * @copyright Copyright 2018, Andrey Klimov.
 * @package app.Lib.Utility
 */

App::uses('Xml', 'Utility');

/**
 * XML helper library.
 * Methods to render XML and a lists of schema validation errors.
 *
 * @package app.Lib.Utility
 */
class RenderXmlData {

/**
 * Create comment block from element
 *
 * @param DOMDocument $xmlObject Object of document for processing
 * @param DOMXPath $xpath Object of DOMXPath
 * @param string $query XPath query to select target elements
 * @param string $commentPrefix Prefix of comment block
 * @param string $commentPostfix Postfix of comment block
 * @param string $methodName Name of method to create comment block
 * @return void
 */
	public static function createComment(DOMDocument $xmlObject, DOMXPath $xpath, $query = null, $commentPrefix = null, $commentPostfix = null, $methodName = null) {
		if (empty($query)) {
			return;
		}

		if (empty($methodName)) {
			$methodName = 'createComment';
		}
		$entries = $xpath->query($query);
		foreach ($entries as $entry) {
			$comment = $xmlObject->$methodName($commentPrefix . $entry->nodeValue . $commentPostfix);
			$entry->parentNode->replaceChild($comment, $entry);
		}
	}

/**
 * Return XML string from data array
 *
 * @param array $xmlDataArray Data of XML
 * @param bool $formatxml If True, format XML output
 * @return string|bool Return XML string, or False on failure
 */
	public static function renderXml($xmlDataArray = [], $formatxml = true) {
		if (!is_array($xmlDataArray)) {
			return $xmlDataArray;
		}

		$xmlOptions = [
			'format' => 'tags',
			'return' => 'domdocument'
		];
		if ($formatxml) {
			$xmlOptions['pretty'] = true;
		}

		$xmlObject = Xml::fromArray($xmlDataArray, $xmlOptions);
		$xpath = new DOMXPath($xmlObject);

		$commentsInfo = [
			XML_SPECIFIC_TAG_NOTES => [
				'prefix' => XML_EXPORT_NOTES_COMMENTS_PREFIX,
				'postfix' => XML_EXPORT_NOTES_COMMENTS_POSTFIX,
			],
			XML_SPECIFIC_TAG_TEMPLATE => [
				'prefix' => XML_EXPORT_TEMPLATE_PREFIX,
				'postfix' => XML_EXPORT_TEMPLATE_POSTFIX,
			]
		];

		foreach ($commentsInfo as $commentTag => $commentPrefixes) {
			$commentPrefix = $commentPrefixes['prefix'];
			$commentPostfix = $commentPrefixes['postfix'];
			$query = '//*/' . XML_SPECIFIC_TAG_DISABLED . '/*/' . $commentTag;
			$methodName = 'createCDATASection';
			self::createComment($xmlObject, $xpath, $query, $commentPrefix, $commentPostfix, $methodName);

			$query = '//*/' . $commentTag;
			$methodName = 'createComment';
			self::createComment($xmlObject, $xpath, $query, $commentPrefix, $commentPostfix, $methodName);
		}

		$query = '//*/' . XML_SPECIFIC_TAG_DISABLED . '/*';
		$entries = $xpath->query($query);
		foreach ($entries as $entry) {
			$comment = $xmlObject->createComment($xmlObject->saveXML($entry));
			$entry->parentNode->parentNode->appendChild($comment);
		}
		if ($entries->length) {
			$entry->parentNode->parentNode->removeChild($entry->parentNode);
		}

		return $xmlObject->saveXML();
	}

/**
 * Return formatted list of XML validation errors
 *
 * @param array $data Data of XML validation errors
 * @return string Return formatted list of XML validation errors
 */
	public static function renderValidateMessages($data = []) {
		$result = '';
		if (empty($data)) {
			return $result;
		}
		$result = __('XML failed to pass XSD schema validation');
		foreach ($data as $msgtype => $messages) {
			$result .= '<br /><b>' . $msgtype . ':</b><br /><ul>';
			foreach ($messages as $msginfo) {
				$result .= '<li>' . $msginfo['message'] . ' (' . __x('XML validation messages', 'line') .
					' ' . $msginfo['line'] . ')' . '</li>';
			}
			$result .= '</ul>';
		}

		return $result;
	}
}
