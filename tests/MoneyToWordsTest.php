<?php declare(strict_types=1);

namespace JK\Utils\Tests;

use InvalidArgumentException;
use JK\Utils\MoneyToWords;
use PHPUnit\Framework\TestCase;

final class MoneyToWordsTest extends TestCase
{
    /**
     * @var MoneyToWords
     */
    private $f;

    public function __construct()
    {
        parent::__construct();

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

    public function testLargeNumberException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->f->spellout(1000000000);
    }
}
