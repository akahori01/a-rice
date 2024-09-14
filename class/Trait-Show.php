<?php declare(strict_types=1);

trait Show
{

    // 入力された値に記号が含まれている際に無害に変換関数
    public function escape($value): string
    {
        return htmlspecialchars(strval($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function numFormat($integer)
    {
        return number_format(intval($integer));
    }

    public function separate_address(string $address)
    {
        if (preg_match('/\A(.{2,3}?[都道府県])(.+?郡.+?[町村]|.+?市.+?区|.+?[市区町村])(.+)\z/u', $address, $matches) !== 1) {
            return [
                'prefecture' => null,
                'city' => null,
                'addr' => null
            ];
        }
        return [
            'prefecture' => $matches[1],
            'city' => $matches[2],
            'addr' => $matches[3],
        ];
    }

    public function postalCodeFormat($postalCode)
    {
        $insert = '-';
        $num = 3;
        $postalCode = substr_replace($postalCode, $insert, $num, 0);
        return $postalCode;
    }

    public function telFormat($tel)
    {
        $insert = '-';
        $num = 3;
        $tel = substr_replace($tel, $insert, $num, 0);
        $num = 8;
        $tel = substr_replace($tel, $insert, $num, 0);
        return $tel;
    }
}