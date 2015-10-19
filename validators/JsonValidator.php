<?php
namespace svk\validators;

use Yii;
use yii\helpers\Json;
use yii\validators\Validator;
use yii\base\InvalidParamException;

/**
 * Валидатор JSON
 *
 * На вход передается параметр, который уже сформатирован как JSON.
 * Если указан атрибут $encode = true, то можно передавать массивы и объекты,
 * тогда в модели они будут сконверитрованы в json-строку.
 */
class JsonValidator extends Validator
{
    /**
     * @var boolean если на входе получен массив или объект - кодировать его в JSON
     */
    public $encode = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->message) {
            $this->message = Yii::t('main', 'Wrong JSON format');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($this->encode && !is_string($model->{$attribute})) {
            // кодировать массив или объект в JSON
            try {
                $model->{$attribute} = Json::encode($model->{$attribute});
            }
            catch (InvalidParamException $ex) { }
        }
        return parent::validateAttribute($model, $attribute);
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $valid = is_string($value);

        if ($valid) {
            try {
                $result = Json::decode($value);
                // проверить, что получили действительно массив
                $valid = is_array($result);
            }
            catch (InvalidParamException $ex) {
                $valid = false;
            }
        }

        return $valid ? null : [$this->message, []];
    }
}