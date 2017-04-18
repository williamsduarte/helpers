<?php

namespace Vialoja\Helpers;


class Tools
{

    public static function setLocalePortuguese()
    {
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    }

    /**
     * Data de Emissão em Português
     * @return string
     */
    public static function issuedDate()
    {
        self::setLocalePortuguese();
        return strftime('%d de %B de %Y às ', strtotime('today')) . date('H:i:s');
    }

    public static function strlen($str, $encoding = 'UTF-8')
    {
        if (is_array($str))
            return false;
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen'))
            return mb_strlen($str, $encoding);
        return strlen($str);
    }

    /**
     * Formata a datetime para Padrão BR
     * @return string
     */
    public static function formatDoDate($data)
    {
        return strftime('%d/%m/%Y', strtotime($data));
    }

    /**
     * Converte valores float, double e decimal para padrão brasileiro
     * @param $value
     * @param int $casa total de casas apos a virgula
     * @return string
     */
    public static function convertToDecimalBR($value, $float=2)
    {
        return number_format($value, $float, ',', '.');
    }

    /**
     * Deixa o primeiro carácter de cada palavra em letra maiúscula
     * @param $string
     * @return string
     */
    public static function specialUcwords($string)
    {
        $words    = explode(' ', strtolower(trim(preg_replace("/\s+/", ' ', $string))));
        $return[] = ucfirst($words[0]);

        unset($words[0]);

        foreach ($words as $word) {
            if (!preg_match("/^([dn]?[aeiou][s]?|em)$/i", $word)) {
                $word = ucfirst($word);
            }
            $return[] = $word;
        }

        return implode(' ', $return);
    }

    /**
     * Clean String Search
     * @param null $string
     * @return string
     */
    public static function sanitizeSearch($string = null)
    {
        $array = [
            "\n\r", "  ",
        ];

        $string = filter_var($string, FILTER_SANITIZE_STRING);
        $string = str_replace($array, '', $string);
        $string = preg_replace('/[^0-9a-z]/i', ' ', $string);
        return trim(html_entity_decode($string));

    }

}
