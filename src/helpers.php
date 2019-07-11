<?php

if (!function_exists('sanitize'))
{
	/**
	 * @param mixed $value
	 *
	 * @return string|null
	 */
	function sanitize($value) : ?string
	{
		$value = trim($value);

		if (strlen($value) === 0)
		{
			return null;
		}

		return $value;
	}
}