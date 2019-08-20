<?php

namespace Codification\Common\Database\Migrations
{
	use Codification\Common\Database\Schema\Builder;
	use Codification\Common\Support\ContainerUtils;

	abstract class Migration extends \Illuminate\Database\Migrations\Migration
	{
		/** @var string */
		protected $table = null;

		/** @var string */
		protected $connection = null;

		/** @var \Illuminate\Database\Eloquent\Model|null */
		protected $model = null;

		/** @var \Illuminate\Database\Eloquent\Model|null */
		protected $instance = null;

		/** @var \Codification\Common\Database\Schema\Builder */
		protected $schema;

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
				throw new \UnexpectedValueException('$this->table === null');
			}

			if ($this->connection === null)
			{
				throw new \UnexpectedValueException('$this->connection === null');
			}

			/** @var \Illuminate\Database\DatabaseManager $db */
			$db         = ContainerUtils::resolve('db');
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