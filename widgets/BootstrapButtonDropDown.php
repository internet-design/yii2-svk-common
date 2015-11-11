<?php
namespace svk\widgets;

use yii\bootstrap\Button;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;

/**
 * Расширение класса ButtonDropdown для возможности использования своего класса Dropdown
 */
class BootstrapButtonDropDown extends ButtonDropdown
{
    /**
     * @var string html для разделителя
     */
    public $caretHtml = '';

    /**
     * Generates the button dropdown.
     * @return string the rendering result.
     */
    protected function renderButton()
    {
        Html::addCssClass($this->options, 'btn');
        $label = $this->label;
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }
        $options = $this->options;
        $splitButton = '';
        $caretHtml = $this->caretHtml ? $this->caretHtml : '<span class="caret"></span>';
        if ($this->split) {
            $options = $this->options;
            $this->options['data-toggle'] = 'dropdown';
            Html::addCssClass($this->options, 'dropdown-toggle');
            $splitButton = Button::widget([
                'label' => $this->caretHtml ? $this->caretHtml : '<span class="caret"></span>',
                'encodeLabel' => false,
                'options' => $this->options,
                'view' => $this->getView(),
            ]);
        } else {
            $label .= ' ' . $this->caretHtml ? $this->caretHtml : '<span class="caret"></span>';
            $options = $this->options;
            if (!isset($options['href'])) {
                $options['href'] = '#';
            }
            Html::addCssClass($options, 'dropdown-toggle');
            $options['data-toggle'] = 'dropdown';
        }

        return Button::widget([
            'tagName' => $this->tagName,
            'label' => $label,
            'options' => $options,
            'encodeLabel' => false,
            'view' => $this->getView(),
        ]) . "\n" . $splitButton;
    }

    /**
     * @inheritdoc
     */
    protected function renderDropdown()
    {
        $config = $this->dropdown;
        $config['clientOptions'] = false;
        $config['view'] = $this->getView();

        return BootstrapDropDown::widget($config);
    }
}