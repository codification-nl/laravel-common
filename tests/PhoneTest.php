<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Phone\ParseErrorType;
	use Codification\Common\Phone\PhoneType;
	use Codification\Common\Phone\Phone;
	use Codification\Common\Validation\Rule;
	use Illuminate\Support\Facades\Validator;

	class PhoneTest extends TestCase
	{
		/** @test */
		public function it_has_helper_function()
		{
			$object = phone('0612345678', 'nl');
			$this->assertInstanceOf(Phone::class, $object);
		}

		/** @test */
		public function it_can_handle_null()
		{
			$parse_error = ParseErrorType::NONE();

			$object = Phone::make(null, 'nl', $parse_error);
			$this->assertNull($object);
			$this->assertTrue($parse_error->eq(ParseErrorType::NOT_A_NUMBER()));
		}

		/** @test */
		public function it_throws_on_invalid_country()
		{
			$parse_error = ParseErrorType::NONE();

			Phone::make('0474-12-34-56', 'abc', $parse_error);
			$this->assertTrue($parse_error->eq(ParseErrorType::INVALID_COUNTRY_CODE()));
		}

		/** @test */
		public function it_can_format()
		{
			$parse_error = ParseErrorType::NONE();

			$object = Phone::make('0474-12-34-56', 'be', $parse_error);
			$this->assertEquals('0032474123456', $object->format('nl'));
			$this->assertTrue($parse_error->eq(ParseErrorType::NONE()));
		}

		/** @test */
		public function it_can_format_with_null()
		{
			$this->app->setLocale('nl');

			$parse_error = ParseErrorType::NONE();

			$object = Phone::make('0474-12-34-56', 'be', $parse_error);
			$this->assertEquals('0032474123456', $object->format());
			$this->assertTrue($parse_error->eq(ParseErrorType::NONE()));
		}

		/** @test */
		public function it_can_validate()
		{
			$this->assertTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::MOBILE()));
			$this->assertNotTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::FIXED()));
			$this->assertTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::BOTH()));

			$this->assertNotTrue(Phone::validate('0474-12-34-56', 'nl', PhoneType::MOBILE()));
			$this->assertNotTrue(Phone::validate('0474-12-34-56', 'nl', PhoneType::FIXED()));
			$this->assertNotTrue(Phone::validate('0474-12-34-56', 'nl', PhoneType::BOTH()));
		}

		/** @test */
		public function it_can_pass_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone(),
			]);

			$this->assertTrue($validator->passes());
		}

		/** @test */
		public function it_can_fail_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'nl',
			], [
				'phone' => Rule::phone(),
			]);

			$this->assertTrue($validator->fails());
		}

		/** @test */
		public function it_can_fail_country_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone' => '0474-12-34-56',
			], [
				'phone' => Rule::phone(),
			]);

			$this->assertTrue($validator->fails());
		}

		/** @test */
		public function it_can_fail_fixed_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone()->fixed(),
			]);

			$this->assertTrue($validator->fails());
		}

		/** @test */
		public function it_can_pass_mobile_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone()->mobile(),
			]);

			$this->assertTrue($validator->passes());
		}

		/** @test */
		public function it_can_pass_custom_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'phone'   => '0474-12-34-56',
				'country' => 'be',
			], [
				'phone' => Rule::phone('country'),
			]);

			$this->assertTrue($validator->passes());
		}
	}
}