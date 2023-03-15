/// select2 plugin
jQuery(function($) {
    (function (Handsontable) {

        "use strict";
        
        var Select2Editor = Handsontable.editors.TextEditor.prototype.extend();

        Select2Editor.prototype.prepare = function (row, col, prop, td, originalValue, cellProperties) {

            Handsontable.editors.TextEditor.prototype.prepare.apply(this, arguments);

            this.options = {};

            if (this.cellProperties.select2Options) {
                this.options = $.extend(this.options, cellProperties.select2Options);
            }
        };

        Select2Editor.prototype.createElements = function () {
            this.$body = $(document.body);

            this.TEXTAREA = document.createElement('select');

            // Handsontable copy paste plugin calls this.TEXTAREA.select()
            this.TEXTAREA.select = function () {};

            // this.TEXTAREA.setAttribute('type', 'text');
            this.$textarea = $(this.TEXTAREA);

            Handsontable.dom.addClass(this.TEXTAREA, 'handsontableInput');

            this.textareaStyle = this.TEXTAREA.style;
            this.textareaStyle.width = 0;
            this.textareaStyle.height = 0;

            this.TEXTAREA_PARENT = document.createElement('DIV');
            Handsontable.dom.addClass(this.TEXTAREA_PARENT, 'handsontableInputHolder');


            this.textareaParentStyle = this.TEXTAREA_PARENT.style;
            this.textareaParentStyle.top = 0;
            this.textareaParentStyle.left = 0;
            this.textareaParentStyle.display = 'none';
            this.textareaParentStyle.width = "200px";

            this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

            this.instance.rootElement.appendChild(this.TEXTAREA_PARENT);

            var that = this;
            this.instance._registerTimeout(setTimeout(function () {
                that.refreshDimensions();
            }, 0));
        };

        var onSelect2Changed = function () {

            let options = this.cellProperties.select2Options;

            if (!options.multiple) {
                this.close();
                this.finishEditing();
            }
        };
        var onSelect2Closed = function () {

            let options = this.cellProperties.select2Options;
            if (!options.multiple) {
                this.close();
                this.finishEditing();
            }
        };
        var onBeforeKeyDown = function (event) {
            var instance = this;
            var that = instance.getActiveEditor();

            var keyCodes = Handsontable.helper.keyCode;
            var ctrlDown = (event.ctrlKey || event.metaKey) && !event.altKey; //catch CTRL but not right ALT (which in some systems triggers ALT+CTRL)


            //Process only events that have been fired in the editor
            if (!$(event.target).hasClass('select2-input') || event.isImmediatePropagationStopped()) {
                return;
            }
            if (event.keyCode === 17 || event.keyCode === 224 || event.keyCode === 91 || event.keyCode === 93) {
                //when CTRL or its equivalent is pressed and cell is edited, don't prepare selectable text in textarea
                event.stopImmediatePropagation();
                return;
            }

            var target = event.target;


            switch (event.keyCode) {
                case keyCodes.ARROW_RIGHT:
                    if (Handsontable.dom.getCaretPosition(target) !== target.value.length) {
                        event.stopImmediatePropagation();
                    } else {
                        that.$textarea.select2('close');
                    }
                    break;

                case keyCodes.ARROW_LEFT:
                    if (Handsontable.dom.getCaretPosition(target) !== 0) {
                        event.stopImmediatePropagation();
                    } else {
                        that.$textarea.select2('close');
                    }
                    break;

                case keyCodes.ENTER:
                    var selected = that.instance.getSelected();
                    var isMultipleSelection = !(selected[0] === selected[2] && selected[1] === selected[3]);
                    if ((ctrlDown && !isMultipleSelection) || event.altKey) { //if ctrl+enter or alt+enter, add new line
                        if (that.isOpened()) {
                            that.val(that.val() + '\n');
                            that.focus();
                        } else {
                            that.beginEditing(that.originalValue + '\n')
                        }
                        event.stopImmediatePropagation();
                    }
                    event.preventDefault(); //don't add newline to field
                    break;

                case keyCodes.A:
                case keyCodes.X:
                case keyCodes.C:
                case keyCodes.V:
                    if (ctrlDown) {
                        event.stopImmediatePropagation(); //CTRL+A, CTRL+C, CTRL+V, CTRL+X should only work locally when cell is edited (not in table context)
                    }
                    break;

                case keyCodes.BACKSPACE:
                    let txt = $(that.TEXTAREA_PARENT).find("input").val();
                    $(that.TEXTAREA_PARENT).find("input").val(txt.substr(0,txt.length-1)).trigger("keyup.select2");

                    event.stopImmediatePropagation();
                    break;
                case keyCodes.DELETE:
                case keyCodes.HOME:
                case keyCodes.END:
                    event.stopImmediatePropagation(); //backspace, delete, home, end should only work locally when cell is edited (not in table context)
                    break;
            }

        };

        Select2Editor.prototype.open = function (keyboardEvent) {
            this.refreshDimensions();
            this.textareaParentStyle.display = 'block';
            this.textareaParentStyle.zIndex = 20000;
            this.instance.addHook('beforeKeyDown', onBeforeKeyDown);

            this.$textarea.css({
                'height': $(this.TD).outerHeight() + 25,
                'width': $(this.TD).outerWidth(),
            });

            //display the list
            this.$textarea.hide();

            var selectedValues = this.$textarea.value;

            var options = $.extend({}, this.options, {
                width: "100%",
                search_contains: true
            });

            if (options.multiple) {
                this.$textarea.attr("multiple", true);
            } else {
                this.$textarea.attr("multiple", false);
            }

            if ( this.options.hasOwnProperty('loadDataDynamically') ) {

                // options.data = this.originalValue;
                // this.originalValue = $(this.TD).text();

                var currentObj = this;

                this.options.ajax = {
                                        dataType: "json",
                                        url: window.smart_manager.sm_ajax_url,
                                        data: function(params) {
                                            let reqParams = {
                                                            searchTerm: params.term,
                                                            // selectedTerm: 
                                                            searchPage: params.page || 1,
                                                            cmd: currentObj.options.func_nm,
                                                            security: window.smart_manager.sm_nonce,
                                                            active_module: window.smart_manager.dashboard_key,
                                                            is_public: ( window.smart_manager.sm_dashboards_public.indexOf(window.smart_manager.dashboard_key) != -1 ) ? 1 : 0,
                                                            active_module_title: window.smart_manager.dashboardName
                                                        };

                                            return reqParams;
                                        },
                                        processResults: function (data) {
                                        return {
                                            results: data
                                        };
                                        }
                                        
                                    };
            }

            this.$textarea.empty();
            // this.$textarea.append("<option value=''></option>");
            var el = null;

            var originalValue = ( this.options.hasOwnProperty('loadDataDynamically') ) ? (this.originalValue + "").split(", ") : (this.originalValue + "").split(",");

            if (options.data && options.data.length) {
                for (var i = 0; i < options.data.length; i++) {
                    el = $("<option />");
                    el.attr("value", options.data[i].id);
                    el.html(options.data[i].text);

                    if (originalValue.indexOf(options.data[i].id + "") > -1 || originalValue.indexOf(options.data[i].text + "") > -1) {
                        el.attr("selected", true);
                    }

                    this.$textarea.append(el);
                }
            }

            var self = this;

            if( (this.$textarea).hasClass('select2-hidden-accessible') ) {
                this.$textarea.select2('destroy');
            }
            
            this.$textarea.select2(this.options)
                .on('change', onSelect2Changed.bind(this))
                .on('select2-close', onSelect2Closed.bind(this));

            self.$textarea.select2('open');
            
            // Pushes initial character entered into the search field, if available
            if (keyboardEvent && keyboardEvent.keyCode) {
                var key = keyboardEvent.keyCode;
                var keyText = (String.fromCharCode((96 <= key && key <= 105) ? key-48 : key)).toLowerCase();
                self.$textarea.select2('search', keyText);
            }
        };

        Select2Editor.prototype.init = function () {
            Handsontable.editors.TextEditor.prototype.init.apply(this, arguments);
        };

        Select2Editor.prototype.close = function () {
            this.instance.listen();
            this.instance.removeHook('beforeKeyDown', onBeforeKeyDown);
            this.$textarea.off();
            this.$textarea.hide();
            Handsontable.editors.TextEditor.prototype.close.apply(this, arguments);
        };

        Select2Editor.prototype.getValue = function (value) {
            if(!this.$textarea.val()) {
                return "";
            }

            if(typeof this.$textarea.val() === "object") {
                return this.$textarea.val().join(",");
            }
            return this.$textarea.val();
        };

        Select2Editor.prototype.setValue = function(value) {
            this.$textarea.value = value;
        };

        Select2Editor.prototype.focus = function () {

            this.instance.listen();

            // DO NOT CALL THE BASE TEXTEDITOR FOCUS METHOD HERE, IT CAN MAKE THIS EDITOR BEHAVE POORLY AND HAS NO PURPOSE WITHIN THE CONTEXT OF THIS EDITOR
            //Handsontable.editors.TextEditor.prototype.focus.apply(this, arguments);
        };

        Select2Editor.prototype.beginEditing = function (initialValue) {
            var onBeginEditing = this.instance.getSettings().onBeginEditing;

            if (onBeginEditing && onBeginEditing() === false) {
                return;
            }

            Handsontable.editors.TextEditor.prototype.beginEditing.apply(this, arguments);

        };

        Select2Editor.prototype.finishEditing = function (isCancelled, ctrlDown) {
            this.instance.listen();
            return Handsontable.editors.TextEditor.prototype.finishEditing.apply(this, arguments);
        };

        Handsontable.editors.Select2Editor = Select2Editor;
        Handsontable.editors.registerEditor('select2', Select2Editor);

    })(Handsontable);
});
