<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Money\Exceptions\CurrencyCodeException;
	use Codification\Common\Country\Exceptions\CountryCodeException;
	use Codification\Common\Money\Money;

	class MoneyTest extends TestCase
	{
		/** @test */
		public function it_has_helper_function()
		{
			$object = money(1, 'eur', 'nl');
			$this->assertInstanceOf(Money::class, $object);
		}

		/** @test */
		public function it_can_handle_null()
		{
			$object = Money::make(null, 'eur', 'nl');
			$this->assertNull($object);
		}

		/** @test */
		public function it_throws_on_invalid_currency()
		{
			$this->expectException(CurrencyCodeException::class);
			Money::make(1, 'abc', 'nl');
		}

		/** @test */
		public function it_throws_on_invalid_locale()
		{
			$this->expectException(CountryCodeException::class);
			Money::make(1, 'eur', 'abc');
		}

		/** @test */
		public function it_can_handle_empty_locale()
		{
			$this->app->setLocale('nl');

			$object = Money::make(1, 'eur');
			$this->assertNotNull($object);
		}

		/** @test */
		public function it_can_cast_to_string()
		{
			$object = Money::make(1, 'eur', 'nl');
			$this->assertEquals('1.00', (string)$object);
		}

		/** @test */
		public function it_can_encode_to_json()
		{
			$object = Money::make(1, 'eur', 'nl');
			$this->assertEquals(json_encode('1.00'), json_encode($object));
		}

		/** @test */
		public function it_can_get_currency_code()
		{
			$object = Money::make(1, 'eur', 'nl');
			$this->assertEquals(978, $object->getCurrencyCode());
		}

		/** @test */
		public function it_can_compare()
		{
			$object = Money::make(1, 'eur', 'nl');
			$this->assertTrue($object->greaterThan(Money::make(0.6, 'eur', 'nl')));
		}

		/** @test */
		public function it_can_add()
		{
			$object = Money::make(1, 'eur', 'nl');
			$object = $object->add(Money::make(0.6, 'eur', 'nl'));
			$this->assertEquals('1.60', $object->format());
		}

		/** @test */
		public function it_can_copy()
		{
			$object = Money::make(1, 'eur', 'nl');
			$copy   = $object->copy();
			$object = $object->add(Money::make(0.6, 'eur', 'nl'));
			$this->assertNotTrue($object->equals($copy));
		}

		/** @test */
		public function it_has_zero()
		{
			$object = Money::zero('eur', 'nl');
			$this->assertEquals('0.00', (string)$object);
		}

		/** @test */
		public function it_can_min()
		{
			$a      = Money::make(1, 'eur', 'nl');
			$b      = Money::make(0.6, 'eur', 'nl');
			$object = Money::min($a, $b);
			$this->assertEquals('0.60', (string)$object);

			$c      = Money::make(2, 'eur', 'nl');
			$object = Money::min($object, $c);
			$this->assertEquals('0.60', (string)$object);
		}

		/** @test */
		public function it_can_max()
		{
			$a      = Money::make(1, 'eur', 'nl');
			$b      = Money::make(0.6, 'eur', 'nl');
			$object = Money::max($a, $b);
			$this->assertEquals('1.00', (string)$object);

			$c      = Money::make(2, 'eur', 'nl');
			$object = Money::max($object, $c);
			$this->assertEquals('2.00', (string)$object);
		}

		/** @test */
		public function it_can_sum()
		{
			$a      = Money::make(1, 'eur', 'nl');
			$b      = Money::make(0.6, 'eur', 'nl');
			$object = Money::sum($a, $b);
			$this->assertEquals('1.60', (string)$object);

			$c      = Money::make(2, 'eur', 'nl');
			$object = Money::sum($object, $c);
			$this->assertEquals('3.60', (string)$object);
		}

		/** @test */
		public function it_can_avg()
		{
			$a      = Money::make(1, 'eur', 'nl');
			$b      = Money::make(0.6, 'eur', 'nl');
			$object = Money::avg($a, $b);
			$this->assertEquals('0.80', (string)$object);

			$c      = Money::make(2, 'eur', 'nl');
			$object = Money::avg($object, $c);
			$this->assertEquals('1.40', (string)$object);
		}
	}
}