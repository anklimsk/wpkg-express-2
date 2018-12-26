(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a)}else{if(typeof module==="object"&&typeof module.exports==="object"){a(require("jquery"))}else{a(jQuery)}}}(function(a){a.timeago.settings.strings={prefixAgo:null,prefixFromNow:null,suffixAgo:"ago",suffixFromNow:"from now",seconds:"less than a minute",minute:"about a minute",minutes:"%d minutes",hour:"about an hour",hours:"about %d hours",day:"a day",days:"%d days",month:"about a month",months:"%d months",year:"about a year",years:"%d years",wordSeparator:" ",numbers:[]}}));
/*!
 * Bootstrap-select v1.12.4 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2017 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
(function(a,b){if(typeof define==="function"&&define.amd){define(["jquery"],function(c){return(b(c))})}else{if(typeof module==="object"&&module.exports){module.exports=b(require("jquery"))}else{b(a.jQuery)}}}(this,function(a){(function(b){b.fn.selectpicker.defaults={noneSelectedText:"Nothing selected",noneResultsText:"No results match {0}",countSelectedText:function(d,c){return(d==1)?"{0} item selected":"{0} items selected"},maxOptionsText:function(c,d){return[(c==1)?"Limit reached ({n} item max)":"Limit reached ({n} items max)",(d==1)?"Group limit reached ({n} item max)":"Group limit reached ({n} items max)"]},selectAllText:"Select All",deselectAllText:"Deselect All",multipleSeparator:", "}})(a)}));
/*!
 * Ajax Bootstrap Select
 *
 * Extends existing [Bootstrap Select] implementations by adding the ability to search via AJAX requests as you type. Originally for CROSCON.
 *
 * @version 1.4.4
 * @author Adam Heim - https://github.com/truckingsim
 * @link https://github.com/truckingsim/Ajax-Bootstrap-Select
 * @copyright 2018 Adam Heim
 * @license Released under the MIT license.
 *
 * Contributors:
 *   Mark Carver - https://github.com/markcarver
 *
 * Last build: 2018-06-12 11:53:57 AM EDT
 */
;!(function(a){
/*!
 * English translation for the "en-US" and "en" language codes.
 * Mark Carver <mark.carver@me.com>
 */
;a.fn.ajaxSelectPicker.locale["en-US"]={currentlySelected:"Currently Selected",emptyTitle:"Select and begin typing",errorText:"Unable to retrieve results",searchPlaceholder:"Search...",statusInitialized:"Start typing a search query",statusNoResults:"No Results",statusSearching:"Searching...",statusTooShort:"Please enter more characters"};a.fn.ajaxSelectPicker.locale.en=a.fn.ajaxSelectPicker.locale["en-US"]})(jQuery);