<?php

namespace Codification\Common\Support
{
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Pagination\Paginator;

	/**
	 * @mixin \Illuminate\Support\Collection
	 */
	final class CollectionUtils
	{
		public function paginate()
		{
			return function (int $per_page = null, array $columns = ['*'], string $page_name = 'page', int $page = null) : \Illuminate\Contracts\Pagination\LengthAwarePaginator
				{
					$first = $this->first();

					if ($per_page === null)
					{
						$per_page = method_exists($first, 'getPerPage') ? $first->getPerPage() : 15;
					}

					if ($page === null)
					{
						$page = Paginator::resolveCurrentPage($page_name);
					}

					$results = $this->forPage($page, $per_page);
					$total   = $this->count();

					if (!in_array('*', $columns, true))
					{
						$results = $results->only($columns);
					}

					return new LengthAwarePaginator($results, $total, $per_page, $page, [
						'path'     => Paginator::resolveCurrentPath(),
						'pageName' => $page_name,
					]);
				};
		}
	}
}