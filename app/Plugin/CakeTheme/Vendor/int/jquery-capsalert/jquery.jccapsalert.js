/**
 * This file use showing Caps Lock alert
 *
 * @file    Main file for WebNotifications
 * @version 0.1
 * @see     http://jaspreetchahal.org/jquery-caps-lock-detection-plugin/
 */

jQuery.fn.CapsLockAlert = function (settings) {
    document.msCapsLockWarningOff = true;
    settings = jQuery.extend(
        {
            animation: true,
            // ! Apply a CSS fade transition to the tooltip
            container: false,
            // ! Appends the tooltip to a specific element. Example: container: 'body'.
            content: 'WARNING: CAPS Lock is on',
            // ! Default content value if data-content attribute isn't present.
            delay: 0,
            // ! Delay showing and hiding the popover (ms) - does not apply to manual trigger type
            html: false,
            // ! Insert HTML into the tooltip. If false, jQuery's text method will be used to insert content into the DOM.
            placement: 'top',
            // ! How to position the tooltip - top | bottom | left | right | auto.
            selector: false,
            // ! If a selector is provided, popover objects will be delegated to the specified targets.
            // ! In practice, this is used to enable dynamic HTML content to have popovers added.
            template: '<div class="popover popover-caps" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
            // ! Base HTML to use when creating the popover.
            title: '',
            // ! Fallback text to use when no tooltip text
            trigger: 'manual'
            // ! How tooltip is triggered - click | hover | focus | manual.
        },
        settings
    );

        var styles = $("style[type='text/css']").text();
    if (!styles || (styles.indexOf(".popover-caps") === -1)) {
        $("<style type='text/css'> .popover-caps { background: tomato; color: white; }.popover-caps.top .arrow:after { border-top-color:tomato; } </style>").appendTo("head");
    }

    return this.each(
        function () {
            jQuery(this).keypress(
                function (e) {
                    jQuery(this).popover(settings);
                    var is_shift_pressed = false;
                    if (e.shiftKey) {
                        is_shift_pressed = e.shiftKey;
                    } else if (e.modifiers) {
                        is_shift_pressed = !!(e.modifiers & 4);
                    }

                    if (((e.which >= 65 && e.which <= 90) && !is_shift_pressed) || ((e.which >= 97 && e.which <= 122) && is_shift_pressed)) {
                        if (jQuery(this).next('div.popover-caps:visible').length === 0) {
                            jQuery(this).popover("show");
                        }
                    } else {
                        jQuery(this).popover("hide");
                    }
                }
            );
            jQuery(this).keyup(
                function (e) {
                    if (jQuery(this).val() === "") {
                        jQuery(this).popover("hide");
                    }
                }
            );
            jQuery(this).focusout(
                function () {
                    jQuery(this).popover("hide");
                }
            );
        }
    );

};
