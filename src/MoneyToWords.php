<?php

namespace JK\Utils;

use InvalidArgumentException;

final class MoneyToWords
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var boolean
     */
    private $replaceSpaces;

    /**
     * @var string
     */
    private $words;

    private static $ones = ['nula', 'jedna', 'dva', 'tři', 'čtyři', 'pět', 'šest', 'sedm', 'osm', 'devět', 'deset', 'jedenáct', 'dvanáct', 'třináct', 'čtrnáct', 'patnáct', 'šestnáct', 'sedmnáct', 'osmnáct', 'devatenáct'];
    private static $tens = ['', 'deset', 'dvacet', 'třicet', 'čtyřicet', 'padesát', 'šedesát', 'sedmdesát', 'osmdesát', 'devadesát'];
    private static $hundreds = ['', 'jedno sto', 'dvě stě', 'tři sta', 'čtyři sta', 'pět set', 'šest set', 'sedm set', 'osm set', 'devět set'];
    private static $thousands = ['', 'jeden tisíc', 'tisíce', 'tisíc'];
    private static $millions = ['', 'jeden milion', 'miliony', 'milionů'];

    /**
     * @param float|integer|string $amount
     * @param boolean $replaceSpaces
     */
    public function spellout($amount, $replaceSpaces = true)
    {
        $this->amount = floatval($amount);

        if (999999999 < $this->amount) {
            throw new InvalidArgumentException('Parsing larger numbers than 1.000.000.000 is not supported');
        }

        $this->replaceSpaces = $replaceSpaces;
        $this->words = '';

        if (0 > $this->amount) {
            $this->words = 'mínus ';
            $this->amount = abs($this->amount);
        }

        list($whole, $decimal) = sscanf(sprintf('%0.2f', $this->amount), '%d.%d');

        $this->words .= $this->parseNumber($whole);

        if (1 === $whole) {
            $this->words .= ' koruna česká';
        } elseif (5 > $whole) {
            $this->words .= ' koruny české';
        } else {
            $this->words .= ' korun českých';
        }

        if (0 < $decimal) {
            $decimalPart = $this->parseNumber($decimal);

            if (1 === $decimal) {
                $this->words .= ' jeden haléř';
            } elseif (5 > $decimal) {
                $this->words .= ' ' . $decimalPart . ' haléře';
            } else {
                $this->words .= ' ' . $decimalPart . ' haléřů';
            }
        }

        return $this->words;
    }

    private function parseNumber($number)
    {
        $words = '';

        if (100 > $number) {
            $words .= $this->onesAndTens($number);
        } elseif (1000 > $number) {
            $words .= $this->hundreds($number);
        } elseif (1000000 > $number) {
            $words .= $this->thousands($number);
        } elseif (1000000000 > $number) {
            $words .= $this->millions($number);
        }

        return preg_replace('/\s+/', $this->replaceSpaces ? '' : ' ', trim($words));
    }

    private function onesAndTens($number)
    {
        $words = '';

        if ($number < 20) {
            $words .= self::$ones[$number];
        } else {
            $words .= self::$tens[intval($number / 10)];
            if (($number % 10) > 0) {
                $words .= ' ' . self::$ones[$number % 10];
            }
        }

        return $words;
    }

    private function hundreds($number)
    {
        $words = '';
        $hundreds = intval($number / 100);
        $tens = ($number % 100);

        if (100 > $number) {
            return $this->onesAndTens($number);
        }

        $words .= self::$hundreds[$hundreds];

        if (0 < $tens) {
            $words .= ' ' . $this->onesAndTens($tens);
        }

        return $words;
    }

    private function thousands($number)
    {
        $words = '';
        $thousands = intval($number / 1000);
        $hundreds = ($number % 1000);

        $words .= $this->hundreds($thousands) . ' ';

        if (1000 > $number) {
            return $this->hundreds($number);
        } elseif (2000 > $number) {
            $words = self::$thousands[1];
        } elseif (5000 > $number) {
            $words .= self::$thousands[2];
        } else {
            $words .= self::$thousands[3];
        }

        if (0 < $hundreds) {
            $words .= ' ' . $this->hundreds($hundreds);
        }

        return $words;
    }

    private function millions($number)
    {
        $words = '';
        $millions = intval($number / 1000000);
        $thousands = ($number % 1000000);

        $words .= $this->hundreds($millions) . ' ';

        if (2000000 > $number) {
            $words = self::$millions[1];
        } elseif (5000000 > $number) {
            $words .= self::$millions[2];
        } else {
            $words .= self::$millions[3];
        }

        if (0 < $thousands) {
            $words .= ' ' . $this->thousands($thousands);
        }

        return $words;
    }
}