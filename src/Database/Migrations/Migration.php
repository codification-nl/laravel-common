<?php

namespace Codification\Common\Database\Migrations
{
	use Codification\Common\Database\Schema\Builder;
	use Codification\Common\Support\ContainerUtils;
	use Codification\Common\Support\Exceptions;

	/**
	 * @template T of \Illuminate\Database\Eloquent\Model
	 */
	abstract class Migration extends \Illuminate\Database\Migrations\Migration
	{
		/** @var string|null */
		protected $table = null;

		/** @var string|null */
		protected $connection = null;

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

		/** @var \Codification\Common\Database\Schema\Builder */
		protected $schema;

		/**
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Support\Exceptions\ReferenceException
		 */
		public function __construct()
		{
			if ($this->model !== null)
			{
				$this->instance   = new $this->model();
				$this->table      = $this->instance->getTable();
				$this->connection = $this->instance->getConnectionName();
			}

			if ($this->table === null)
			{
				throw new Exceptions\ReferenceException('$this->table');
			}

			if ($this->connection === null)
			{
				throw new Exceptions\ReferenceException('$this->connection');
			}

			try
			{
				/** @var \Illuminate\Database\DatabaseManager $db */
				$db = ContainerUtils::resolve('db');
			}
			catch (Exceptions\ResolutionException $e)
			{
				throw new Exceptions\ShouldNotHappenException('Failed to resolve [db]', $e->getPrevious());
			}

			$connection = $db->connection($this->connection);

			$this->schema = new Builder($connection, $this->table, $this->model, $this->instance);
		}

		/**
		 * @return void
		 */
		public abstract function up();

		/**
		 * @return void
		 */
		public abstract function down();
	}
}