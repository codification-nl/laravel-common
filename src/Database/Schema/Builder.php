<?php

namespace Codification\Common\Database\Schema
{
	use Illuminate\Database\Connection;
	use Illuminate\Database\Eloquent\Model;

	class Builder extends \Illuminate\Database\Schema\Builder
	{
		/** @var string */
		protected $table;

		/** @var \Illuminate\Database\Eloquent\Model|null */
		protected $model = null;

		/** @var \Illuminate\Database\Eloquent\Model|null */
		protected $instance = null;

		/**
		 * @param \Illuminate\Database\Connection                 $connection
		 * @param string                                          $table
		 * @param \Illuminate\Database\Eloquent\Model|string|null $model    = null
		 * @param \Illuminate\Database\Eloquent\Model|null        $instance = null
		 */
		public function __construct(Connection $connection, string $table, string $model = null, Model $instance = null)
		{
			$this->table    = $table;
			$this->model    = $model;
			$this->instance = $instance;

			parent::__construct($connection);
		}

		/**
		 * @param string|null $table
		 * @param \Closure    $callback
		 *
		 * @return void
		 */
		public function table($table, \Closure $callback)
		{
			$this->build($this->createBlueprint($table, $callback));
		}

		/**
		 * @param string|null $table
		 * @param \Closure    $callback
		 *
		 * @return void
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
		 *
		 * @return void
		 */
		public function modifyTable(\Closure $callback)
		{
			$this->build($this->createBlueprint($this->table, $callback));
		}

		/**
		 * @param \Closure $callback
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
			$this->table($table ?: $this->table, function (Blueprint $table) : void
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
			$this->table($table ?: $this->table, function (Blueprint $table) : void
				{
					$table->drop();
				});
		}

		/**
		 * @param string        $table
		 * @param \Closure|null $callback
		 *
		 * @return \Codification\Common\Database\Schema\Blueprint
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
				return $this->connection->getConfig('prefix');
			}

			return '';
		}
	}
}