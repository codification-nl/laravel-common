<?php

namespace Codification\Common\Support
{
	use Illuminate\Contracts\Support\Arrayable;
	use Illuminate\Contracts\Support\Jsonable;

	/**
	 * @template            TKey of array-key
	 * @template            TValue
	 * @template-implements \ArrayAccess<TKey, TValue>
	 * @template-implements \IteratorAggregate<TKey, TValue>
	 */
	abstract class Dictionary implements \ArrayAccess, Arrayable, \Countable, Jsonable, \IteratorAggregate, \JsonSerializable
	{
		/**
		 * @var array
		 * @psalm-var array<TKey, TValue>
		 */
		protected $items = [];

		/**
		 * @param array $items
		 * @psalm-param array<TKey, TValue> $items
		 */
		protected function __construct(array $items)
		{
			$this->items = $items;
		}

		/**
		 * @return array
		 * @psalm-return array<TKey, TValue>
		 */
		public function all() : array
		{
			return $this->items;
		}

		/**
		 * @param mixed $key
		 * @psalm-param TKey $key
		 *
		 * @return bool
		 */
		public function has($key) : bool
		{
			return array_key_exists($key, $this->items);
		}

		/**
		 * @param mixed $key
		 * @psalm-param TKey $key
		 */
		public function remove($key) : void
		{
			unset($this->items[$key]);
		}

		/**
		 * @param array $items
		 * @psalm-param array<TKey, TValue> $items
		 */
		public function add(array $items) : void
		{
			$this->items = array_replace($this->items, $items);
		}

		/**
		 * @template     V
		 * @param mixed $key
		 * @psalm-param  TKey $key
		 * @param mixed $default = null
		 * @psalm-param  V|null $default = null
		 *
		 * @return mixed|null
		 * @psalm-return TValue|V|null
		 */
		public function get($key, $default = null)
		{
			return $this->items[$key] ?? $default;
		}

		/**
		 * @param mixed $key
		 * @psalm-param TKey $key
		 * @param mixed $value
		 * @psalm-param TValue $value
		 */
		public function set($key, $value) : void
		{
			$this->items[$key] = $value;
		}

		/**
		 * @return mixed[]
		 * @psalm-return list<TKey>
		 */
		public function keys() : array
		{
			return array_keys($this->items);
		}

		/**
		 * @return mixed[]
		 * @psalm-return list<TValue>
		 */
		public function values() : array
		{
			return array_values($this->items);
		}

		/**
		 * @param mixed $key
		 * @psalm-param TKey $key
		 *
		 * @return bool
		 */
		public function offsetExists($key) : bool
		{
			return $this->has($key);
		}

		/**
		 * @param mixed $key
		 * @psalm-param  TKey $key
		 *
		 * @return mixed|null
		 */
		public function offsetGet($key)
		{
			return $this->get($key);
		}

		/**
		 * @param mixed $key
		 * @psalm-param TKey $key
		 * @param mixed $value
		 * @psalm-param TValue $value
		 *
		 * @return void
		 */
		public function offsetSet($key, $value) : void
		{
			$this->set($key, $value);
		}

		/**
		 * @param mixed $key
		 * @psalm-param TKey $key
		 *
		 * @return void
		 */
		public function offsetUnset($key) : void
		{
			$this->remove($key);
		}

		/**
		 * @return array
		 * @psalm-return array<TKey, TValue>
		 */
		public function jsonSerialize() : array
		{
			return $this->toArray();
		}

		/**
		 * @return array
		 * @psalm-return array<TKey, TValue>
		 */
		public function toArray() : array
		{
			return $this->items;
		}

		/**
		 * @return int
		 */
		public function count() : int
		{
			return count($this->items);
		}

		/**
		 * @return \ArrayIterator
		 * @psalm-return \ArrayIterator<TKey, TValue>
		 */
		public function getIterator() : \ArrayIterator
		{
			return new \ArrayIterator($this->items);
		}

		/**
		 * @param int $options = 0
		 *
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function toJson($options = 0) : string
		{
			$json = json_encode($this->jsonSerialize(), $options);

			if ($json === false)
			{
				throw new Exceptions\ShouldNotHappenException('Failed to get JSON representation');
			}

			return $json;
		}
	}
}