<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Phone\PhoneType;
	use Codification\Common\Country\Exceptions\LocaleException;
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
			$object = Phone::make(null, 'nl');
			$this->assertNull($object);
		}

		/** @test */
		public function it_throws_on_invalid_country()
		{
			$this->expectException(LocaleException::class);
			Phone::make('0474-12-34-56', 'abc');
		}

		/** @test */
		public function it_can_format()
		{
			$object = Phone::make('0474-12-34-56', 'be');
			$this->assertEquals('0032474123456', $object->format('nl'));
		}

		/** @test */
		public function it_can_format_with_null()
		{
			$this->app->setLocale('nl');
			$object = Phone::make('0474-12-34-56', 'be');
			$this->assertEquals('0032474123456', $object->format());
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