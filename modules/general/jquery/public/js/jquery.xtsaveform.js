/**
 * jquery.xtsaveform.js 1.1.0 - https://github.com/anibalsanchez/jquery.saveform.js
 * Saves automatically all entered form fields, to restore them in the next visit.
 * 
 * Copyright (c) 2013 Anibal Sanchez (http://www.extly.com) Licensed under the MIT
 * license (http://www.opensource.org/licenses/mit-license.php). 2013/05/04
 * 
 * Based on the original work of Yannick Albert (http://yckart.com)
 * jquery.saveform.js 0.0.1 - https://github.com/yckart/jquery.saveform.js
 */

;(function($, window) {
	"use strict";
	
	// this.prefix - inorder to seperate the fields of different forms
	$.fn.xtautosave = function(prefix_param) {
		var storage = window.localStorage, $this = this, prefix;

		if (typeof $this.prefix_param === 'undefined') {
			prefix = $this.attr('id') || $this.attr('name') || 'no-Id-Or-Name-Given';
		} else {
			prefix = $this.prefix_param;
		}

		// _ $this will give unique names and will not clash with
		// other fields
		prefix += "_";
		$this.attr('prefix', prefix);

		function restoreInput(elem, index) {
			var key = $.fn.xtautosave.getKey($this, index), value = storage.getItem(key);
			
			if (!value) {
				return;
			}
			
                        console.log(elem);
                        
                        
			if ((elem.attr('type') === 'checkbox') || (elem.attr('type') === 'radio')) {
				elem.prop('checked', value);
			} else {
                            var currentVal = elem.val();
                            if(!currentVal)
				elem.val(value);
			}		
		}

		function restoreSelect(elem, index) {
			var key = $.fn.xtautosave.getSelectKey($this, index), value = storage.getItem(key);
			
			if (!value) {
				return;
			}
			// Just in case it's an array
			value = value.split(',');
			
			elem.val(value);
		}
		
		function restore() {
			var elems;
			
			elems = $this.find('input:not([type=password],[type=submit])');
			elems.each(
					function(index, elem) {
						restoreInput($(elem), index);
					});
			
			elems = $this.find('select');
			elems.each(
					function(index, elem) {
						restoreSelect($(elem), index);
					});			
		}
		
//		$this.on({
//			submit : $.fn.xtautosave.save
//		});
                $this.on({
                    change: $.fn.xtautosave.save,
                    submit: $.fn.xtautosave.reset
                });

		restore();

	};
	
	$.fn.xtautosave.getKey = function(elem, index) {
		return elem.attr('prefix') + index;
	};
	
	$.fn.xtautosave.getSelectKey = function(elem, index) {
		return 'S' + elem.attr('prefix') + index;
	};		
	
	$.fn.xtautosave.saveInput = function($this, elem, index) {
		var value, key = $.fn.xtautosave.getKey($this, index), storage = window.localStorage;
		
		if ((elem.attr('type') === 'checkbox') || (elem.attr('type') === 'radio')) {
			value = elem.prop('checked');
		} else {
			value = elem.val();
		}
		
		if ((value) && (value !== '')) {
			storage.setItem(key, value);			
		}
		else {
			storage.removeItem(key);
		}
	};
	
	$.fn.xtautosave.saveSelect = function($this, elem, index) {
		var value = elem.val(), key = $.fn.xtautosave.getSelectKey($this, index), storage = window.localStorage;
		
		if ((value) && (value !== '')) {
			storage.setItem(key, value);			
		}
		else {
			storage.removeItem(key);
		}			
	};	
	
	$.fn.xtautosave.save = function () {
		var $this = $(this), elems;		
		elems = $this.find('input:not([type=password],[type=submit])');
		elems.each(
				function(index, elem) {
					$.fn.xtautosave.saveInput($this, $(elem), index);
				});
		elems = $this.find('select');
		elems.each(
				function(index, elem) {
					$.fn.xtautosave.saveSelect($this, $(elem), index);
				});			
	};
	
        $.fn.xtautosave.reset = function () {
                       var $this = $(this), elems;		
                       elems = $this.find('input:not([type=password],[type=submit])');
                       elems.each(
                                       function(index, elem) {
                                                var key = $.fn.xtautosave.getKey($this, index);
                                                storage.removeItem(key);
                                       });
                       elems = $this.find('select');
                       elems.each(
                                       function(index, elem) {
                                               var key = $.fn.xtautosave.getSelectKey($this, index);
                                               storage.removeItem(key);
                                       });			
               };       

}(jQuery, window));