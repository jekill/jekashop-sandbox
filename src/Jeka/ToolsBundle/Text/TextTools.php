<?php

namespace Jeka\ToolsBundle\Text;

class TextTools
{
    /**
     *
     * @param string $s
     * @param array $trl
     * @return string
     */
    function translit($s, $trl = array())
    {
        if (count($trl) > 0) {
            while (list($k, $v) = each($trl))
            {
                $s = str_replace($k, $v, $s);
            }
        }

        $s = str_replace("ий", "iy", $s);
        $s = str_replace("ж", "zh", $s);
        $s = str_replace("ч", "ch", $s);
        $s = str_replace("ш", "sh", $s);
        $s = str_replace("щ", "sh", $s);
        $s = str_replace("ю", "yu", $s);
        $s = str_replace("я", "ya", $s);
        $s = str_replace("ИЙ", "IY", $s);
        $s = str_replace("Ж", "Zh", $s);
        $s = str_replace("Ч", "Ch", $s);
        $s = str_replace("Ш", "Sh", $s);
        $s = str_replace("Щ", "Sch", $s);
        $s = str_replace("Ю", "Yu", $s);
        $s = str_replace("Я", "Ya", $s);

        $rus_alpha = array("а", "б", "в", "г", "д", "е", "ё", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ы", "ъ", "ь", "э");
        $lat_alpha = str_split("abvgdeeziyklmnoprstufhci''e");
        $s = str_replace($rus_alpha, $lat_alpha, $s);

        $rus_alpha = array("А", "Б", "В", "Г", "Д", "Е", "Ё", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ы", "Ъ", "Ь", "Э");
        $lat_alpha = str_split("ABVGDEEZIYKLMNOPRSTUFHCI''E");
        $s = str_replace($rus_alpha, $lat_alpha, $s);


        //$s = strtr($s, "АБВГДЕЁЗИЙКЛМНОПРСТУФХЦЫЪЬЭ", "ABVGDEEZIYKLMNOPRSTUFHCI''E");
        //                $s = str_replace("№", "#", $s);
        return $s;
    }

    function sms_translit($str)
    {
        $map = array(
            'ж' => 'zh',
            'ч' => 'ch',
            'ш' => 'sh',
            'ъ' => 'x',
            'э' => 'je',
            'ю' => 'ju',
            'я' => 'ja'
        );

        while (list($k, $v) = each($map))
        {
            $str = str_replace($v, $k, $str);
            $str = str_replace(strtoupper($v), strtoupper($k), $str);
        }
        $str = strtr($str, "abvgdezijklmnoprstufhcwyq'", "абвгдезийклмнопрстуфхцщыьь");
        $str = strtr($str, "ABVGDEZIJKLMNOPRSTUFHCWYQ'", "АБВГДЕЗИЙКЛМНОПРСТУФХЦЩЫЬЬ");

        return $str;
    }

    public function slugify($string)
    {
        $string = strtolower($this->translit($string));
        $repl = array(
            '/\'/' => '',
            '/[^a-z0-9\-_]+/' => '-'
        );
        $string = preg_replace(array_keys($repl), array_values($repl), $string);
        $string = trim($string, '-');

        return $string;
    }

    public static function closeTags($html)
    {
        $single_tags = array('meta', 'img', 'br', 'link', 'area', 'input', 'hr', 'col', 'param', 'base');
        preg_match_all('~<([a-z0-9]+)(?: .*)?(?<![/|/ ])>~iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('~</([a-z0-9]+)>~iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i = 0; $i < $len_opened; $i++)
        {
            if (!in_array($openedtags[$i], $single_tags)) {
                if (FALSE !== ($key = array_search($openedtags[$i], $closedtags))) {
                    unset($closedtags[$key]);
                }
                else
                {
                    $html .= '</' . $openedtags[$i] . '>';
                }
            }
        }
        return $html;
    }


    /**
     * return a equal part of strings
     * @param array $strings
     * @return string
     */
    public function getEqualsFromStrings(array $strings)
    {
        $i = 0;
        for (; $i < 1000; $i++)
        {
            if (!mb_substr($strings[0], $i, 1)) {
                break;
            }

            $char = mb_substr($strings[0], $i, 1);

            for ($j = 1; $j < count($strings); $j++)
            {
                if (!mb_substr($strings[$j], $i, 1)) {
                    break 2;
                }

                if (mb_substr($strings[$j], $i, 1) != $char) {
                    break 2;
                }
            }
        }
        return trim(mb_substr($strings[0], 0, $i));
    }


}