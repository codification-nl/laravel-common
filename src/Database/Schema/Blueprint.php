<?php

namespace Codification\Common\Database\Schema
{
	use Codification\Common\Database\Eloquent\Concerns\HasToken;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Schema\ColumnDefinition;
	use Illuminate\Support\Fluent;

	class Blueprint extends \Illuminate\Database\Schema\Blueprint
	{
		/** @var \Illuminate\Database\Eloquent\Model|null */
		protected $model = null;

		/** @var \Illuminate\Database\Eloquent\Model|null */
		protected $instance = null;

		/**
		 * @param string                                          $table
		 * @param \Closure|null                                   $callback
		 * @param string                                          $prefix
		 * @param \Illuminate\Database\Eloquent\Model|string|null $model
		 * @param \Illuminate\Database\Eloquent\Model|null        $instance
		 */
		public function __construct(string $table, \Closure $callback = null, string $prefix = '', string $model = null, Model $instance = null)
		{
			$this->model    = $model;
			$this->instance = $instance;

			parent::__construct($table, $callback, $prefix);
		}

		/**
		 * @return \Illuminate\Database\Eloquent\Model
		 */
		private function ensureInstance() : Model
		{
			if ($this->instance === null)
			{
				throw new \UnexpectedValueException('$this->instance === null');
			}

			return $this->instance;
		}

		/**
		 * @param string|null $column
		 * @param int         $length
		 *
		 * @return \Illuminate\Database\Schema\ColumnDefinition
		 */
		public function token(string $column = null, int $length = 60) : ColumnDefinition
		{
			if ($column === null)
			{
				/** @var \Codification\Common\Database\Eloquent\Contracts\Tokenable $tokenable */
				$tokenable = $this->ensureInstance();

				if (!in_array(HasToken::class, class_uses_recursive($tokenable)))
				{
					throw new \RuntimeException("[{$this->model}] is not tokenable");
				}

				$column = $tokenable->getTokenKey();
				$length = $tokenable->getTokenLength();
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
			$model = $this->model;

			$created_at = ($model !== null) ? $model::CREATED_AT : 'created_at';
			$updated_at = ($model !== null) ? $model::UPDATED_AT : 'updated_at';

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
		 * @param string|\Illuminate\Database\Eloquent\Model     $relation
		 * @param string|null                                    $column
		 * @param string[]|\Illuminate\Database\Eloquent\Model[] $constraints
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