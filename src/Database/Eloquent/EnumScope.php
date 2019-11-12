<?php

namespace Codification\Common\Database\Eloquent
{
	use Codification\Common\Enum;
	use Illuminate\Database\Eloquent;
	use Illuminate\Support\Str;

	class EnumScope implements Eloquent\Scope
	{
		/**
		 * @param \Illuminate\Database\Eloquent\Builder $builder
		 * @param \Illuminate\Database\Eloquent\Model   $model
		 *
		 * @return void
		 * @throws \InvalidArgumentException
		 * @throws \RuntimeException
		 */
		public function apply(Eloquent\Builder $builder, Eloquent\Model $model)
		{
			/** @var \Codification\Common\Database\Eloquent\Contracts\HasEnums $model */
			foreach ($model->getEnums() as $column => $type)
			{
				$traits = class_uses_recursive($type);

				if (!in_array(Enum\EnumFlags::class, $traits))
				{
					continue;
				}

				$builder = Enum\Enum::select($builder, $column);
			}
		}

		/**
		 * @param \Illuminate\Database\Eloquent\Builder $builder
		 *
		 * @return void
		 */
		public function extend(Eloquent\Builder $builder) : void
		{
			/** @var \Codification\Common\Database\Eloquent\Contracts\HasEnums $model */
			$model = $builder->getModel();

			foreach ($model->getEnums() as $column => $type)
			{
				/** @var array<trait-string> $traits */
				$traits = class_uses_recursive($type);

				if (!in_array(Enum\EnumFlags::class, $traits))
				{
					continue;
				}

				$name = 'where' . Str::studly($column) . 'Has';

				/** @psalm-suppress MissingClosureParamType */
				$builder->macro($name, function (Eloquent\Builder $builder, $value, string $boolean = 'and') use ($column)
					{
						/**
						 * @psalm-var      Codification\Common\Enum\Enum<array-key>|array-key $value
						 * @psalm-suppress PossiblyInvalidArgument
						 */

						return Enum\Enum::where($builder, $column, $value, $boolean);
					});
			}
		}
	}
}