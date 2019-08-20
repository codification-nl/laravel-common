<?php

namespace Codification\Common\Database\Eloquent
{
	abstract class Model extends \Illuminate\Database\Eloquent\Model
	{
		/** @var string[][] */
		protected static $traitAccessors = [];

		/** @var string[][] */
		protected static $traitMutators = [];

		/**
		 * @param array|false $attributes
		 */
		public function __construct($attributes)
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

			foreach (class_uses_recursive($class) as $trait)
			{
				$name = class_basename($trait);

				$accessor = "get{$name}Value";
				$mutator  = "set{$name}Value";

				if (method_exists($class, $accessor) && !in_array($accessor, static::$traitAccessors[$class]))
				{
					static::$traitAccessors[$class][] = $accessor;
				}

				if (method_exists($class, $mutator) && !in_array($mutator, static::$traitMutators[$class]))
				{
					static::$traitMutators[$class][] = $mutator;
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
		 * @return mixed
		 */
		public function getAttributeValue($key)
		{
			$value = parent::getAttributeValue($key);

			if ($value === null)
			{
				return null;
			}

			foreach (static::$traitAccessors[static::class] as $accessor)
			{
				if ($this->{$accessor}($key, $value))
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
			if ($value !== null)
			{
				foreach (static::$traitMutators[static::class] as $mutator)
				{
					if ($this->{$mutator}($key, $value))
					{
						break;
					}
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