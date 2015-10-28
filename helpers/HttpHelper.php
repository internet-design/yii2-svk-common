<?php
namespace svk\helpers;

/**
 * Вспомогательный класс для работы с HTTP-запросами
 */
class HttpHelper
{
    /**
     * Парсит HTTP-заголовки, указанные в строке $str и возвращает ассоциативный массив, в котором ключ - название заголовка.
     *
     * @param string $str заголовки
     * @return array ассоциативный массив, ключ 0 - строка статуса заголовка, далее ключ - название заголовка.
     */
    public static function parseHeader($str)
    {
        $headers = [];

        $rows = explode("\n", $str);

        foreach ($rows as $k => $row) {
            if (!trim($row)) {
                continue;
            }
            if ($k == 0 || !isset($headers[0])) {
                // первая строка описывает статус ответа
                $headers[0] = trim($row);
                continue;
            }
            $pieces = explode(':', $row, 2);
            if (isset($pieces[0], $pieces[1])) {
                $headers[trim($pieces[0])] = trim($pieces[1]);
            }
        }

        return $headers;
    }
}