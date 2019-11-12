<?php

namespace Codification\Common\Database\Schema
{
	use Illuminate\Database\Connection;
	use Illuminate\Database\Eloquent\Model;

	/**
	 * @template       T of \Illuminate\Database\Eloquent\Model
	 * @psalm-suppress PropertyNotSetInConstructor
	 */
	class Builder extends \Illuminate\Database\Schema\Builder
	{
		/** @var string */
		protected $table;

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
		 * @param \Illuminate\Database\Connection                 $connection
		 * @param string                                          $table
		 * @param string|\Illuminate\Database\Eloquent\Model|null $model    = null
		 * @psalm-param class-string<T>|null                      $model    = null
		 * @param \Illuminate\Database\Eloquent\Model|null        $instance = null
		 * @psalm-param T|null                                    $instance = null
		 */
		public function __construct(Connection $connection, string $table, string $model = null, Model $instance = null)
		{
			$this->table    = $table;
			$this->model    = $model;
			$this->instance = $instance;

			parent::__construct($connection);
		}

		/**
		 * @param string   $table
		 * @param \Closure $callback
		 * @psalm-param \Closure(\Codification\Common\Database\Schema\Blueprint):void $callback
		 *
		 * @return void
		 * @psalm-suppress MoreSpecificImplementedParamType
		 */
		public function table($table, \Closure $callback)
		{
			$this->build($this->createBlueprint($table, $callback));
		}

		/**
		 * @param string   $table
		 * @param \Closure $callback
		 * @psalm-param \Closure(\Codification\Common\Database\Schema\Blueprint):void $callback
		 *
		 * @return void
		 * @psalm-suppress MoreSpecificImplementedParamType
		 */
		public function create($table, \Closure $callback)
		{
			$this->table($table, function (Blueprint $table) use ($callback) : void
				{
					$table->create();

					$callback($table);
				});
		}

		/**
		 * @param \Closure $callback
		 * @psalm-param \Closure(\Codification\Common\Database\Schema\Blueprint):void $callback
		 *
		 * @return void
		 */
		public function modifyTable(\Closure $callback)
		{
			$this->build($this->createBlueprint($this->table, $callback));
		}

		/**
		 * @param \Closure $callback
		 * @psalm-param \Closure(\Codification\Common\Database\Schema\Blueprint):void $callback
		 *
		 * @return void
		 */
		public function createTable(\Closure $callback)
		{
			$this->modifyTable(function (Blueprint $table) use ($callback) : void
				{
					$table->create();

					if ($this->instance !== null && $this->instance->getIncrementing())
					{
						$table->increments();
					}

					$callback($table);

					if ($this->instance !== null && $this->instance->usesTimestamps())
					{
						$table->timestamps();
					}
				});
		}

		/**
		 * @param string|null $table
		 *
		 * @return void
		 */
		public function dropIfExists($table = null)
		{
			$this->table($table ?? $this->table, function (Blueprint $table) : void
				{
					$table->dropIfExists();
				});
		}

		/**
		 * @param string|null $table
		 *
		 * @return void
		 */
		public function drop($table = null)
		{
			$this->table($table ?? $this->table, function (Blueprint $table) : void
				{
					$table->drop();
				});
		}

		/**
		 * @param string        $table
		 * @param \Closure|null $callback = null
		 * @psalm-param    null|\Closure(\Codification\Common\Database\Schema\Blueprint):void $callback = null
		 *
		 * @return \Codification\Common\Database\Schema\Blueprint
		 * @psalm-suppress MoreSpecificImplementedParamType
		 */
		public function createBlueprint($table, \Closure $callback = null)
		{
			return new Blueprint($table, $callback, $this->getPrefix(), $this->model, $this->instance);
		}

		/**
		 * @return string
		 */
		private function getPrefix() : string
		{
			if ($this->connection->getConfig('prefix_indexes'))
			{
				/** @var string $prefix */
				$prefix = $this->connection->getConfig('prefix');

				return $prefix;
			}

			return '';
		}
	}
}