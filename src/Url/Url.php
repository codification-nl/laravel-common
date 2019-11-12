<?php

/** @noinspection RegExpRedundantEscape */

/** @noinspection RegExpUnexpectedAnchor */

namespace Codification\Common\Url
{
	use Codification\Common\Contracts\Support\Stringable;
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;
	use Illuminate\Support\Str;

	final class Url implements Stringable
	{
		/** @var string */
		public const SCHEME_PATTERN = /** @lang RegExp */
			'/^https?:\/\//';

		/** @var string */
		public const HOST_PATTERN = /** @lang RegExp */
			'/^([a-zA-Z]+:\/\/)?www\./';

		/** @var string */
		public $scheme = 'https';

		/** @var string */
		public $host = 'localhost';

		/** @var int|null */
		public $port = null;

		/** @var string */
		public $path = '';

		/** @var \Codification\Common\Url\UrlQueryParams|null */
		public $query = null;

		/** @var string|null */
		public $fragment = null;

		/**
		 * @param string                                     $url
		 * @param \Codification\Common\Url\UrlParseFlags|int $flags
		 *
		 * @return \Codification\Common\Url\Url
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \InvalidArgumentException
		 */
		public static function parse(string $url, $flags = UrlParseFlags::ALL) : Url
		{
			/**
			 * @var array<string, string|null> $parsed
			 * @psalm-var array{1: string} $parsed
			 */
			$parsed = parse_url(static::sanitize($url, $flags));

			$result = new static();

			foreach (UrlPart::values() as $part)
			{
				switch ($part)
				{
					case UrlPart::HOST:
						$result->host = $parsed[UrlPart::HOST] ?? '';
						break;

					case UrlPart::PATH:
						$result->path = $parsed[UrlPart::PATH] ?? '/';
						break;

					case  UrlPart::QUERY:
						$result->query = UrlQueryParams::parse($parsed[UrlPart::QUERY] ?? '');
						break;

					default:
						$result->{$part} = $parsed[$part] ?? null;
						break;
				}
			}

			return $result;
		}

		/**
		 * @param string                                     $url
		 * @param \Codification\Common\Url\UrlParseFlags|int $flags
		 *
		 * @return string
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \InvalidArgumentException
		 */
		public static function sanitize(string $url, $flags = UrlParseFlags::ALL) : string
		{
			$url = sanitize($url);

			if ($url === null)
			{
				throw new \InvalidArgumentException();
			}

			if (!($flags instanceof UrlParseFlags))
			{
				$flags = UrlParseFlags::make($flags);
			}

			if (preg_match(static::SCHEME_PATTERN, $url) !== 1)
			{
				$scheme = $flags->has(UrlParseFlags::SECURE()) ? 'https://' : 'http://';
				$url    = $scheme . $url;
			}

			if ($flags->has(UrlParseFlags::STRIP_WWW()))
			{
				/**
				 * @var string[] $matches
				 * @psalm-var array{1: string} $matches
				 */
				$matches = [];

				if (preg_match(static::HOST_PATTERN, $url, $matches) === 1)
				{
					/** @var string $scheme */
					[, $scheme] = $matches + [null, ''];

					$url = preg_replace(static::HOST_PATTERN, $scheme, $url);

					if ($url === null)
					{
						throw new ShouldNotHappenException('Failed to replace');
					}
				}
			}

			return $url;
		}

		/**
		 * @param string|array<string> $path
		 *
		 * @return $this
		 */
		public function path($path) : self
		{
			$this->path = Str::start(implode('/', is_array($path) ? $path : func_get_args()), '/');

			return $this;
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function humanize() : string
		{
			if (substr($this->host, 0, 4) === 'www.')
			{
				$host = substr($this->host, 4);

				if ($host === false)
				{
					throw new ShouldNotHappenException('Failed to get part of string');
				}

				return $host;
			}

			return $this->host;
		}

		/**
		 * @return string
		 */
		public function toString() : string
		{
			$result = "{$this->scheme}://{$this->host}";

			if ($this->port !== null)
			{
				$result .= ":{$this->port}";
			}

			$result .= $this->path;

			if ($this->query !== null && count($this->query))
			{
				$result .= "?{$this->query->toString()}";
			}

			if ($this->fragment !== null)
			{
				$result .= "#{$this->fragment}";
			}

			return $result;
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return $this->toString();
		}

		/**
		 * @return string
		 */
		public function jsonSerialize() : string
		{
			return $this->toString();
		}
	}
}