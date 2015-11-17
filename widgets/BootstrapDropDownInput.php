<?php
namespace svk\widgets;

use svk\assets\BootstrapDropDownInputAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Возможность использования bootstrap dropdown как инпут в формах
 */
class BootstrapDropDownInput extends InputWidget
{
    /**
     * @var string тег для враппера
     */
    public $wrapperTag = 'div';

    /**
     * @var string атрибуты для враппера
     */
    public $wrapperOptions = [
        'class' => 'dropdown',
    ];

    /**
     * @var array опции для инпута
     */
    public $hiddenOptions = [];

    /**
     * @var string класс, определяющий, что выбранный элемент неактивен
     */
    public $disabledClass = 'disabled';

    /**
     * @var boolean экранировать лейблы
     */
    public $encodeLabels = false;

    /**
     * @var string каретка
     */
    public $caretHtml = '<span class="caret"></span>';

    /**
     * Элементы списка в виде:
     * ```php
     * [
     *  'value' => 'значение',
     *  'label' => 'подпись',
     *  'disabled' => false, // true, если выбор списка недоступен
     *  'options' => [], // опции для элемента списка
     *  'linkOptions' => [], // опции для ссылки
     *  'items' => [], // вложенные списки, если требуется
     *  // иные атрибуты, необходимые для Dropdown
     * ]
     * ```
     * @var array
     */
    public $items = [];

    /**
     * @var array настройки для dropdown
     * @see BootstrapDropDownButton
     */
    public $dropdown = [];

    /**
     * @var array настройки для кнопки
     * @see BootstrapDropDown
     */
    public $button = [];

    /**
     * Подготовка элементов
     *
     * @param array $items
     * @return array
     */
    protected function prepareItems(array $items)
    {
        foreach ($items as $k => $item) {
            if (array_key_exists('items', $item) && is_array($item['items'])) {
                $item['items'] = $this->prepareItems($item['items']);
            }
            $options = ArrayHelper::getValue($item, 'options', []);
            $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
            Html::addCssClass($linkOptions, 'js-dropdown-input-item');
            if (array_key_exists('disabled', $item) && $item['disabled'] === true) {
                Html::addCssClass($options, $this->disabledClass);
            }
            $linkOptions['data-value'] = ArrayHelper::getValue($item, 'value', null);

            if (isset($item['value'])) {
                unset ($item['value']);
            }
            if (isset($item['disabled'])) {
                unset ($item['disabled']);
            }

            $item['options'] = $options;
            $item['linkOptions'] = $linkOptions;

            $item['url'] = '#';

            $items[$k] = $item;
        }
        return $items;
    }

    /**
     * Получить выбранный элемент
     *
     * @param array $items
     * @return array
     */
    protected function getSelectedItem(array $items)
    {
        $selectedItem = null;

        $selectedValue = array_key_exists('value', $this->hiddenOptions) ? $this->hiddenOptions['value'] : Html::getAttributeValue($this->model, $this->attribute);

        foreach ($items as $item) {
            if (array_key_exists('value', $item) && $item['value'] == $selectedValue) {
                $selectedItem = $item;
            }
            if (array_key_exists('items', $item)) {
                // вложенный список
                $selectedItem = $this->getSelectedItem($item['items']);
            }
            if (!is_null($selectedItem)) {
                break;
            }
        }

        if (is_null($selectedItem)) {
            $selectedItem = reset($items);
        }

        if (is_array($selectedItem) && array_key_exists('items', $selectedItem)) {
            unset ($selectedItem['items']);
        }

        return $selectedItem;
    }

    public function init()
    {
        if (!array_key_exists('encodeLabel', $this->button)) {
            $this->button['encodeLabel'] = $this->encodeLabels;
        }
        if (!array_key_exists('encodeLabels', $this->dropdown)) {
            $this->dropdown['encodeLabels'] = $this->encodeLabels;
        }
        return parent::init();
    }

    /**
     * Подготовка конфигов для виджета BootstrapButtonDropDown.
     *
     * @param array $selectedItem выбранный по умолчанию элемент
     * @return array
     */
    protected function prepareButtonConfig($selectedItem)
    {
        $config = $this->button;
        if (!array_key_exists('options', $config)) {
            $config['options'] = [];
        }
        if (!array_key_exists('class', $config['options'])) {
            $config['options']['class'] = [];
        }
        if (!array_key_exists('caretHtml', $config)) {
            $config['caretHtml'] = $this->caretHtml;
        }
        Html::addCssClass($config['options'], 'js-dropdown-button');
        $config['label'] = isset($selectedItem['label']) ? $selectedItem['label'] : '';
        $config['dropdown'] = $this->dropdown;
        $config['dropdown']['items'] = $this->prepareItems($this->items);

        if (!array_key_exists('wrapperTag', $config['dropdown'])) {
            $config['dropdown']['wrapperTag'] = 'ul';
        }

        if (!array_key_exists('itemTag', $config['dropdown'])) {
            $config['dropdown']['itemTag'] = 'li';
        }

        return $config;
    }

    /**
     * Регистрация скриптов
     *
     * @param array $config массив конфигурации из метода prepareButtonConfig
     */
    protected function registerScripts($config)
    {
        $clientOptions = [
            'itemsWrapperTag' => $config['dropdown']['wrapperTag'],
            'itemTag' => $config['dropdown']['itemTag'],
            'wrapperSelector' => '#' . $this->wrapperOptions['id'],
            'disabledClass' => $this->disabledClass,
            'caretHtml' => $config['caretHtml'],
        ];
        BootstrapDropDownInputAsset::register($this->view, Html::getInputId($this->model, $this->attribute), $clientOptions);
    }

    /**
     * Рендер инпута
     *
     * @return string
     */
    public function run()
    {
        $content = '';

        $selectedItem = $this->getSelectedItem($this->items);

        if (!array_key_exists('id', $this->wrapperOptions)) {
            $this->wrapperOptions['id'] = $this->id;
        }

        $content .= Html::beginTag($this->wrapperTag, $this->wrapperOptions);
        $content .= Html::activeHiddenInput($this->model, $this->attribute);

        // конфиг для кнопки
        $config = $this->prepareButtonConfig($selectedItem);

        $content .= BootstrapButtonDropDown::widget($config);
        $content .= Html::endTag($this->wrapperTag);

        $this->registerScripts($config);

        return $content;
    }
}