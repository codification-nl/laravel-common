<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;
	use Illuminate\Database\Eloquent\MassAssignmentException;

	/** @psalm-suppress PropertyNotSetInConstructor */
	abstract class Model extends \Illuminate\Database\Eloquent\Model
	{
		/** @var array<class-string, list<callable-string>> */
		protected static $traitAccessors = [];

		/** @var array<class-string, list<callable-string>> */
		protected static $traitMutators = [];

		/** @var array<class-string, list<callable-string>> */
		protected static $traitCasts = [];

		/**
		 * @return string
		 * @psalm-return class-string<\Codification\Common\Database\Eloquent\Model>
		 */
		public function getClass() : string
		{
			return static::class;
		}

		/**
		 * @param array|false $attributes
		 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
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

			/** @var array<trait-string> $traits */
			$traits = class_uses_recursive($class);

			foreach ($traits as $trait)
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
			$type = parent::getCastType($key);

			foreach (static::$traitCasts[$this->getClass()] as $method)
			{
				/** @var string $custom */
				$custom = $this->{$method}();

				if (strncmp($type, $custom, strlen($custom)) === 0)
				{
					return $custom;
				}
			}

			return $type;
		}

		/**
		 * @template     V
		 * @param string $key
		 *
		 * @return mixed
		 * @psalm-return V
		 */
		public function getAttributeValue($key)
		{
			/** @psalm-var V $value */
			$value = parent::getAttributeValue($key);

			foreach (static::$traitAccessors[$this->getClass()] as $method)
			{
				if ($this->{$method}($key, $value))
				{
					return $value;
				}
			}

			return $value;
		}

		/**
		 * @template     V
		 * @param string $key
		 * @param mixed  $value
		 * @psalm-param  V $value
		 *
		 * @return mixed
		 */
		public function setAttribute($key, $value)
		{
			foreach (static::$traitMutators[$this->getClass()] as $method)
			{
				if ($this->{$method}($key, $value))
				{
					break;
				}
			}

			return parent::setAttribute($key, $value);
		}

		/**
		 * @return static
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public static function dummy() : self
		{
			try
			{
				return new static(false);
			}
			catch (MassAssignmentException $e)
			{
				throw new ShouldNotHappenException('Failed to instantiate dummy', $e);
			}
		}
	}
}