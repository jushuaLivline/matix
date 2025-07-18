/*!
 * pickadate.js v3.6.4, 2019/05/25
 * By Amsul, http://amsul.ca
 * Hosted on http://amsul.github.io/pickadate.js
 * Licensed under MIT
 */

(function (factory) {

    // AMD.
    if (typeof define == 'function' && define.amd)
        define('picker', ['jquery'], factory)

    // Node.js/browserify.
    else if (typeof exports == 'object')
        module.exports = factory(require('jquery'))

    // Browser globals.
    else if (typeof window == 'object')
        window.Picker = factory(jQuery)

    else this.Picker = factory(jQuery)

}(function ($) {

    var $window = $(window)
    var $document = $(document)
    var $html = $(document.documentElement)
    var supportsTransitions = document.documentElement.style.transition != null


    /**
     * The picker constructor that creates a blank picker.
     */
    function PickerConstructor(ELEMENT, NAME, COMPONENT, OPTIONS) {

        // If thereâ€™s no element, return the picker constructor.
        if (!ELEMENT) return PickerConstructor


        var
            IS_DEFAULT_THEME = false,


            // The state of the picker.
            STATE = {
                id: ELEMENT.id || 'P' + Math.abs(~~(Math.random() * new Date())),
                handlingOpen: false,
            },


            // Merge the defaults and options passed.
            SETTINGS = COMPONENT ? $.extend(true, {}, COMPONENT.defaults, OPTIONS) : OPTIONS || {},


            // Merge the default classes with the settings classes.
            CLASSES = $.extend({}, PickerConstructor.klasses(), SETTINGS.klass),


            // The element node wrapper into a jQuery object.
            $ELEMENT = $(ELEMENT),


            // Pseudo picker constructor.
            PickerInstance = function () {
                return this.start()
            },


            // The picker prototype.
            P = PickerInstance.prototype = {

                constructor: PickerInstance,

                $node: $ELEMENT,


                /**
                 * Initialize everything
                 */
                start: function () {

                    // If itâ€™s already started, do nothing.
                    if (STATE && STATE.start) return P


                    // Update the picker states.
                    STATE.methods = {}
                    STATE.start = true
                    STATE.open = false
                    STATE.type = ELEMENT.type


                    // Confirm focus state, convert into text input to remove UA stylings,
                    // and set as readonly to prevent keyboard popup.
                    ELEMENT.autofocus = ELEMENT == getActiveElement()
                    ELEMENT.readOnly = !SETTINGS.editable
                    SETTINGS.id = ELEMENT.id = ELEMENT.id || STATE.id
                    if (ELEMENT.type != 'text') {
                        ELEMENT.type = 'text'
                    }

                    // Create a new picker component with the settings.
                    P.component = new COMPONENT(P, SETTINGS)


                    // Create the picker root and then prepare it.
                    P.$root = $('<div class="' + CLASSES.picker + '" id="' + ELEMENT.id + '_root" />')
                    prepareElementRoot()


                    // Create the picker holder and then prepare it.
                    P.$holder = $(createWrappedComponent()).appendTo(P.$root)
                    prepareElementHolder()


                    // If thereâ€™s a format for the hidden input element, create the element.
                    if (SETTINGS.formatSubmit) {
                        prepareElementHidden()
                    }


                    // Prepare the input element.
                    prepareElement()


                    // Insert the hidden input as specified in the settings.
                    if (SETTINGS.containerHidden) $(SETTINGS.containerHidden).append(P._hidden)
                    else $ELEMENT.after(P._hidden)


                    // Insert the root as specified in the settings.
                    if (SETTINGS.container) $(SETTINGS.container).append(P.$root)
                    else $ELEMENT.after(P.$root)


                    // Bind the default component and settings events.
                    P.on({
                        start: P.component.onStart,
                        render: P.component.onRender,
                        stop: P.component.onStop,
                        open: P.component.onOpen,
                        close: P.component.onClose,
                        set: P.component.onSet
                    }).on({
                        start: SETTINGS.onStart,
                        render: SETTINGS.onRender,
                        stop: SETTINGS.onStop,
                        open: SETTINGS.onOpen,
                        close: SETTINGS.onClose,
                        set: SETTINGS.onSet
                    })


                    // Once weâ€™re all set, check the theme in use.
                    IS_DEFAULT_THEME = isUsingDefaultTheme(P.$holder[0])


                    // If the element has autofocus, open the picker.
                    if (ELEMENT.autofocus) {
                        P.open()
                    }


                    // Trigger queued the â€œstartâ€ and â€œrenderâ€ events.
                    return P.trigger('start').trigger('render')
                }, //start


                /**
                 * Render a new picker
                 */
                render: function (entireComponent) {

                    // Insert a new component holder in the root or box.
                    if (entireComponent) {
                        P.$holder = $(createWrappedComponent())
                        prepareElementHolder()
                        P.$root.html(P.$holder)
                    } else P.$root.find('.' + CLASSES.box).html(P.component.nodes(STATE.open))

                    // Trigger the queued â€œrenderâ€ events.
                    return P.trigger('render')
                }, //render


                /**
                 * Destroy everything
                 */
                stop: function () {

                    // If itâ€™s already stopped, do nothing.
                    if (!STATE.start) return P

                    // Then close the picker.
                    P.close()

                    // Remove the hidden field.
                    if (P._hidden) {
                        P._hidden.parentNode.removeChild(P._hidden)
                    }

                    // Remove the root.
                    P.$root.remove()

                    // Remove the input class, remove the stored data, and unbind
                    // the events (after a tick for IE - see `P.close`).
                    $ELEMENT.removeClass(CLASSES.input).removeData(NAME)
                    setTimeout(function () {
                        $ELEMENT.off('.' + STATE.id)
                    }, 0)

                    // Restore the element state
                    ELEMENT.type = STATE.type
                    ELEMENT.readOnly = false

                    // Trigger the queued â€œstopâ€ events.
                    P.trigger('stop')

                    // Reset the picker states.
                    STATE.methods = {}
                    STATE.start = false

                    return P
                }, //stop


                /**
                 * Open up the picker
                 */
                open: function (dontGiveFocus) {

                    // If itâ€™s already open, do nothing.
                    if (STATE.open) return P

                    // Add the â€œactiveâ€ class.
                    $ELEMENT.addClass(CLASSES.active)

                    // * A Firefox bug, when `html` has `overflow:hidden`, results in
                    //   killing transitions :(. So add the â€œopenedâ€ state on the next tick.
                    //   Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=625289
                    setTimeout(function () {

                        // Add the â€œopenedâ€ class to the picker root.
                        P.$root.addClass(CLASSES.opened)
                        aria(P.$root[0], 'hidden', false)

                    }, 0)

                    // If we have to give focus, bind the element and doc events.
                    if (dontGiveFocus !== false) {

                        // Set it as open.
                        STATE.open = true

                        // Prevent the page from scrolling.
                        if (IS_DEFAULT_THEME) {
                            $('body').css('overflow', 'hidden').css('padding-right', '+=' + getScrollbarWidth())
                        }

                        // Pass focus to the root elementâ€™s jQuery object.
                        focusPickerOnceOpened()

                        // Bind the document events.
                        $document.on('click.' + STATE.id + ' focusin.' + STATE.id, function (event) {
                            // If the picker is currently midway through processing
                            // the opening sequence of events then don't handle clicks
                            // on any part of the DOM. This is caused by a bug in Chrome 73
                            // where a click event is being generated with the incorrect
                            // path in it.
                            // In short, if someone does a click that finishes after the
                            // new element is created then the path contains only the
                            // parent element and not the input element itself.
                            if (STATE.handlingOpen) {
                                return;
                            }

                            var target = getRealEventTarget(event, ELEMENT)

                            // If the target of the event is not the element, close the picker picker.
                            // * Donâ€™t worry about clicks or focusins on the root because those donâ€™t bubble up.
                            //   Also, for Firefox, a click on an `option` element bubbles up directly
                            //   to the doc. So make sure the target wasn't the doc.
                            // * In Firefox stopPropagation() doesnâ€™t prevent right-click events from bubbling,
                            //   which causes the picker to unexpectedly close when right-clicking it. So make
                            //   sure the event wasnâ€™t a right-click.
                            // * In Chrome 62 and up, password autofill causes a simulated focusin event which
                            //   closes the picker.
                            if (!event.isSimulated && target != ELEMENT && target != document && event.which != 3) {

                                // If the target was the holder that covers the screen,
                                // keep the element focused to maintain tabindex.
                                P.close(target === P.$holder[0])
                            }

                        }).on('keydown.' + STATE.id, function (event) {

                            var
                                // Get the keycode.
                                keycode = event.keyCode,

                                // Translate that to a selection change.
                                keycodeToMove = P.component.key[keycode],

                                // Grab the target.
                                target = getRealEventTarget(event, ELEMENT)


                            // On escape, close the picker and give focus.
                            if (keycode == 27) {
                                P.close(true)
                            }


                            // Check if there is a key movement or â€œenterâ€ keypress on the element.
                            else if (target == P.$holder[0] && (keycodeToMove || keycode == 13)) {

                                // Prevent the default action to stop page movement.
                                event.preventDefault()

                                // Trigger the key movement action.
                                if (keycodeToMove) {
                                    PickerConstructor._.trigger(P.component.key.go, P, [PickerConstructor._.trigger(keycodeToMove)])
                                }

                                // On â€œenterâ€, if the highlighted item isnâ€™t disabled, set the value and close.
                                else if (!P.$root.find('.' + CLASSES.highlighted).hasClass(CLASSES.disabled)) {
                                    P.set('select', P.component.item.highlight)
                                    if (SETTINGS.closeOnSelect) {
                                        P.close(true)
                                    }
                                }
                            }


                                // If the target is within the root and â€œenterâ€ is pressed,
                            // prevent the default action and trigger a click on the target instead.
                            else if ($.contains(P.$root[0], target) && keycode == 13) {
                                event.preventDefault()
                                target.click()
                            }
                        })
                    }

                    // Trigger the queued â€œopenâ€ events.
                    return P.trigger('open')
                }, //open


                /**
                 * Close the picker
                 */
                close: function (giveFocus) {

                    // If we need to give focus, do it before changing states.
                    if (giveFocus) {
                        if (SETTINGS.editable) {
                            ELEMENT.focus()
                        } else {
                            // ....ah yes! It wouldâ€™ve been incomplete without a crazy workaround for IE :|
                            // The focus is triggered *after* the close has completed - causing it
                            // to open again. So unbind and rebind the event at the next tick.
                            P.$holder.off('focus.toOpen').focus()
                            setTimeout(function () {
                                P.$holder.on('focus.toOpen', handleFocusToOpenEvent)
                            }, 0)
                        }
                    }

                    // Remove the â€œactiveâ€ class.
                    $ELEMENT.removeClass(CLASSES.active)

                    // * A Firefox bug, when `html` has `overflow:hidden`, results in
                    //   killing transitions :(. So remove the â€œopenedâ€ state on the next tick.
                    //   Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=625289
                    setTimeout(function () {

                        // Remove the â€œopenedâ€ and â€œfocusedâ€ class from the picker root.
                        P.$root.removeClass(CLASSES.opened + ' ' + CLASSES.focused)
                        aria(P.$root[0], 'hidden', true)

                    }, 0)

                    // If itâ€™s already closed, do nothing more.
                    if (!STATE.open) return P

                    // Set it as closed.
                    STATE.open = false

                    // Allow the page to scroll.
                    if (IS_DEFAULT_THEME) {
                        $('body').css('overflow', '').css('padding-right', '-=' + getScrollbarWidth())
                    }

                    // Unbind the document events.
                    $document.off('.' + STATE.id)

                    // Trigger the queued â€œcloseâ€ events.
                    return P.trigger('close')
                }, //close


                /**
                 * Clear the values
                 */
                clear: function (options) {
                    return P.set('clear', null, options)
                }, //clear


                /**
                 * Set something
                 */
                set: function (thing, value, options) {

                    var thingItem, thingValue,
                        thingIsObject = $.isPlainObject(thing),
                        thingObject = thingIsObject ? thing : {}

                    // Make sure we have usable options.
                    options = thingIsObject && $.isPlainObject(value) ? value : options || {}

                    if (thing) {

                        // If the thing isnâ€™t an object, make it one.
                        if (!thingIsObject) {
                            thingObject[thing] = value
                        }

                        // Go through the things of items to set.
                        for (thingItem in thingObject) {

                            // Grab the value of the thing.
                            thingValue = thingObject[thingItem]

                            // First, if the item exists and thereâ€™s a value, set it.
                            if (thingItem in P.component.item) {
                                if (thingValue === undefined) thingValue = null
                                P.component.set(thingItem, thingValue, options)
                            }

                            // Then, check to update the element value and broadcast a change.
                            if ((thingItem == 'select' || thingItem == 'clear') && SETTINGS.updateInput) {
                                $ELEMENT.val(thingItem == 'clear' ? '' : P.get(thingItem, SETTINGS.format)).trigger('change')
                            }
                        }

                        // Render a new picker.
                        P.render()
                    }

                    // When the method isnâ€™t muted, trigger queued â€œsetâ€ events and pass the `thingObject`.
                    return options.muted ? P : P.trigger('set', thingObject)
                }, //set


                /**
                 * Get something
                 */
                get: function (thing, format) {

                    // Make sure thereâ€™s something to get.
                    thing = thing || 'value'

                    // If a picker state exists, return that.
                    if (STATE[thing] != null) {
                        return STATE[thing]
                    }

                    // Return the submission value, if that.
                    if (thing == 'valueSubmit') {
                        if (P._hidden) {
                            return P._hidden.value
                        }
                        thing = 'value'
                    }

                    // Return the value, if that.
                    if (thing == 'value') {
                        return ELEMENT.value
                    }

                    // Check if a component item exists, return that.
                    if (thing in P.component.item) {
                        if (typeof format == 'string') {
                            var thingValue = P.component.get(thing)
                            return thingValue ?
                                PickerConstructor._.trigger(
                                    P.component.formats.toString,
                                    P.component,
                                    [format, thingValue]
                                ) : ''
                        }
                        return P.component.get(thing)
                    }
                }, //get


                /**
                 * Bind events on the things.
                 */
                on: function (thing, method, internal) {

                    var thingName, thingMethod,
                        thingIsObject = $.isPlainObject(thing),
                        thingObject = thingIsObject ? thing : {}

                    if (thing) {

                        // If the thing isnâ€™t an object, make it one.
                        if (!thingIsObject) {
                            thingObject[thing] = method
                        }

                        // Go through the things to bind to.
                        for (thingName in thingObject) {

                            // Grab the method of the thing.
                            thingMethod = thingObject[thingName]

                            // If it was an internal binding, prefix it.
                            if (internal) {
                                thingName = '_' + thingName
                            }

                            // Make sure the thing methods collection exists.
                            STATE.methods[thingName] = STATE.methods[thingName] || []

                            // Add the method to the relative method collection.
                            STATE.methods[thingName].push(thingMethod)
                        }
                    }

                    return P
                }, //on


                /**
                 * Unbind events on the things.
                 */
                off: function () {
                    var i, thingName,
                        names = arguments;
                    for (i = 0, namesCount = names.length; i < namesCount; i += 1) {
                        thingName = names[i]
                        if (thingName in STATE.methods) {
                            delete STATE.methods[thingName]
                        }
                    }
                    return P
                },


                /**
                 * Fire off method events.
                 */
                trigger: function (name, data) {
                    var _trigger = function (name) {
                        var methodList = STATE.methods[name]
                        if (methodList) {
                            methodList.map(function (method) {
                                PickerConstructor._.trigger(method, P, [data])
                            })
                        }
                    }
                    _trigger('_' + name)
                    _trigger(name)
                    return P
                } //trigger
            } //PickerInstance.prototype


        /**
         * Wrap the picker holder components together.
         */
        function createWrappedComponent() {

            // Create a picker wrapper holder
            return PickerConstructor._.node('div',

                // Create a picker wrapper node
                PickerConstructor._.node('div',

                    // Create a picker frame
                    PickerConstructor._.node('div',

                        // Create a picker box node
                        PickerConstructor._.node('div',

                            // Create the components nodes.
                            P.component.nodes(STATE.open),

                            // The picker box class
                            CLASSES.box
                        ),

                        // Picker wrap class
                        CLASSES.wrap
                    ),

                    // Picker frame class
                    CLASSES.frame
                ),

                // Picker holder class
                CLASSES.holder,

                'tabindex="-1"'
            ) //endreturn
        } //createWrappedComponent

        /**
         * Prepare the input element with all bindings.
         */
        function prepareElement() {

            $ELEMENT.

                // Store the picker data by component name.
                data(NAME, P).

                // Add the â€œinputâ€ class name.
                addClass(CLASSES.input).

                // If thereâ€™s a `data-value`, update the value of the element.
                val($ELEMENT.data('value') ?
                    P.get('select', SETTINGS.format) :
                    ELEMENT.value
                ).

                // On focus/click, open the picker.
                on('focus.' + STATE.id + ' click.' + STATE.id,
                    function (event) {
                        event.preventDefault()
                        P.open()
                    }
                )

                // Mousedown handler to capture when the user starts interacting
                // with the picker. This is used in working around a bug in Chrome 73.
                .on('mousedown', function () {
                    STATE.handlingOpen = true;
                    var handler = function () {
                        // By default mouseup events are fired before a click event.
                        // By using a timeout we can force the mouseup to be handled
                        // after the corresponding click event is handled.
                        setTimeout(function () {
                            $(document).off('mouseup', handler);
                            STATE.handlingOpen = false;
                        }, 0);
                    };
                    $(document).on('mouseup', handler);
                });


            // Only bind keydown events if the element isnâ€™t editable.
            if (!SETTINGS.editable) {

                $ELEMENT.

                    // Handle keyboard event based on the picker being opened or not.
                    on('keydown.' + STATE.id, handleKeydownEvent)
            }


            // Update the aria attributes.
            aria(ELEMENT, {
                haspopup: true,
                readonly: false,
                owns: ELEMENT.id + '_root'
            })
        }


        /**
         * Prepare the root picker element with all bindings.
         */
        function prepareElementRoot() {
            aria(P.$root[0], 'hidden', true)
        }


        /**
         * Prepare the holder picker element with all bindings.
         */
        function prepareElementHolder() {

            P.$holder.on({

                // For iOS8.
                keydown: handleKeydownEvent,

                'focus.toOpen': handleFocusToOpenEvent,

                blur: function () {
                    // Remove the â€œtargetâ€ class.
                    $ELEMENT.removeClass(CLASSES.target)
                },

                // When something within the holder is focused, stop from bubbling
                // to the doc and remove the â€œfocusedâ€ state from the root.
                focusin: function (event) {
                    P.$root.removeClass(CLASSES.focused)
                    event.stopPropagation()
                },

                // When something within the holder is clicked, stop it
                // from bubbling to the doc.
                'mousedown click': function (event) {

                    var target = getRealEventTarget(event, ELEMENT)

                    // Make sure the target isnâ€™t the root holder so it can bubble up.
                    if (target != P.$holder[0]) {

                        event.stopPropagation()

                        // * For mousedown events, cancel the default action in order to
                        //   prevent cases where focus is shifted onto external elements
                        //   when using things like jQuery mobile or MagnificPopup (ref: #249 & #120).
                        //   Also, for Firefox, donâ€™t prevent action on the `option` element.
                        if (event.type == 'mousedown' && !$(target).is('input, select, textarea, button, option')) {

                            event.preventDefault()

                            // Re-focus onto the holder so that users can click away
                            // from elements focused within the picker.
                            P.$holder.eq(0).focus()
                        }
                    }
                }

            }).

                // If thereâ€™s a click on an actionable element, carry out the actions.
                on('click', '[data-pick], [data-nav], [data-clear], [data-close]', function () {

                    var $target = $(this),
                        targetData = $target.data(),
                        targetDisabled = $target.hasClass(CLASSES.navDisabled) || $target.hasClass(CLASSES.disabled),

                        // * For IE, non-focusable elements can be active elements as well
                        //   (http://stackoverflow.com/a/2684561).
                        activeElement = getActiveElement()
                    activeElement = activeElement && ((activeElement.type || activeElement.href) ? activeElement : null);

                    // If itâ€™s disabled or nothing inside is actively focused, re-focus the element.
                    if (targetDisabled || activeElement && !$.contains(P.$root[0], activeElement)) {
                        P.$holder.eq(0).focus()
                    }

                    // If something is superficially changed, update the `highlight` based on the `nav`.
                    if (!targetDisabled && targetData.nav) {
                        P.set('highlight', P.component.item.highlight, {nav: targetData.nav})
                    }

                    // If something is picked, set `select` then close with focus.
                    else if (!targetDisabled && 'pick' in targetData) {
                        P.set('select', targetData.pick)
                        if (SETTINGS.closeOnSelect) {
                            P.close(true)
                        }
                    }

                    // If a â€œclearâ€ button is pressed, empty the values and close with focus.
                    else if (targetData.clear) {
                        P.clear()
                        if (SETTINGS.closeOnClear) {
                            P.close(true)
                        }
                    } else if (targetData.close) {
                        P.close(true)
                    }

                }) //P.$holder

        }


        /**
         * Prepare the hidden input element along with all bindings.
         */
        function prepareElementHidden() {

            var name

            if (SETTINGS.hiddenName === true) {
                name = ELEMENT.name
                ELEMENT.name = ''
            } else {
                name = [
                    typeof SETTINGS.hiddenPrefix == 'string' ? SETTINGS.hiddenPrefix : '',
                    typeof SETTINGS.hiddenSuffix == 'string' ? SETTINGS.hiddenSuffix : '_submit'
                ]
                name = name[0] + ELEMENT.name + name[1]
            }

            P._hidden = $(
                '<input ' +
                'type=hidden ' +

                // Create the name using the original inputâ€™s with a prefix and suffix.
                'name="' + name + '"' +

                // If the element has a value, set the hidden value as well.
                (
                    $ELEMENT.data('value') || ELEMENT.value ?
                        ' value="' + P.get('select', SETTINGS.formatSubmit) + '"' :
                        ''
                ) +
                '>'
            )[0]

            $ELEMENT.

                // If the value changes, update the hidden input with the correct format.
                on('change.' + STATE.id, function () {
                    P._hidden.value = ELEMENT.value ?
                        P.get('select', SETTINGS.formatSubmit) :
                        ''
                })
        }


        // Wait for transitions to end before focusing the holder. Otherwise, while
        // using the `container` option, the view jumps to the container.
        function focusPickerOnceOpened() {

            if (IS_DEFAULT_THEME && supportsTransitions) {
                P.$holder.find('.' + CLASSES.frame).one('transitionend', function () {
                    P.$holder.eq(0).focus()
                })
            } else {
                setTimeout(function () {
                    P.$holder.eq(0).focus()
                }, 0)
            }
        }


        function handleFocusToOpenEvent(event) {

            // Stop the event from propagating to the doc.
            event.stopPropagation()

            // Add the â€œtargetâ€ class.
            $ELEMENT.addClass(CLASSES.target)

            // Add the â€œfocusedâ€ class to the root.
            P.$root.addClass(CLASSES.focused)

            // And then finally open the picker.
            P.open()
        }


        // For iOS8.
        function handleKeydownEvent(event) {

            var keycode = event.keyCode,

                // Check if one of the delete keys was pressed.
                isKeycodeDelete = /^(8|46)$/.test(keycode)

            // For some reason IE clears the input value on â€œescapeâ€.
            if (keycode == 27) {
                P.close(true)
                return false
            }

            // Check if `space` or `delete` was pressed or the picker is closed with a key movement.
            if (keycode == 32 || isKeycodeDelete || !STATE.open && P.component.key[keycode]) {

                // Prevent it from moving the page and bubbling to doc.
                event.preventDefault()
                event.stopPropagation()

                // If `delete` was pressed, clear the values and close the picker.
                // Otherwise open the picker.
                if (isKeycodeDelete) {
                    P.clear().close()
                } else {
                    P.open()
                }
            }
        }


        // Return a new picker instance.
        return new PickerInstance()
    } //PickerConstructor


    /**
     * The default classes and prefix to use for the HTML classes.
     */
    PickerConstructor.klasses = function (prefix) {
        prefix = prefix || 'picker'
        return {

            picker: prefix,
            opened: prefix + '--opened',
            focused: prefix + '--focused',

            input: prefix + '__input',
            active: prefix + '__input--active',
            target: prefix + '__input--target',

            holder: prefix + '__holder',

            frame: prefix + '__frame',
            wrap: prefix + '__wrap',

            box: prefix + '__box'
        }
    } //PickerConstructor.klasses


    /**
     * Check if the default theme is being used.
     */
    function isUsingDefaultTheme(element) {

        var theme,
            prop = 'position'

        // For IE.
        if (element.currentStyle) {
            theme = element.currentStyle[prop]
        }

        // For normal browsers.
        else if (window.getComputedStyle) {
            theme = getComputedStyle(element)[prop]
        }

        return theme == 'fixed'
    }


    /**
     * Get the width of the browserâ€™s scrollbar.
     * Taken from: https://github.com/VodkaBears/Remodal/blob/master/src/jquery.remodal.js
     */
    function getScrollbarWidth() {

        if ($html.height() <= $window.height()) {
            return 0
        }

        var $outer = $('<div style="visibility:hidden;width:100px" />').appendTo('body')

        // Get the width without scrollbars.
        var widthWithoutScroll = $outer[0].offsetWidth

        // Force adding scrollbars.
        $outer.css('overflow', 'scroll')

        // Add the inner div.
        var $inner = $('<div style="width:100%" />').appendTo($outer)

        // Get the width with scrollbars.
        var widthWithScroll = $inner[0].offsetWidth

        // Remove the divs.
        $outer.remove()

        // Return the difference between the widths.
        return widthWithoutScroll - widthWithScroll
    }


    /**
     * Get the target element from the event.
     * If ELEMENT is supplied and present in the event path (ELEMENT is ancestor of the target),
     * returns ELEMENT instead
     */
    function getRealEventTarget(event, ELEMENT) {

        var path = []

        if (event.path) {
            path = event.path
        }

        if (event.originalEvent && event.originalEvent.path) {
            path = event.originalEvent.path
        }

        if (path && path.length > 0) {
            if (ELEMENT && path.indexOf(ELEMENT) >= 0) {
                return ELEMENT
            } else {
                return path[0]
            }
        }

        return event.target
    }

    /**
     * PickerConstructor helper methods.
     */
    PickerConstructor._ = {

        /**
         * Create a group of nodes. Expects:
         * `
         {
            min:    {Integer},
            max:    {Integer},
            i:      {Integer},
            node:   {String},
            item:   {Function}
        }
         * `
         */
        group: function (groupObject) {

            var
                // Scope for the looped object
                loopObjectScope,

                // Create the nodes list
                nodesList = '',

                // The counter starts from the `min`
                counter = PickerConstructor._.trigger(groupObject.min, groupObject)


            // Loop from the `min` to `max`, incrementing by `i`
            for (; counter <= PickerConstructor._.trigger(groupObject.max, groupObject, [counter]); counter += groupObject.i) {

                // Trigger the `item` function within scope of the object
                loopObjectScope = PickerConstructor._.trigger(groupObject.item, groupObject, [counter])

                // Splice the subgroup and create nodes out of the sub nodes
                nodesList += PickerConstructor._.node(
                    groupObject.node,
                    loopObjectScope[0],   // the node
                    loopObjectScope[1],   // the classes
                    loopObjectScope[2]    // the attributes
                )
            }

            // Return the list of nodes
            return nodesList
        }, //group


        /**
         * Create a dom node string
         */
        node: function (wrapper, item, klass, attribute) {

            // If the item is false-y, just return an empty string
            if (!item) return ''

            // If the item is an array, do a join
            item = $.isArray(item) ? item.join('') : item

            // Check for the class
            klass = klass ? ' class="' + klass + '"' : ''

            // Check for any attributes
            attribute = attribute ? ' ' + attribute : ''

            // Return the wrapped item
            return '<' + wrapper + klass + attribute + '>' + item + '</' + wrapper + '>'
        }, //node


        /**
         * Lead numbers below 10 with a zero.
         */
        lead: function (number) {
            return (number < 10 ? '0' : '') + number
        },


        /**
         * Trigger a function otherwise return the value.
         */
        trigger: function (callback, scope, args) {
            return typeof callback == 'function' ? callback.apply(scope, args || []) : callback
        },


        /**
         * If the second character is a digit, length is 2 otherwise 1.
         */
        digits: function (string) {
            return (/\d/).test(string[1]) ? 2 : 1
        },


        /**
         * Tell if something is a date object.
         */
        isDate: function (value) {
            return {}.toString.call(value).indexOf('Date') > -1 && this.isInteger(value.getDate())
        },


        /**
         * Tell if something is an integer.
         */
        isInteger: function (value) {
            return {}.toString.call(value).indexOf('Number') > -1 && value % 1 === 0
        },


        /**
         * Create ARIA attribute strings.
         */
        ariaAttr: ariaAttr
    } //PickerConstructor._


    /**
     * Extend the picker with a component and defaults.
     */
    PickerConstructor.extend = function (name, Component) {

        // Extend jQuery.
        $.fn[name] = function (options, action) {

            // Grab the component data.
            var componentData = this.data(name)

            // If the picker is requested, return the data object.
            if (options == 'picker') {
                return componentData
            }

            // If the component data exists and `options` is a string, carry out the action.
            if (componentData && typeof options == 'string') {
                return PickerConstructor._.trigger(componentData[options], componentData, [action])
            }

            // Otherwise go through each matched element and if the component
            // doesnâ€™t exist, create a new picker using `this` element
            // and merging the defaults and options with a deep copy.
            return this.each(function () {
                var $this = $(this)
                if (!$this.data(name)) {
                    new PickerConstructor(this, name, Component, options)
                }
            })
        }

        // Set the defaults.
        $.fn[name].defaults = Component.defaults
    } //PickerConstructor.extend


    function aria(element, attribute, value) {
        if ($.isPlainObject(attribute)) {
            for (var key in attribute) {
                ariaSet(element, key, attribute[key])
            }
        } else {
            ariaSet(element, attribute, value)
        }
    }

    function ariaSet(element, attribute, value) {
        element.setAttribute(
            (attribute == 'role' ? '' : 'aria-') + attribute,
            value
        )
    }

    function ariaAttr(attribute, data) {
        if (!$.isPlainObject(attribute)) {
            attribute = {attribute: data}
        }
        data = ''
        for (var key in attribute) {
            var attr = (key == 'role' ? '' : 'aria-') + key,
                attrVal = attribute[key]
            data += attrVal == null ? '' : attr + '="' + attribute[key] + '"'
        }
        return data
    }

// IE8 bug throws an error for activeElements within iframes.
    function getActiveElement() {
        try {
            return document.activeElement
        } catch (err) {
        }
    }


// Expose the picker constructor.
    return PickerConstructor


}));
