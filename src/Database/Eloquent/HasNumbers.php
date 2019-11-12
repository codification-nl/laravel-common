<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Math;

	/**
	 * @mixin \Illuminate\Database\Eloquent\Concerns\HasAttributes
	 */
	trait HasNumbers
	{
		/**
		 * @return string
		 * @psalm-return class-string<\Codification\Math\Number>
		 */
		public function getHasNumbersType() : string
		{
			return Math\Number::class;
		}

		/**
		 * @return string
		 */
		public function getHasNumbersCast() : string
		{
			return 'number';
		}

		/**
		 * @param string $key
		 *
		 * @return int|null
		 */
		private function getNumberScale(string $key) : ?int
		{
			$cast  = $this->getCasts()[$key];
			$parts = explode(':', $cast, 2);
			$scale = $parts[1] ?? null;

			return $scale;
		}

		/**
		 * @param string                                           $key
		 * @param mixed|int|float|string|\Codification\Math\Number $out
		 * @param-out mixed|numeric|\Codification\Math\Number      $out
		 *
		 * @return bool
		 */
		public function getHasNumbersValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isNumberAttribute($key))
			{
				return false;
			}

			$scale = $this->getNumberScale($key);
			$out   = $this->asNumber($out, $scale);

			return true;
		}

		/**
		 * @param string                                           $key
		 * @param mixed|int|float|string|\Codification\Math\Number $out
		 * @param-out mixed|numeric|\Codification\Math\Number      $out
		 *
		 * @return bool
		 */
		public function setHasNumbersValue(string $key, &$out) : bool
		{
			if ($out === null || !$this->isNumberAttribute($key))
			{
				return false;
			}

			$out = $this->fromNumber($out);

			return true;
		}

		/**
		 * @param int|float|string $value
		 * @psalm-param numeric    $value
		 * @param int|null         $scale
		 *
		 * @return \Codification\Math\Number
		 */
		public function asNumber($value, int $scale = null) : Math\Number
		{
			return new Math\Number($value, $scale);
		}

		/**
		 * @param \Codification\Math\Number $value
		 *
		 * @return string
		 */
		public function fromNumber(Math\Number $value) : string
		{
			return $value->getValue();
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isNumberCastable(string $key) : bool
		{
			return $this->hasCast($key, $this->getHasNumbersCast());
		}

		/**
		 * @param string $key
		 *
		 * @return bool
		 */
		protected function isNumberAttribute(string $key) : bool
		{
			return $this->isNumberCastable($key);
		}
	}
}