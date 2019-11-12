<?php

namespace Codification\Common\Support
{
	use Illuminate\Contracts\Pagination\LengthAwarePaginator;
	use Illuminate\Pagination\Paginator;

	final class CollectionUtils
	{
		/**
		 * @return \Closure
		 * @psalm-return \Closure(int=,list<string>=,string=,int=):\Illuminate\Contracts\Pagination\LengthAwarePaginator
		 */
		public function paginate() : \Closure
		{
			return function (int $per_page = null, array $columns = ['*'], string $page_name = 'page', int $page = null) : LengthAwarePaginator
				{
					/** @var \Illuminate\Support\Collection $self */
					$self = $this;

					/** @var object $first */
					$first = $self->first();

					if ($per_page === null)
					{
						/** @var int $per_page */
						$per_page = method_exists($first, 'getPerPage') ? $first->getPerPage() : 15;
					}

					if ($page === null)
					{
						$page = Paginator::resolveCurrentPage($page_name);
					}

					$results = $self->forPage($page, $per_page);
					$total   = $self->count();

					if (!in_array('*', $columns, true))
					{
						$results = $results->only($columns);
					}

					return new \Illuminate\Pagination\LengthAwarePaginator($results, $total, $per_page, $page, [
						'path'     => Paginator::resolveCurrentPath(),
						'pageName' => $page_name,
					]);
				};
		}
	}
}