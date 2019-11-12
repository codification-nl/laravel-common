<?php

namespace Codification\Common\Url
{
	use Codification\Common\Contracts\Support\Stringable;
	use Codification\Common\Support\Dictionary;

	/**
	 * @template-extends \Codification\Common\Support\Dictionary<array-key, mixed>
	 */
	final class UrlQueryParams extends Dictionary implements Stringable
	{
		/**
		 * @param array<array-key, mixed> $parameters
		 *
		 * @return static
		 */
		public static function make(array $parameters) : self
		{
			return new static($parameters);
		}

		/**
		 * @param string $query
		 *
		 * @return static
		 */
		public static function parse(string $query) : self
		{
			$parameters = [];

			parse_str($query, $parameters);

			return new static($parameters);
		}

		/**
		 * @return string
		 */
		public function toString() : string
		{
			return http_build_query($this->items);
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return $this->toString();
		}
	}
}