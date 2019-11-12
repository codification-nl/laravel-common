<?php

namespace Codification\Common\Test\TestCase
{
	use Codification\Common\Country\Country;
	use Codification\Common\Country\Exceptions\CountryCodeException;
	use Illuminate\Support\Facades\Validator;

	class CountryTest extends TestCase
	{
		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_validate() : void
		{
			static::assertTrue(Country::isValid('nl'));
			static::assertNotTrue(Country::isValid('abc'));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public function it_can_assert() : void
		{
			static::expectException(CountryCodeException::class);
			Country::ensureValid('abc');
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_pass_validation() : void
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'test' => 'nl',
			], [
				'test' => 'country',
			]);

			static::assertTrue($validator->passes());
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_fail_validation() : void
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'test' => 'abc',
			], [
				'test' => 'country',
			]);

			static::assertTrue($validator->fails());
		}
	}
}