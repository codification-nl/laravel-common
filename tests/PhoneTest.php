<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Enums\PhoneType;
	use Codification\Common\Exceptions\LocaleException;
	use Codification\Common\Support\Phone;
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
			$this->assertFalse(Phone::validate('0474-12-34-56', 'be', PhoneType::FIXED()));
			$this->assertTrue(Phone::validate('0474-12-34-56', 'be', PhoneType::BOTH()));

			$this->assertFalse(Phone::validate('0474-12-34-56', 'nl', PhoneType::MOBILE()));
			$this->assertFalse(Phone::validate('0474-12-34-56', 'nl', PhoneType::FIXED()));
			$this->assertFalse(Phone::validate('0474-12-34-56', 'nl', PhoneType::BOTH()));
		}

		/** @test */
		public function it_can_fail_validation()
		{
			$validator = Validator::make([
				'phone' => '0474-12-34-56',
			], [
				'phone' => Rule::phone(),
			]);

			$this->assertTrue($validator->fails());
		}

		/** @test */
		public function it_can_pass_validation()
		{
			$validator = Validator::make([
				'phone'         => '0474-12-34-56',
				'phone_country' => 'be',
			], [
				'phone' => Rule::phone(),
			]);

			$this->assertTrue($validator->passes());
		}

		/** @test */
		public function it_can_fail_fixed_validation()
		{
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
			$validator = Validator::make([
				'phone' => '0474-12-34-56',
				'test'  => 'be',
			], [
				'phone' => Rule::phone('test'),
			]);

			$this->assertTrue($validator->passes());
		}
	}
}