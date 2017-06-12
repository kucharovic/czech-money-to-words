<?php
namespace JK\Utils\Tests;

use JK\Utils\MoneyToWords;
use PHPUnit_Framework_TestCase;

final class MoneyToWordsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MoneyToWords
     */
    private $f;

    protected function setUp()
    {
        $this->f = new MoneyToWords();
    }

    public function testSimpleNumber()
    {
        $this->assertSame('jednostodvacettři korun českých', $this->f->spellout(123));
    }

    public function testSimpleNumberWithSpaces()
    {
        $this->assertSame('jedno sto dvacet tři korun českých', $this->f->spellout(123, MoneyToWords::PRESERVE_SPACES));
    }

    public function testNegativeNumber()
    {
        $this->assertSame('mínus jednostodvacettři korun českých', $this->f->spellout(-123));
    }

    public function testFloatNumber()
    {
        $this->assertSame('jednostodvacettři korun českých padesát haléřů', $this->f->spellout(123.5));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLargeNumberException()
    {
        $this->f->spellout(1000000000);
    }
}