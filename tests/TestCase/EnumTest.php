<?php

namespace Codification\Common\Test\TestCase
{
	use Illuminate\Support\Facades\Validator;

	class EnumTest extends TestCase
	{
		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_construct() : void
		{
			$object = TestEnum::make('hello');
			static::assertTrue($object->eq(TestEnum::HELLO()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ParseException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_parse() : void
		{
			$object = TestEnum::parse('HELLO');
			static::assertTrue($object->eq(TestEnum::HELLO()));
			static::assertNotTrue($object->eq(TestEnum::WORLD()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ParseException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public function it_throws_on_invalid_parse() : void
		{
			static::expectException(\Codification\Common\Enum\Exceptions\ParseException::class);
			TestEnum::parse('abc');
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public function it_throws_on_invalid_value() : void
		{
			static::expectException(\Codification\Common\Enum\Exceptions\ValueException::class);
			TestEnum::make('abc');
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\ParseException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public function it_throws_on_invalid() : void
		{
			static::expectException(\Codification\Common\Enum\Exceptions\ParseException::class);
			/** @noinspection PhpUndefinedMethodInspection */
			TestEnum::ABC();
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_cast_to_string() : void
		{
			$object = TestEnum::HELLO();
			static::assertEquals('hello', (string)$object);
		}

		/**
		 * @test
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_encode_to_json() : void
		{
			$object = TestEnum::HELLO();
			static::assertEquals(json_encode('hello'), json_encode($object));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_compare() : void
		{
			$object = TestEnum::HELLO();

			static::assertTrue($object->eq(TestEnum::HELLO()));
			static::assertTrue($object->eq(TestEnum::HELLO_ALSO()));
			static::assertTrue($object->eq(TestEnumFooBar::HELLO(), false));

			static::assertNotTrue($object->eq(TestEnum::WORLD()));
			static::assertNotTrue($object->eq(TestEnumFooBar::HELLO()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public function it_throws_on_assert() : void
		{
			static::expectException(\Codification\Common\Enum\Exceptions\EnumException::class);
			TestEnum::assertType(TestEnumFooBar::HELLO());
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \PHPUnit\Framework\Exception
		 * @throws \PHPUnit\Framework\ExpectationFailedException
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_have_hidden() : void
		{
			$keys = TestEnum::keys();
			static::assertArrayNotHasKey('NONE', $keys);
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_validate() : void
		{
			static::assertTrue(TestEnum::isValid('hello'));
			static::assertNotTrue(TestEnum::isValid('bye'));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_have_flags() : void
		{
			$object = TestEnumHasFlags::FIRST();
			static::assertTrue($object->eq(TestEnumHasFlags::FIRST()));
			static::assertNotTrue($object->has(TestEnumHasFlags::SECOND()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_set_flags() : void
		{
			$object = TestEnumHasFlags::NONE();

			$object = $object->set(TestEnumHasFlags::FIRST());
			static::assertTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertNotTrue($object->has(TestEnumHasFlags::SECOND()));

			$object = $object->set(TestEnumHasFlags::SECOND());
			static::assertTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertTrue($object->has(TestEnumHasFlags::SECOND()));
			static::assertTrue($object->eq(TestEnumHasFlags::BOTH()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_remove_flags() : void
		{
			$object = TestEnumHasFlags::BOTH();
			$object = $object->remove(TestEnumHasFlags::SECOND(), TestEnumHasFlags::FIRST());
			static::assertNotTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertNotTrue($object->has(TestEnumHasFlags::SECOND()));
			static::assertTrue($object->eq(TestEnumHasFlags::NONE()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_make_flags_from_string() : void
		{
			$object = TestEnumHasFlags::make('FIRST');
			static::assertTrue($object->eq(TestEnumHasFlags::FIRST()));
			static::assertNotTrue($object->has(TestEnumHasFlags::SECOND()));

			$object = TestEnumHasFlags::make('FIRST,SECOND');
			static::assertTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertTrue($object->has(TestEnumHasFlags::SECOND()));
			static::assertTrue($object->eq(TestEnumHasFlags::BOTH()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_make_flags_from_array() : void
		{
			$object = TestEnumHasFlags::make(['FIRST', 'SECOND']);
			static::assertTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertTrue($object->has(TestEnumHasFlags::SECOND()));
			static::assertTrue($object->eq(TestEnumHasFlags::BOTH()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_make_flags_from_int() : void
		{
			$object = TestEnumHasFlags::make(0);
			static::assertNotTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertNotTrue($object->has(TestEnumHasFlags::SECOND()));
			static::assertTrue($object->eq(TestEnumHasFlags::NONE()));
		}

		/**
		 * @test
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \PHPUnit\Framework\AssertionFailedError
		 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
		 */
		public function it_can_make_flags_combination() : void
		{
			/** @var int $second */
			$second = TestEnumHasFlags::SECOND;

			/** @var int $third */
			$third = TestEnumHasFlags::THIRD;

			$object = TestEnumHasFlags::make($second | $third);
			static::assertNotTrue($object->has(TestEnumHasFlags::FIRST()));
			static::assertTrue($object->has(TestEnumHasFlags::THIRD()));
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
				'enum' => 'hello',
			], [
				'enum' => TestEnum::rule(),
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
				'enum' => 'abc',
			], [
				'enum' => TestEnum::rule(),
			]);

			static::assertTrue($validator->fails());
		}
	}
}