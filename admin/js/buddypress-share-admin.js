(function ($) {

    /**
     * Render a save result as a standard WordPress admin notice.
     *
     * The Settings API tabs (Icon Colors) get their banner from
     * settings_errors(), which core places as a direct child of .wrap right
     * after the .wp-header-end marker, dismissible, with the message in a
     * <strong>. An AJAX save has to land in the same place with the same
     * chrome, or the two save paths look like two different products.
     *
     * Triggering 'wp-notice-added' hands the element to core's
     * makeNoticesDismissible(), so the close button and its behaviour come
     * from WordPress rather than a hand-rolled copy.
     *
     * @param {boolean} ok      Whether the operation succeeded.
     * @param {string}  message Text to display.
     */
    function bpasNotice(ok, message) {
        var $wrap = $('.wrap.bpas-admin');
        var $anchor = $wrap.find('.wp-header-end');

        // Only ever one AJAX notice on screen, so a success never sits
        // underneath a stale error.
        $wrap.find('.bpas-ajax-notice').remove();

        var $notice = $('<div/>')
            .addClass('notice bpas-ajax-notice is-dismissible ' + (ok ? 'notice-success' : 'notice-error'))
            .append($('<p/>').append($('<strong/>').text(message)));

        if ($anchor.length) {
            $anchor.after($notice);
        } else {
            $wrap.prepend($notice);
        }

        $(document).trigger('wp-notice-added');

        // The banner sits at the top of the page while the Save button is at
        // the bottom, so without this the confirmation is off-screen — which
        // is indistinguishable from no confirmation at all.
        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    /**
     * Save Changes — persists the Extra Options checkboxes.
     *
     * Enabling/disabling a network is saved immediately by the drag-and-drop
     * handlers below, so this button only carries the Extra Options state.
     *
     * The checkbox lookup is anchored on the Extra Options card rather than on
     * the section-callback div: do_settings_sections() renders the fields table
     * as a SIBLING of that callback div, so the card is the element that
     * actually contains the checkboxes.
     */
    $(document).on('click', '.button-primary.bp_share_option_save', function () {
        var $button = $(this);
        var selected_extras = [];

        $('.bp-share-services-extra input[type="checkbox"]:checked').each(function () {
            selected_extras.push($(this).attr('name'));
        });

        $button.prop('disabled', true);

        $.post(my_ajax_object.ajax_url, {
            action: 'bp_share_chb_services_ajax',
            active_chb_extras: selected_extras,
            nonce: my_ajax_object.nonce
        })
            .done(function (response) {
                if (response && response.success) {
                    bpasNotice(true, (response.data && response.data.message) || my_ajax_object.saved_text);
                } else {
                    bpasNotice(false, (response && response.data && response.data.message) || my_ajax_object.error_text);
                }
            })
            .fail(function () {
                bpasNotice(false, my_ajax_object.error_text);
            })
            .always(function () {
                $button.prop('disabled', false);
            });

        return false;
    });

    $(function () {

        function bpas_drag_drop_social_icon() {
            var $lists = $('.social_icon_section > ul');
            $('li', $lists).draggable({
                revert: 'invalid',
                helper: 'clone',
                start: function () {
                    $(this).css('opacity', '0.5');
                },
                stop: function () {
                    $(this).css('opacity', '1');
                }
            });
        }

        /**
         * Move a network between the enable and disable columns and persist it.
         *
         * The DOM is updated optimistically so the drag feels immediate, but a
         * failed request puts the icon back where it came from rather than
         * leaving the UI claiming a change that was never saved.
         *
         * @param {jQuery} $dragged Item that was dropped.
         * @param {string} toId     Selector for the destination list.
         * @param {string} fromId   Selector for the originating list.
         * @param {string} action   AJAX action name.
         * @param {string} dataKey  POST key carrying the network name.
         */
        function bpasMoveIcon($dragged, toId, fromId, action, dataKey) {
            var name = $dragged.text();
            var $moved = $('<li/>')
                .addClass('socialicon ui-draggable icon_' + name)
                .text(name)
                .hide();

            $(toId).append($moved);
            $moved.fadeIn();
            $dragged.fadeOut('normal', function () {
                $(this).remove();
            });

            var payload = { action: action, nonce: my_ajax_object.nonce };
            payload[dataKey] = name;

            $.ajax({ url: my_ajax_object.ajax_url, type: 'POST', data: payload })
                .done(function (res) {
                    if (!res || !res.success) {
                        revert();
                        return;
                    }
                    bpasNotice(true, my_ajax_object.saved_text);
                })
                .fail(revert)
                .always(function () {
                    bpas_drag_drop_social_icon();
                });

            function revert() {
                $moved.remove();
                $(fromId).append(
                    $('<li/>').addClass('socialicon ui-draggable icon_' + name).text(name)
                );
                bpasNotice(false, my_ajax_object.error_text);
                bpas_drag_drop_social_icon();
            }
        }

        $('#drag_icon_ul').droppable({
            accept: '.social_icon_section > #drag_social_icon > li',
            drop: function (event, ui) {
                bpasMoveIcon($(ui.draggable), '#drag_icon_ul', '#drag_social_icon', 'wss_social_icons', 'term_name');
            }
        });

        $('#drag_social_icon').droppable({
            accept: '.social_icon_section > #drag_icon_ul > li',
            drop: function (event, ui) {
                bpasMoveIcon($(ui.draggable), '#drag_social_icon', '#drag_icon_ul', 'wss_social_remove_icons', 'icon_name');
            }
        });

        // Initial call to set up draggable and droppable elements.
        bpas_drag_drop_social_icon();
    });

})(jQuery);
