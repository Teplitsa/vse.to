/**
 * jquery.saveform.js 0.0.1 - https://github.com/yckart/jquery.saveform.js
 * Saves automatically all entered form fields.
 *
 * Copyright (c) 2013 Yannick Albert (http://yckart.com)
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 * 2013/02/14
**/
;(function ($, window) {
  //prefix - inorder to seperate the fields of different forms
  $.fn.autosave = function (prefix) {
    var storage = window.localStorage,
        $this = this;

    if (typeof prefix === 'undefined') {
      prefix = $this.attr('id') || $this.attr('name');
    }

    prefix += "_"; //_ this will give unique names and will not clash with other fields

    function save() {
      $this.find('input:not(:password,:submit), textarea, select').each(function (index) {
        var elem = $(this),
            key = prefix + index;
        storage.setItem(key, elem.attr('type') === 'checkbox' ? elem.prop('checked') : elem.val());
      });
    }

    function restore() {
      $this.find('input:not(:password,:submit), textarea, select').each(function (i) {
        var elem = $(this),
            key = prefix + i;
        if(elem.attr('type') === 'checkbox'){
          elem.prop('checked', storage.getItem(key));
        } else {
          elem.val(storage.getItem(key));
        }
      });
    }

    function reset() {
      $this.find('input:not(:password,:submit), textarea, select').each(function (index) {
        var key = prefix + index;
        storage.removeItem(key);
      });
    }

      $this.on({
          change: save,
          submit: reset
      });
    restore();
  };
}(jQuery, window));