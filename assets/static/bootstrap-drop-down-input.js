/**
 * Скрипты для виджета bootstrap dropdown input
 *
 * @param {String} disabledClass класс для недоступного инпута
 * @param {String} caretHtml шаблон каретки
 * @param {String} wrapperSelector селектор враппера
 * @param {String} itemsWrapperTag тег, в котором хранятся элементы (по умолчанию ul)
 * @param {String} itemTag тег для описания самого элемента (по умолчани li)
 *
 * Для подписки на изменения поля можно использовать два типа событий:
 * - change;
 * - dropdownInput.change.
 *
 * Пример:
 * ```javascript
 * $(document).on('input#myId', 'dropdownInput.change', function(e, value) {
 *     console.log(value);
 * });
 * ```
 *
 * Для изменения набора полей можно использовать триггер dropdownInput.renderList, внутрь триггера необходимо передать массив,
 * каждый элемент которого - объект, у которого есть:
 * - label - html подписи;
 * - value - значение элемента.
 *
 * Пример:
 * ```javascript
 * $('input#myId').trigger('dropdownInput.renderList', [
 *     [
 *         {
 *             'value': 'value1',
 *             'label': 'label1'
 *         },
 *         {
 *             'value': 'value2',
 *             'label': 'label2,
 *             'disabled' : true
 *         }
 *         // etc
 *     ]
 * ]);
 * ```
 *
 * Для переключения значения можно использовать триггер dropdownInput.changeValue, внутрь триггера необходимо передать значение, например:
 *
 * ```javascript
 * $('input#myId').trigger('dropdownInput.changeValue', ['value1']);
 * ```
 */
(function($) {
    $.fn.dropdownInput = function(options) {
        var disabledClass = options.disabledClass,
            caretHtml = options.caretHtml,
            wrapperSelector = options.wrapperSelector,
            itemsTag = options.itemsWrapperTag || 'ul',
            itemTag = options.itemTag || 'li',

            $self = this;

        $(function() {
            var $wrapper = $(wrapperSelector),
                $button = $wrapper.find('.js-dropdown-button'),
                $list = $wrapper.find(itemsTag);

            /**
             * Выбор элемента
             *
             * @param {String} значение
             * @param {String} content
             * @returns {undefined}
             */
            var selectItem = function(value, content) {
                $self.val(value);
                $button.empty().html(content + ' ' + caretHtml);
                $self.trigger('change').trigger('dropdownInput.change', [value]);
            };

            /**
             * Выбор элемента
             */
            $wrapper.on('click', 'a', function(e) {
                if ($(this).hasClass(disabledClass) || $(this).parent(itemTag).hasClass(disabledClass)) {
                    return false;
                }
                var value = $(this).data('value');
                selectItem(value, $(this).html());
                e.preventDefault();
            });

            /**
             * Перерисовка элементов
             */
            $self.on('dropdownInput.renderList', function(e, items) {
                // очистить все элементы
                $list.empty();
                var selectedItem = null;
                for (var i in items) {
                    var newItem = items[i];
                    var $newItem = $('<' + itemTag + ' />');
                    var $link = $('<a/>')
                        .attr('href', '#')
                        .attr('class', 'js-dropdown-input-item ' + (newItem.disabled ? disabledClass : ''))
                        .attr('data-value', newItem.value);
                    $link.html(newItem.label)
                        .appendTo($newItem);

                    $newItem.appendTo($list);

                    if (selectedItem === null || newItem.value == $self.val()) {
                        selectedItem = newItem;
                    }
                }

                if (selectedItem) {
                    selectItem(selectedItem.value || '', selectedItem.label || '');
                }
                else {
                    selectItem(null, null);
                }
            });

            $self.on('dropdownInput.changeValue', function(e, value) {
                $list.find('a').each(function() {
                    if ($(this).data('value') == value) {
                        selectItem($(this).data('value'), $(this).html());
                    }
                });
            });
        });
    };
})(jQuery);
