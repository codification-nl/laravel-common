<?php

namespace Codification\Common\Support
{
	final class UrlSafe
	{
		/**
		 * @param string $data
		 * @param bool   $padding
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
		 */
		public static function base64_decode(string $string) : string
		{
			return base64_decode(strtr($string, '-_', '+/'));
		}
	}
}