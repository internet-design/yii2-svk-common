<?php
namespace svk\validators;

use Yii;
use yii\validators\Validator;

/**
 * Валидатор полей типа uuid.
 * Здесь же можно сгенерировать это поле, используя любую последовательность символов.
 */
class UuidValidator extends Validator
{
    /**
     * Инициализация
     */
    public function init()
    {
        parent::init();
        if (empty($this->message)) {
            $this->message = Yii::t('main', 'Wrong uuid value');
        }
    }

    /**
     * Генерирует uuid на основе последовательности $charid.
     * Если последовательность не задана - генерирует ее.
     *
     * @param string $charid
     * @return string
     */
    public static function generate($charid = null)
    {
        if (empty($charid)) {
            mt_srand((double) microtime() * 10000);
            $charid = uniqid(rand(), true);
        }

        $charid = strtoupper(md5($charid));

        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return strtoupper($uuid);
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $valid = false;

        if (is_string($value) && !empty($value)) {
            $chars = '[ABCDEF0123456789]';
            $reg = '#^' . implode(chr(45), [$chars . '{8}', $chars . '{4}', $chars . '{4}', $chars . '{4}', $chars . '{12}']) . '$#';
            $valid = preg_match($reg, $value);
        }

        return $valid ? null : [$this->message, []];
    }
}