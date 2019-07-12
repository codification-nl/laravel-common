<?php

namespace Codification\Common\Tests
{
	use Codification\Common\Support\Country;
	use Illuminate\Support\Facades\Validator;

	class CountryTest extends TestCase
	{
		/** @test */
		public function it_can_validate()
		{
			$this->assertTrue(Country::isValid('nl'));
			$this->assertFalse(Country::isValid('abc'));
		}

		/** @test */
		public function it_can_pass_validation()
		{
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
			$validator = Validator::make([
				'test' => 'abc',
			], [
				'test' => 'country',
			]);

			$this->assertTrue($validator->fails());
		}
	}
}