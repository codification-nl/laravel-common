<?php

namespace Codification\Common\Database\Eloquent
{
	abstract class Model extends \Illuminate\Database\Eloquent\Model
	{
		/** @var string[][] */
		protected static $traitAccessors = [];

		/** @var string[][] */
		protected static $traitMutators = [];

		/** @var string[][] */
		protected static $traitCasts = [];

		/**
		 * @param array|false $attributes
		 */
		public function __construct($attributes = [])
		{
			if ($attributes !== false)
			{
				parent::__construct($attributes);
			}
		}

		/**
		 * @return void
		 */
		protected static function bootTraits() : void
		{
			parent::bootTraits();

			$class = static::class;

			static::$traitAccessors[$class] = [];
			static::$traitMutators[$class]  = [];
			static::$traitCasts[$class]     = [];

			foreach (class_uses_recursive($class) as $trait)
			{
				$name = class_basename($trait);

				$accessor = "get{$name}Value";
				$mutator  = "set{$name}Value";
				$cast     = "get{$name}Cast";

				if (method_exists($class, $accessor) && !in_array($accessor, static::$traitAccessors[$class]))
				{
					static::$traitAccessors[$class][] = $accessor;
				}

				if (method_exists($class, $mutator) && !in_array($mutator, static::$traitMutators[$class]))
				{
					static::$traitMutators[$class][] = $mutator;
				}

				if (method_exists($class, $cast) && !in_array($cast, static::$traitCasts[$class]))
				{
					static::$traitCasts[$class][] = $cast;
				}
			}
		}

		/**
		 * @return string
		 */
		public function getClass() : string
		{
			return static::class;
		}

		/**
		 * @return string
		 */
		public function getQualifiedTable() : string
		{
			return "{$this->getConnectionName()}.{$this->getTable()}";
		}

		/**
		 * @param string $key
		 *
		 * @return string
		 */
		protected function getCastType($key) : string
		{
			$cast = $this->getCasts()[$key];

			foreach (static::$traitCasts[static::class] as $method)
			{
				$custom = $this->{$method}();

				if (strncmp($cast, $custom, strlen($custom)) === 0)
				{
					return /** @var string $custom */ $custom;
				}
			}

			return parent::getCastType($key);
		}

		/**
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function getAttributeValue($key)
		{
			$value = parent::getAttributeValue($key);

			foreach (static::$traitAccessors[static::class] as $method)
			{
				if ($this->{$method}($key, $value))
				{
					return $value;
				}
			}

			return $value;
		}

		/**
		 * @param string $key
		 * @param mixed  $value
		 *
		 * @return mixed
		 */
		public function setAttribute($key, $value)
		{
			foreach (static::$traitMutators[static::class] as $method)
			{
				if ($this->{$method}($key, $value))
				{
					break;
				}
			}

			return parent::setAttribute($key, $value);
		}

		/**
		 * @return $this
		 */
		public static function dummy() : self
		{
			return new static(false);
		}
	}
}