<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Country\Country;
	use Codification\Common\Country\Exceptions\InvalidCountryCodeException;
	use Illuminate\Support\Facades\Validator;

	class CountryTest extends TestCase
	{
		/** @test */
		public function it_can_validate()
		{
			$this->assertTrue(Country::isValid('nl'));
			$this->assertNotTrue(Country::isValid('abc'));
		}

		/** @test */
		public function it_can_assert()
		{
			$this->expectException(InvalidCountryCodeException::class);
			Country::ensureValid('abc');
		}

		/** @test */
		public function it_can_pass_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'test' => 'nl',
			], [
				'test' => 'country',
			]);

			$this->assertTrue($validator->passes());
		}

		/** @test */
		public function it_can_fail_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'test' => 'abc',
			], [
				'test' => 'country',
			]);

			$this->assertTrue($validator->fails());
		}
	}
}