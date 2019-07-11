<?php

namespace Codification\Common\Database\Eloquent\Scopes
{
	use Codification\Common\Enums\Concerns\EnumFlags;
	use Codification\Common\Enums\Enum;
	use Illuminate\Database\Eloquent\Scope;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Str;

	class EnumScope implements Scope
	{
		/**
		 * @param \Illuminate\Database\Eloquent\Builder                                                         $builder
		 * @param \Illuminate\Database\Eloquent\Model|\Codification\Common\Database\Eloquent\Contracts\HasEnums $model
		 *
		 * @return void
		 */
		public function apply(Builder $builder, Model $model)
		{
			foreach ($model->getEnums() as $column => $type)
			{
				if (!in_array(EnumFlags::class, class_uses_recursive($type)))
				{
					continue;
				}

				$builder = Enum::select($builder, $column);
			}
		}

		/**
		 * @param \Illuminate\Database\Eloquent\Builder $builder
		 *
		 * @return void
		 */
		public function extend(Builder $builder) : void
		{
			/** @var \Codification\Common\Database\Eloquent\Contracts\HasEnums $model */
			$model = $builder->getModel();

			foreach ($model->getEnums() as $column => $type)
			{
				if (!in_array(EnumFlags::class, class_uses_recursive($type)))
				{
					continue;
				}

				$name = 'where' . Str::studly($column) . 'Has';

				$builder->macro($name, function (Builder $builder, $value, string $boolean = 'and') use ($column)
					{
						return Enum::where($builder, $column, $value, $boolean);
					});
			}
		}
	}
}