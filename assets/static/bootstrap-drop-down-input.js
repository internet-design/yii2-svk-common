/**
 * Скрипты для виджета bootstrap dropdown input
 *
 * @param {String} disabledClass класс для недоступного инпута
 * @param {String} caretHtml шаблон каретки
 */
(function($) {
    $.fn.dropdownInput = function(options) {
        var disabledClass = options.disabledClass;
        var caretHtml = options.caretHtml;
        var $self = this;

        $(document).ready(function() {
            var $button = $self.find('.js-dropdown-button');
            var $input = $self.find('input:hidden');

            $self.on('click', 'a', function(e) {
                e.preventDefault();

                var value = $(this).data('value');
                if ($(this).hasClass(disabledClass)) {
                    value = '';
                }

                var content = $(this).html();
                $button.html(content + ' ' + caretHtml);

                $input.val(value);
                $input.trigger('change');
            });
        });
    };
})(jQuery);