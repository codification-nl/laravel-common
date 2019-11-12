<?php

namespace Codification\Common\Test\TestCase
{
	use Codification\Common\Enum;

	/**
	 * @method static TestEnumFooBar HELLO()
	 */
	class TestEnumFooBar extends Enum\Enum
	{
		/** @var string */
		public const HELLO = 'hello';
	}
}