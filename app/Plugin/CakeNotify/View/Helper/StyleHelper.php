<?php
/**
 * This file is the view helper file of the plugin.
 * Build and compress CSS for HTML email.
 * Method to build CSS by email content and compress it.
 *
 * CakeNotify: Sending email from CakePHP using task queues
 * @copyright Copyright 2016-2018, Andrey Klimov.
 * @license https://opensource.org/licenses/mit-license.php MIT License
 * @package plugin.View.Helper
 */

App::uses('CakeNotifyAppHelper', 'CakeNotify.View/Helper');
App::uses('InlineCssLib', 'Tools.Lib');

/**
 * Style helper used to build and compress CSS for email.
 *
 * @package plugin.View.Helper
 */
class StyleHelper extends CakeNotifyAppHelper {

/**
 * Process Email HTML content after rendering of the email
 *
 * @param string $layoutFile The layout file that was rendered.
 * @return void
 */
	public function afterLayout($layoutFile) {
		$content = $this->_View->Blocks->get('content');
		$content = $this->_prepareHtmlContent($content);

		if (!isset($this->InlineCss)) {
			$options = [
				'engine' => InlineCssLib::ENGINE_CSS_TO_INLINE,
				'xhtmlOutput' => true,
			];
			$this->InlineCss = new InlineCssLib($options);
		}
		$content = trim($this->InlineCss->process($content));

		$this->_View->Blocks->set('content', $content);
	}

/**
 * Make relative URLs absolute
 *
 * @param string $html HTML content for processing
 * @return string Return processed HTML string
 */
	protected function _convertUrls($html) {
		$fullBaseUrl = Configure::read('App.fullBaseUrl');
		if (!empty($fullBaseUrl)) {
			$html = preg_replace('~(?:src|action|href)=[\'"]\K/(?!/)[^\'"]*~', "$fullBaseUrl$0", $html);
		}

		return $html;
	}

/**
 * Preparing HTML content
 *
 * @param string $html HTML content for preparing
 * @return string Return prepared HTML string
 */
	protected function _prepareHtmlContent($html) {
		$html = $this->_convertUrls($html);

		return $html;
	}

/**
 * Return CSS
 *
 * @return string Return CSS
 */
	public function getStyle() {
		$result = <<<EOL
	* {
		box-sizing: border-box;
	}
	*:before, *:after {	
		box-sizing: border-box;
	}
	body {
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 14px;
		line-height: 1.42857143;
		color: #333;
		background-color: #fff;
	}
	a {
		color: #337ab7;
		text-decoration: none;
	}
	a:hover, a:focus {
		color: #23527c;
		text-decoration: underline;
		background-color: transparent;
	}
	a:focus {
		outline: thin dotted;
		outline: 5px auto;
		outline-offset: -2px;
	}
	p {
		margin: 0 0 10px;
	}
	.lead {
		margin-bottom: 20px;
		font-size: 21px;
		font-weight: 300;
		line-height: 1.4;
	}
	img {
		vertical-align: middle;
		border: 0;
	}
	.well {
		min-height: 20px;
		padding: 19px;
		margin-bottom: 20px;
		background-color: #f5f5f5;
		border: 1px solid #e3e3e3;
	}
	.well-lg {
		padding: 24px;
	}
	.well-sm {
		padding: 9px;
	}
	.well blockquote {
		border-color: #ddd;
		border-color: rgba(0, 0, 0, .15);
	}
	h1, .h1 {
		font-size: 36px;
	}
	h2, .h2 {
		font-size: 30px;
	}
	h3, .h3 {
		font-size: 24px;
	}
	h4, .h4 {
		font-size: 18px;
	}
	h1, h2, h3, h4, .h1, .h2, .h3, .h4 { 
		font-family: inherit;
		font-weight: 500;
		line-height: 1.1;
		color: inherit;
	}
	h1, .h1, h2, .h2, h3, .h3 {
		margin-top: 20px;
		margin-bottom: 10px;
	}
	h4, .h4, h5, .h5, h6, .h6 {
		margin-top: 10px;
		margin-bottom: 10px;
	}
	.well h1, .well h2, .well h3, .well h4, .well h5, .well h6, .well .h1, .well .h2, .well .h3, .well .h4, .well .h5, .well .h6 {
		margin : 0px;
	}
	strong {
		font-weight: bold;
	}
	small, .small {
		font-size: 85%;
	}
	hr {
		height: 0;  
		box-sizing: content-box;
		margin-top: 20px;
		margin-bottom: 20px;
		border: 0;
		border-top: 1px solid #eee;
	}
	.text-left {
		text-align: left;
	}
	.text-right {
		text-align: right;
	}
	.text-center {
		text-align: center;
	}
	.text-justify {
		text-align: justify;
	}
	.text-nowrap {
		white-space: nowrap;
	}
	.text-lowercase {
		text-transform: lowercase;
	}
	.text-uppercase {
		text-transform: uppercase;
	}
	.text-capitalize {
		text-transform: capitalize;
	}
	.text-muted {
		color: #777;
	}
	.text-primary {
		color: #337ab7;
	}
	.text-success {
		color: #3c763d;
	}
	.text-info {
		color: #31708f;
	}
	.text-warning {
		color: #8a6d3b;
	}
	.text-danger {
		color: #a94442;
	}
	.bg-primary {
		color: #fff;
		background-color: #337ab7;
	}
	a.bg-primary:hover, a.bg-primary:focus {
		background-color: #286090;
	}
	.bg-success {
		background-color: #dff0d8;
	}
	a.bg-success:hover, a.bg-success:focus {
		background-color: #c1e2b3;
	}
	.bg-info {
		background-color: #d9edf7;
	}
	a.bg-info:hover, a.bg-info:focus {
		background-color: #afd9ee;
	}
	.bg-warning {
		background-color: #fcf8e3;
	}
	a.bg-warning:hover, a.bg-warning:focus {
		background-color: #f7ecb5;
	}
	.bg-danger {
		background-color: #f2dede;
	}
	a.bg-danger:hover, a.bg-danger:focus {
		background-color: #e4b9b9;
	}
	.page-header {
		padding-bottom: 9px;
		margin: 40px 0 20px;
		border-bottom: 1px solid #eee;
	}
	ul, ol {
		margin-top: 0;
		margin-bottom: 10px;
	}
	ul ul, ol ul, ul ol, ol ol {
		margin-bottom: 0;
	}
	ul.list-compact, ol.list-compact {
		margin-top: 0;
		margin-bottom: 0;
	}
	.list-unstyled {
		padding-left: 0;
		list-style: none;
	}
	.list-inline {
		padding-left: 0;
		margin-left: -5px;
		list-style: none;
	}
	.list-inline > li {
		display: inline-block;
		padding-right: 5px;
		padding-left: 5px;
	}
	dl {
		margin-top: 0;
		margin-bottom: 20px;
	}
	dt, dd {
		line-height: 1.42857143;
	}
	dt {
		font-weight: bold;
	}
	dd {
		margin-left: 0;
	}
	.dl-horizontal dt {
		float: left;
		width: 160px;
		overflow: hidden;
		clear: left;
		text-align: right;
		text-overflow: ellipsis;
		white-space: normal;
	}
	.dl-horizontal dd {
		margin-left: 180px;
	}
	.clearfix:before, .clearfix:after, .dl-horizontal dd:before, .dl-horizontal dd:after, .container:before, .container:after, .container-fluid:before, .container-fluid:after, .row:before, .row:after {
		display: table;
		content: \" \";
	}
	.clearfix:after, .dl-horizontal dd:after, .container:after, .container-fluid:after, .row:after {
		clear: both;
	}
	dl.list-compact {
		margin-top: 0;
		margin-bottom: 0;
	}
	.container {
		padding-right: 15px;
		padding-left: 15px;
		margin-right: auto;
		margin-left: auto;
		width: 750px;
	}
	.row {
		margin-right: -15px;
		margin-left: -15px;
	}
	.col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12  {
		position: relative;
		min-height: 1px;
		float: left;
	}
	.col-xs-12 {
		width: 100%;
	}
	.col-xs-11 {
		width: 91.66666667%;
	}
	.col-xs-10 {
		width: 83.33333333%;
	}
	.col-xs-9 {
		width: 75%;
	}
	.col-xs-8 {
		width: 66.66666667%;
	}
	.col-xs-7 {
		width: 58.33333333%;
	}
	.col-xs-6 {
		width: 50%;
	}
	.col-xs-5 {
		width: 41.66666667%;
	}
	.col-xs-4 {
		width: 33.33333333%;
	}
	.col-xs-3 {
		width: 25%;
	}
	.col-xs-2 {
		width: 16.66666667%;
	}
	.col-xs-1 {
		width: 8.33333333%;
	}
	.col-xs-pull-12 {
		right: 100%;
	}
	.col-xs-pull-11 {
		right: 91.66666667%;
	}
	.col-xs-pull-10 {
		right: 83.33333333%;
	}
	.col-xs-pull-9 {
		right: 75%;
	}
	.col-xs-pull-8 {
		right: 66.66666667%;
	}
	.col-xs-pull-7 {
		right: 58.33333333%;
	}
	.col-xs-pull-6 {
		right: 50%;
	}
	.col-xs-pull-5 {
		right: 41.66666667%;
	}
	.col-xs-pull-4 {
		right: 33.33333333%;
	}
	.col-xs-pull-3 {
		right: 25%;
	}
	.col-xs-pull-2 {
		right: 16.66666667%;
	}
	.col-xs-pull-1 {
		right: 8.33333333%;
	}
	.col-xs-pull-0 {
		right: auto;
	}
	.col-xs-push-12 {
		left: 100%;
	}
	.col-xs-push-11 {
		left: 91.66666667%;
	}
	.col-xs-push-10 {
		left: 83.33333333%;
	}
	.col-xs-push-9 {
		left: 75%;
	}
	.col-xs-push-8 {
		left: 66.66666667%;
	}
	.col-xs-push-7 {
		left: 58.33333333%;
	}
	.col-xs-push-6 {
		left: 50%;
	}
	.col-xs-push-5 {
		left: 41.66666667%;
	}
	.col-xs-push-4 {
		left: 33.33333333%;
	}
	.col-xs-push-3 {
		left: 25%;
	}
	.col-xs-push-2 {
		left: 16.66666667%;
	}
	.col-xs-push-1 {
		left: 8.33333333%;
	}
	.col-xs-push-0 {
		left: auto;
	}
	.col-xs-offset-12 {
		margin-left: 100%;
	}
	.col-xs-offset-11 {
		margin-left: 91.66666667%;
	}
	.col-xs-offset-10 {
		margin-left: 83.33333333%;
	}
	.col-xs-offset-9 {
		margin-left: 75%;
	}
	.col-xs-offset-8 {
		margin-left: 66.66666667%;
	}
	.col-xs-offset-7 {
		margin-left: 58.33333333%;
	}
	.col-xs-offset-6 {
		margin-left: 50%;
	}
	.col-xs-offset-5 {
		margin-left: 41.66666667%;
	}
	.col-xs-offset-4 {
		margin-left: 33.33333333%;
	}
	.col-xs-offset-3 {
		margin-left: 25%;
	}
	.col-xs-offset-2 {
		margin-left: 16.66666667%;
	}
	.col-xs-offset-1 {
		margin-left: 8.33333333%;
	}
	.col-xs-offset-0 {
		margin-left: 0;
	}
	table {
		background-color: transparent;
		border-spacing: 0;
		border-collapse: collapse;
	}
	td, th {
		padding: 0;
	}
	th {
		text-align: left;
	}
	caption {
		padding-top: 8px;
		padding-bottom: 8px;
		color: #777;
		text-align: left;
	}
	.table {
		width: 100%;
		max-width: 100%;
		margin-bottom: 20px;
	}
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		padding: 8px;
		line-height: 1.42857143;
		vertical-align: top;
		border-top: 1px solid #ddd;
	}
	.table > thead > tr > th {
		vertical-align: bottom;
		border-bottom: 2px solid #ddd;
	}
	.table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {
		border-top: 0;
	}
	.table > tbody + tbody {
		border-top: 2px solid #ddd;
	}
	.table .table {
		background-color: #fff;
	}
	.table > thead > tr > td.active, .table > tbody > tr > td.active, .table > tfoot > tr > td.active, .table > thead > tr > th.active, .table > tbody > tr > th.active, .table > tfoot > tr > th.active, .table > thead > tr.active > td, .table > tbody > tr.active > td, .table > tfoot > tr.active > td, .table > thead > tr.active > th, .table > tbody > tr.active > th, .table > tfoot > tr.active > th {
		background-color: #f5f5f5;
	}
	.table > thead > tr > td.success, .table > tbody > tr > td.success, .table > tfoot > tr > td.success, .table > thead > tr > th.success, .table > tbody > tr > th.success, .table > tfoot > tr > th.success, .table > thead > tr.success > td, .table > tbody > tr.success > td, .table > tfoot > tr.success > td, .table > thead > tr.success > th, .table > tbody > tr.success > th, .table > tfoot > tr.success > th {
		background-color: #dff0d8;
	}
	.table > thead > tr > td.info, .table > tbody > tr > td.info, .table > tfoot > tr > td.info, .table > thead > tr > th.info, .table > tbody > tr > th.info, .table > tfoot > tr > th.info, .table > thead > tr.info > td, .table > tbody > tr.info > td, .table > tfoot > tr.info > td, .table > thead > tr.info > th, .table > tbody > tr.info > th, .table > tfoot > tr.info > th {
		background-color: #d9edf7;
	}
	.table > thead > tr > td.warning, .table > tbody > tr > td.warning, .table > tfoot > tr > td.warning, .table > thead > tr > th.warning, .table > tbody > tr > th.warning, .table > tfoot > tr > th.warning, .table > thead > tr.warning > td, .table > tbody > tr.warning > td, .table > tfoot > tr.warning > td, .table > thead > tr.warning > th, .table > tbody > tr.warning > th, .table > tfoot > tr.warning > th {
		background-color: #fcf8e3;
	}
	.table > thead > tr > td.danger, .table > tbody > tr > td.danger, .table > tfoot > tr > td.danger, .table > thead > tr > th.danger, .table > tbody > tr > th.danger, .table > tfoot > tr > th.danger, .table > thead > tr.danger > td, .table > tbody > tr.danger > td, .table > tfoot > tr.danger > td, .table > thead > tr.danger > th, .table > tbody > tr.danger > th, .table > tfoot > tr.danger > th {
		background-color: #f2dede;
	}
	.table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td {
		padding: 5px;
	}
	.table-bordered {
		border: 1px solid #ddd;
	}
	.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
		border: 1px solid #ddd;
	}
	.table-bordered > thead > tr > th, .table-bordered > thead > tr > td {
		border-bottom-width: 2px;
	}
	.table-striped > tbody > tr:nth-of-type(odd) {
		background-color: #f9f9f9;
	}
	.table-hover > tbody > tr:hover {
		background-color: #f5f5f5;
	}
	.table-hover > tbody > tr > td.active:hover, .table-hover > tbody > tr > th.active:hover, .table-hover > tbody > tr.active:hover > td, .table-hover > tbody > tr:hover > .active, .table-hover > tbody > tr.active:hover > th {
		background-color: #e8e8e8;
	}	
	.table-hover > tbody > tr > td.success:hover, .table-hover > tbody > tr > th.success:hover, .table-hover > tbody > tr.success:hover > td, .table-hover > tbody > tr:hover > .success, .table-hover > tbody > tr.success:hover > th {
		background-color: #d0e9c6;
	}	
	.table-hover > tbody > tr > td.info:hover, .table-hover > tbody > tr > th.info:hover, .table-hover > tbody > tr.info:hover > td, .table-hover > tbody > tr:hover > .info, .table-hover > tbody > tr.info:hover > th {
		background-color: #c4e3f3;
	}	
	.table-hover > tbody > tr > td.warning:hover, .table-hover > tbody > tr > th.warning:hover, .table-hover > tbody > tr.warning:hover > td, .table-hover > tbody > tr:hover > .warning, .table-hover > tbody > tr.warning:hover > th {
		background-color: #faf2cc;
	}	
	.table-hover > tbody > tr > td.danger:hover, .table-hover > tbody > tr > th.danger:hover, .table-hover > tbody > tr.danger:hover > td, .table-hover > tbody > tr:hover > .danger, .table-hover > tbody > tr.danger:hover > th {
		background-color: #ebcccc;
	}
	table col[class*=\"col-\"] {
		position: static;
		display: table-column;
		float: none;
	}
	table td[class*=\"col-\"], table th[class*=\"col-\"] {
		position: static;
		display: table-cell;
		float: none;
	}
	blockquote {
		padding: 10px 20px;
		margin: 0 0 20px;
		font-size: 17.5px;
		border-left: 5px solid #eee;
	}
	blockquote p:last-child, blockquote ul:last-child, blockquote ol:last-child {
		margin-bottom: 0;
	}
	blockquote footer, blockquote small, blockquote .small {
		display: block;
		font-size: 80%;
		line-height: 1.42857143;
		color: #777;
	}
	.blockquote-reverse, blockquote.pull-right {
		padding-right: 15px;
		padding-left: 0;
		text-align: right;
		border-right: 5px solid #eee;
		border-left: 0;
	}
	.alert {
		padding: 15px;
		margin-bottom: 20px;
		border: 1px solid transparent;
		border-radius: 4px;
	}
	.alert h4 {
		margin-top: 0;
		color: inherit;
	}
	.alert .alert-link {
		font-weight: bold;
	}
	.alert > p, .alert > ul {
		margin-bottom: 0;
	}
	.alert > p + p {
		margin-top: 5px;
	}
	.alert-dismissable, .alert-dismissible {
		padding-right: 35px;
	}
	.alert-dismissable .close, .alert-dismissible .close {
		position: relative;
		top: -2px;
		right: -21px;
		color: inherit;
	}
	.alert-success {
		color: #3c763d;
		background-color: #dff0d8;
		border-color: #d6e9c6;
	}
	.alert-success hr {
		border-top-color: #c9e2b3;
	}
	.alert-success .alert-link {
		color: #2b542c;
	}
	.alert-info {
		color: #31708f;
		background-color: #d9edf7;
		border-color: #bce8f1;
	}
	.alert-info hr {
		border-top-color: #a6e1ec;
	}
	.alert-info .alert-link {
		color: #245269;
	}
	.alert-warning {
		color: #8a6d3b;
		background-color: #fcf8e3;
		border-color: #faebcc;
	}
	.alert-warning hr {
		border-top-color: #f7e1b5;
	}
	.alert-warning .alert-link {
		color: #66512c;
	}
	.alert-danger {
		color: #a94442;
		background-color: #f2dede;
		border-color: #ebccd1;
	}
	.alert-danger hr {
		border-top-color: #e4b9c0;
	}
	.alert-danger .alert-link {
		color: #843534;
	}
	footer, header {
		display: block;
	}
	.img-thumbnail {
		display: inline-block;
		max-width: 100%;
		height: auto;
		padding: 4px;
		line-height: 1.42857143;
		background-color: #fff;
		border: 1px solid #ddd;
		border-radius: 4px;
	}
EOL;

		return $result;
	}
}
