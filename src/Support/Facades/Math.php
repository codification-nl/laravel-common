<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static \Codification\Math\Number add(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number sub(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number mul(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number div(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number mod(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 */
	class Math extends Facade
	{
		/**
		 * @return string
		 */
		protected static function getFacadeAccessor()
		{
			return 'math';
		}
	}
}