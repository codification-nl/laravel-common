<?php

namespace Codification\Common\Test\TestCase
{
	use Codification\Common\Phone\ParseErrorType;
	use Codification\Common\Phone\Phone;
	use Codification\Common\Phone\PhoneType;
	use Codification\Common\Validation\Rule;
	use Illuminate\Support\Facades\Validator;

	class PhoneTest extends TestCase
	{
		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \PHPUnit\Framework\Exception
		 */
		public function it_has_helper_function() : void
		{
			$object = phone('0612345678', 'nl');
			static::assertInstanceOf(Phone::class, $object);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_handle_null() : void
		{
			$parse_error = ParseErrorType::NONE();
			$object      = Phone::make(null, 'nl', $parse_error);
			static::assertNull($object);
			static::assertNotNull($parse_error);
			static::assertTrue($parse_error->eq(ParseErrorType::NOT_A_NUMBER()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_throws_on_invalid_country() : void
		{
			$parse_error = ParseErrorType::NONE();
			$object      = Phone::make('0474-12-34-56', 'abc', $parse_error);
			static::assertNull($object);
			static::assertNotNull($parse_error);
			static::assertTrue($parse_error->eq(ParseErrorType::INVALID_COUNTRY_CODE()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_format() : void
		{
			$parse_error = ParseErrorType::NONE();
			$object      = Phone::make('0474-12-34-56', 'be', $parse_error);
			static::assertNotNull($object);
			static::assertEquals('0032474123456', $object->format('nl'));
			static::assertNotNull($parse_error);
			static::assertTrue($parse_error->eq(ParseErrorType::NONE()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_format_with_null() : void
		{
			$this->app->setLocale('nl');

			$parse_error = ParseErrorType::NONE();

			$object = Phone::make('0474-12-34-56', 'be', $parse_error);
			static::assertNotNull($object);
			static::assertEquals('0032474123456', $object->format());
			static::assertNotNull($parse_error);
			static::assertTrue($parse_error->eq(ParseErrorType::NONE()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_validate() : void
		{
			static::assertTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::MOBILE()));
			static::assertNotTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::FIXED()));
			static::assertTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::BOTH()));

			static::assertNotTrue(Phone::validate('0474-12-34-56', 'nl', PhoneType::MOBILE()));
			static::assertNotTrue(Phone::validate('0474-12-34-56', 'nl', PhoneType::FIXED()));
			static::assertNotTrue(Phone::validate('0474-12-34-56', 'nl', PhoneType::BOTH()));
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
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone(),
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
				'phone'         => '0474-12-34-56',
				'phone_country' => 'nl',
			], [
				'phone' => Rule::phone(),
			]);

			static::assertTrue($validator->fails());
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_fail_country_validation() : void
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone' => '0474-12-34-56',
			], [
				'phone' => Rule::phone(),
			]);

			static::assertTrue($validator->fails());
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_fail_fixed_validation() : void
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone()->fixed(),
			]);

			static::assertTrue($validator->fails());
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_pass_mobile_validation() : void
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone()->mobile(),
			]);

			static::assertTrue($validator->passes());
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_pass_custom_validation() : void
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'   => '0474-12-34-56',
				'country' => 'be',
			], [
				'phone' => Rule::phone('country'),
			]);

			static::assertTrue($validator->passes());
		}
	}
}