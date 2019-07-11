<?php

namespace Codification\Common\Database\Migrations
{
	use Codification\Common\Database\Schema\Builder;

	abstract class Migration extends \Illuminate\Database\Migrations\Migration
	{
		/** @var string */
		protected $table = null;

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
				$this->instance = new $this->model();
				$this->table    = $this->instance->getTable();
			}

			if ($this->table === null)
			{
				throw new \UnexpectedValueException('$this->table === null');
			}

			$this->schema = new Builder($this->table, $this->model, $this->instance);
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