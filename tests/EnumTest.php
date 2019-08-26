<?php

namespace Codification\Common\Tests
{
	use Illuminate\Support\Facades\Validator;

	class EnumTest extends TestCase
	{
		/** @test */
		public function it_can_construct()
		{
			$object = TestEnum::make('hello');
			$this->assertTrue($object->eq(TestEnum::HELLO()));
		}

		/** @test */
		public function it_can_parse()
		{
			$object = TestEnum::parse('HELLO');
			$this->assertTrue($object->eq(TestEnum::HELLO()));
			$this->assertNotTrue($object->eq(TestEnum::WORLD()));
		}

		/** @test */
		public function it_throws_on_invalid_parse()
		{
			$this->expectException(\UnexpectedValueException::class);
			TestEnum::parse('abc');
		}

		/** @test */
		public function it_throws_on_invalid_value()
		{
			$this->expectException(\UnexpectedValueException::class);
			TestEnum::make('abc');
		}

		/** @test */
		public function it_throws_on_invalid()
		{
			$this->expectException(\UnexpectedValueException::class);
			TestEnum::ABC();
		}

		/** @test */
		public function it_can_cast_to_string()
		{
			$object = TestEnum::HELLO();
			$this->assertEquals('hello', (string)$object);
		}

		/** @test */
		public function it_can_encode_to_json()
		{
			$object = TestEnum::HELLO();
			$this->assertEquals(json_encode('hello'), json_encode($object));
		}

		/** @test */
		public function it_can_compare()
		{
			$object = TestEnum::HELLO();

			$this->assertTrue($object->eq(TestEnum::HELLO()));
			$this->assertTrue($object->eq(TestEnum::HELLO_ALSO()));
			$this->assertTrue($object->eq(TestEnumFooBar::HELLO(), false));

			$this->assertNotTrue($object->eq(TestEnum::WORLD()));
			$this->assertNotTrue($object->eq(TestEnumFooBar::HELLO()));
		}

		/** @test */
		public function it_throws_on_assert()
		{
			$this->expectException(\UnexpectedValueException::class);
			TestEnum::assertType(TestEnumFooBar::HELLO());
		}

		/** @test */
		public function it_can_have_hidden()
		{
			$keys = TestEnum::keys();
			$this->assertArrayNotHasKey('NONE', $keys);
		}

		/** @test */
		public function it_can_validate()
		{
			$this->assertTrue(TestEnum::isValid('hello'));
			$this->assertNotTrue(TestEnum::isValid('bye'));
		}

		/** @test */
		public function it_can_have_flags()
		{
			$object = TestEnumHasFlags::FIRST();
			$this->assertTrue($object->eq(TestEnumHasFlags::FIRST()));
			$this->assertNotTrue($object->has(TestEnumHasFlags::SECOND()));
		}

		/** @test */
		public function it_can_set_flags()
		{
			$object = TestEnumHasFlags::NONE();

			$object = $object->set(TestEnumHasFlags::FIRST());
			$this->assertTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertNotTrue($object->has(TestEnumHasFlags::SECOND()));

			$object = $object->set(TestEnumHasFlags::SECOND());
			$this->assertTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertTrue($object->has(TestEnumHasFlags::SECOND()));
			$this->assertTrue($object->eq(TestEnumHasFlags::BOTH()));
		}

		/** @test */
		public function it_can_remove_flags()
		{
			$object = TestEnumHasFlags::BOTH();
			$object = $object->remove(TestEnumHasFlags::SECOND(), TestEnumHasFlags::FIRST());
			$this->assertNotTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertNotTrue($object->has(TestEnumHasFlags::SECOND()));
			$this->assertTrue($object->eq(TestEnumHasFlags::NONE()));
		}

		/** @test */
		public function it_can_make_flags_from_string()
		{
			$object = TestEnumHasFlags::make('FIRST');
			$this->assertTrue($object->eq(TestEnumHasFlags::FIRST()));
			$this->assertNotTrue($object->has(TestEnumHasFlags::SECOND()));

			$object = TestEnumHasFlags::make('FIRST,SECOND');
			$this->assertTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertTrue($object->has(TestEnumHasFlags::SECOND()));
			$this->assertTrue($object->eq(TestEnumHasFlags::BOTH()));
		}

		/** @test */
		public function it_can_make_flags_from_array()
		{
			$object = TestEnumHasFlags::make(['FIRST', 'SECOND']);
			$this->assertTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertTrue($object->has(TestEnumHasFlags::SECOND()));
			$this->assertTrue($object->eq(TestEnumHasFlags::BOTH()));
		}

		/** @test */
		public function it_can_make_flags_from_int()
		{
			$object = TestEnumHasFlags::make(0);
			$this->assertNotTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertNotTrue($object->has(TestEnumHasFlags::SECOND()));
			$this->assertTrue($object->eq(TestEnumHasFlags::NONE()));
		}

		/** @test */
		public function it_can_make_flags_combination()
		{
			$object = TestEnumHasFlags::make(TestEnumHasFlags::SECOND | TestEnumHasFlags::THIRD);
			$this->assertNotTrue($object->has(TestEnumHasFlags::FIRST()));
			$this->assertTrue($object->has(TestEnumHasFlags::THIRD()));
		}

		/** @test */
		public function it_can_pass_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'enum' => 'hello',
			], [
				'enum' => TestEnum::rule(),
			]);

			$this->assertTrue($validator->passes());
		}

		/** @test */
		public function it_can_fail_validation()
		{
			/** @var \Illuminate\Validation\Validator $validator */
			$validator = Validator::make([
				'enum' => 'abc',
			], [
				'enum' => TestEnum::rule(),
			]);

			$this->assertTrue($validator->fails());
		}
	}
}