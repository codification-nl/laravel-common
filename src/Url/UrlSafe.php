<?php

namespace Codification\Common\Url
{
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;

	final class UrlSafe
	{
		/**
		 * @param string $data
		 * @param bool   $padding = false
		 *
		 * @return string
		 */
		public static function base64_encode(string $data, bool $padding = false) : string
		{
			$string = strtr(base64_encode($data), '+/', '-_');

			if (!$padding)
			{
				$string = rtrim($string, '=');
			}

			return $string;
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public static function base64_decode(string $string) : string
		{
			$result = base64_decode(strtr($string, '-_', '+/'));

			if ($result === false)
			{
				throw new ShouldNotHappenException('Failed to decode base64 string');
			}

			return $result;
		}
	}
}