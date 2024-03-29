<?php

namespace Codification\Common\Database\Schema
{
	use Codification\Common\Database\Eloquent\HasToken;
	use Codification\Common\Support\Exceptions;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Schema\ColumnDefinition;
	use Illuminate\Support\Fluent;

	/**
	 * @template       T of \Illuminate\Database\Eloquent\Model
	 * @psalm-suppress PropertyNotSetInConstructor
	 */
	class Blueprint extends \Illuminate\Database\Schema\Blueprint
	{
		/**
		 * @var string|\Illuminate\Database\Eloquent\Model|null
		 * @psalm-var class-string<T>|null
		 */
		protected $model = null;

		/**
		 * @var \Illuminate\Database\Eloquent\Model|null
		 * @psalm-var T|null
		 */
		protected $instance = null;

		/**
		 * @param string                                          $table
		 * @param \Closure|null                                   $callback = null
		 * @psalm-param null|\Closure(\Codification\Common\Database\Schema\Blueprint):void $callback = null
		 * @param string                                          $prefix   = ''
		 * @param string|\Illuminate\Database\Eloquent\Model|null $model    = null
		 * @psalm-param class-string<T>|null                      $model    = null
		 * @param \Illuminate\Database\Eloquent\Model|null        $instance = null
		 * @psalm-param T|null                                    $instance = null
		 */
		public function __construct(string $table, \Closure $callback = null, string $prefix = '', string $model = null, Model $instance = null)
		{
			$this->model    = $model;
			$this->instance = $instance;

			parent::__construct($table, $callback, $prefix);
		}

		/**
		 * @return \Illuminate\Database\Eloquent\Model
		 * @psalm-return T
		 * @throws \Codification\Common\Support\Exceptions\ReferenceException
		 */
		private function ensureInstance() : Model
		{
			if ($this->instance === null)
			{
				throw new Exceptions\ReferenceException('$this->instance');
			}

			return $this->instance;
		}

		/**
		 * @param string|null $column = null
		 * @param int|null    $length = 60
		 *
		 * @return \Illuminate\Database\Schema\ColumnDefinition
		 * @throws \Codification\Common\Support\Exceptions\ReferenceException
		 * @throws \DomainException
		 */
		public function token(string $column = null, int $length = null) : ColumnDefinition
		{
			if ($column === null)
			{
				/** @var \Codification\Common\Database\Eloquent\Contracts\Tokenable $tokenable */
				$tokenable = $this->ensureInstance();

				if (!in_array(HasToken::class, class_uses_recursive($tokenable)))
				{
					throw new \DomainException("[{$this->model}] is not tokenable");
				}

				$column = $tokenable->getTokenKey();

				if ($length === null)
				{
					$length = $tokenable->getTokenLength();
				}
			}

			return $this->string($column, $length);
		}

		/**
		 * @param int $precision
		 *
		 * @return void
		 */
		public function timestamps($precision = 0)
		{
			/** @var string|null $created_at */
			$created_at = ($this->model !== null) ? $this->model::CREATED_AT : 'created_at';
			/** @var string|null $updated_at */
			$updated_at = ($this->model !== null) ? $this->model::UPDATED_AT : 'updated_at';

			if ($created_at !== null)
			{
				$this->timestamp($created_at, $precision)->nullable();
			}

			if ($updated_at !== null)
			{
				$this->timestamp($updated_at, $precision)->nullable();
			}
		}

		/**
		 * @param string|\Illuminate\Database\Eloquent\Model        $relation
		 * @psalm-param class-string<\Illuminate\Database\Eloquent\Model> $relation
		 * @param string|null                                       $column      = null
		 * @param array<string|\Illuminate\Database\Eloquent\Model> $constraints = []
		 * @psalm-param array<class-string<\Illuminate\Database\Eloquent\Model>> $constraints = []
		 *
		 * @return \Illuminate\Database\Schema\ColumnDefinition
		 */
		public function belongsTo(string $relation, string $column = null, array $constraints = []) : ColumnDefinition
		{
			/** @var \Illuminate\Database\Eloquent\Model $instance */
			$instance = new $relation();

			if ($column === null)
			{
				$column = $instance->getForeignKey();
			}

			$constraints = array_map(function (string $constraint) : string
				{
					/** @var \Illuminate\Database\Eloquent\Model $instance */
					$instance = new $constraint();

					return $instance->getForeignKey();
				}, $constraints);

			$this->index(array_merge([$column], $constraints));

			$this->foreign(array_merge([$column], $constraints))
			     ->references(array_merge([$instance->getKeyName()], $constraints))
			     ->on($instance->getTable());

			return $this->unsignedInteger($column);
		}

		/**
		 * @param string|\Illuminate\Database\Eloquent\Model $relation
		 * @psalm-param class-string<\Illuminate\Database\Eloquent\Model> $relation
		 *
		 * @return \Illuminate\Support\Fluent
		 */
		public function dropBelongsTo(string $relation) : Fluent
		{
			/** @var \Illuminate\Database\Eloquent\Model $instance */
			$instance = new $relation();

			return $this->dropColumn($instance->getForeignKey());
		}

		/**
		 * @param string|null $column
		 *
		 * @return \Illuminate\Database\Schema\ColumnDefinition
		 * @throws \UnexpectedValueException
		 */
		public function increments($column = null)
		{
			if ($column === null)
			{
				$column = $this->ensureInstance()->getKeyName();
			}

			return $this->unsignedInteger($column, true);
		}

		/**
		 * @param string|null $column
		 *
		 * @return \Illuminate\Database\Schema\ColumnDefinition
		 * @throws \UnexpectedValueException
		 */
		public function bigIncrements($column = null)
		{
			if ($column === null)
			{
				$column = $this->ensureInstance()->getKeyName();
			}

			return $this->unsignedBigInteger($column, true);
		}
	}
}