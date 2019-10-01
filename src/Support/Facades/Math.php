<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static \Codification\Math\Number|string add(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number|string sub(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number|string mul(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number|string div(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
	 * @method static \Codification\Math\Number|string mod(\Codification\Math\Number|string|float|int $lhs, \Codification\Math\Number|string|float|int $rhs)
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