<?php

namespace Codification\Common\Test\TestCase
{
	use Codification\Common\Country\Exceptions\CountryCodeException;
	use Codification\Common\Money\Exceptions\CurrencyCodeException;
	use Codification\Common\Money\Money;

	class MoneyTest extends TestCase
	{
		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \PHPUnit\Framework\Exception
		 */
		public function it_has_helper_function() : void
		{
			$object = money(1, 'eur', 'nl');
			static::assertInstanceOf(Money::class, $object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 */
		public function it_can_handle_null() : void
		{
			$object = Money::make(null, 'eur', 'nl');
			static::assertNull($object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function it_throws_on_invalid_currency() : void
		{
			static::expectException(CurrencyCodeException::class);
			Money::make(1, 'abc', 'nl');
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function it_throws_on_invalid_locale() : void
		{
			static::expectException(CountryCodeException::class);
			Money::make(1, 'eur', 'abc');
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 */
		public function it_can_handle_empty_locale() : void
		{
			$this->app->setLocale('nl');

			$object = Money::make(1, 'eur');
			static::assertNotNull($object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_cast_to_string() : void
		{
			$object = Money::make(1, 'eur', 'nl');
			static::assertEquals('1.00', (string)$object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_encode_to_json() : void
		{
			$object = Money::make(1, 'eur', 'nl');
			static::assertEquals(json_encode('1.00'), json_encode($object));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_get_currency_code() : void
		{
			$object = Money::make(1, 'eur', 'nl');
			static::assertNotNull($object);
			static::assertEquals(978, $object->getCurrencyCode());
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_compare() : void
		{
			$object = Money::make(1, 'eur', 'nl');
			static::assertNotNull($object);
			$gt = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($gt);
			static::assertTrue($object->greaterThan($gt));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_add() : void
		{
			$object = Money::make(1, 'eur', 'nl');
			static::assertNotNull($object);
			$add = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($add);
			$object = $object->add($add);
			static::assertEquals('1.60', $object->format());
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_copy() : void
		{
			$object = Money::make(1, 'eur', 'nl');
			static::assertNotNull($object);

			$add = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($add);

			$copy   = $object->copy();
			$object = $object->add($add);
			static::assertNotTrue($object->equals($copy));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_has_zero() : void
		{
			$object = Money::zero('eur', 'nl');
			static::assertEquals('0.00', (string)$object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_min() : void
		{
			$a = Money::make(1, 'eur', 'nl');
			$b = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($a);
			static::assertNotNull($b);
			$object = Money::min($a, $b);
			static::assertEquals('0.60', (string)$object);

			$c = Money::make(2, 'eur', 'nl');
			static::assertNotNull($c);
			$object = Money::min($object, $c);
			static::assertEquals('0.60', (string)$object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_max() : void
		{
			$a = Money::make(1, 'eur', 'nl');
			$b = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($a);
			static::assertNotNull($b);
			$object = Money::max($a, $b);
			static::assertEquals('1.00', (string)$object);

			$c = Money::make(2, 'eur', 'nl');
			static::assertNotNull($c);
			$object = Money::max($object, $c);
			static::assertEquals('2.00', (string)$object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_sum() : void
		{
			$a = Money::make(1, 'eur', 'nl');
			$b = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($a);
			static::assertNotNull($b);
			$object = Money::sum($a, $b);
			static::assertEquals('1.60', (string)$object);

			$c = Money::make(2, 'eur', 'nl');
			static::assertNotNull($c);
			$object = Money::sum($object, $c);
			static::assertEquals('3.60', (string)$object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
		 * @throws \Codification\Common\Money\Exceptions\ParseException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_avg() : void
		{
			$a = Money::make(1, 'eur', 'nl');
			$b = Money::make(0.6, 'eur', 'nl');
			static::assertNotNull($a);
			static::assertNotNull($b);
			$object = Money::avg($a, $b);
			static::assertEquals('0.80', (string)$object);

			$c = Money::make(2, 'eur', 'nl');
			static::assertNotNull($c);
			$object = Money::avg($object, $c);
			static::assertEquals('1.40', (string)$object);
		}
	}
}