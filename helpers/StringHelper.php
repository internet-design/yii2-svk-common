<?php
namespace svk\helpers;

/**
 * Расширение базового хелпера для строк
 */
class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * Обрезать текст по предложениям. Возвращает обрезанный текст по предложениям, общее количество символов которых не превышает $count.
     * Если предложения не найдены - пытается обрезать текст по словам и подставляет $suffix в конце.
     * Если предложение состоит из одного слова - обрезает слово и подставляет $suffix в конце.
     *
     * @param string $input входная строка
     * @param integer $count количество символов для обрезания текста
     * @param string $suffix суффикс
     * @param boolean $asHtml вставка html
     * @return string
     */
    public static function truncateSentence($input, $count, $suffix = '...', $asHtml = false)
    {
        if ($count < 1) {
            return $input;
        }

        // символы завершения предложений
        $sentenceDelimieters = [
            '.', '!', '?',
        ];

        // обрезать до $count символов
        $output = self::truncate($input, $count, '', null, $asHtml);

        if ($output <= $count) {
            return $output;
        }

        // обрезать текст до первого символа завершения предложения с конца
        for ($x = mb_strlen($output); $x > 0; $x--) {
            $str = mb_substr($output, $x - 1, 1);
            if (in_array($str, $sentenceDelimieters)) {
                // символ завершения предложения
                return mb_substr($output, 0, $x);
            }
        }

        // обрезать текст до первого пробела с конца
        for ($x = mb_strlen($output); $x > 0; $x--) {
            $str = substr($output, $x - 1, 1);
            if ($str == ' ') {
                // пробельный символ
                return mb_substr($output, 0, $x) . $suffix;
            }
        }

        // обрезать текст как есть
        return $output . $suffix;
    }
}