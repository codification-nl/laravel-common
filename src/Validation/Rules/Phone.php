<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Enums\PhoneType;
	use Illuminate\Support\Arr;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Illuminate\Validation\Validator;

	class Phone implements ValidatorRule
	{
		/** @var string|null */
		protected $countryField;

		/** @var PhoneType */
		protected $type;

		/**
		 * @param string|null $country_field
		 */
		public function __construct(string $country_field = null)
		{
			$this->countryField = $country_field;
			$this->type         = PhoneType::BOTH();
		}

		/**
		 * @return $this
		 */
		public function mobile() : self
		{
			return $this->type(PhoneType::MOBILE());
		}

		/**
		 * @return $this
		 */
		public function fixed() : self
		{
			return $this->type(PhoneType::FIXED());
		}

		/**
		 * @param \Codification\Common\Enums\PhoneType $type
		 *
		 * @return $this
		 */
		public function type(PhoneType $type) : self
		{
			$this->type = $type;

			return $this;
		}

		/**
		 * @param string                           $attribute
		 * @param mixed                            $value
		 * @param string[]                         $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return bool
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool
		{
			[$country_field, $type] = $parameters;

			$key   = sanitize($country_field) ?: "{$attribute}_country";
			$data  = $validator->getData();
			$array = Arr::dot($data);

			if (!array_key_exists($key, $array))
			{
				return false;
			}

			$country = Arr::get($data, $array[$key], null);

			if ($country === null)
			{
				return false;
			}

			$type = PhoneType::make(intval($type));

			return \Codification\Common\Support\Phone::validate($value, $country, $type);
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return "phone:{$this->countryField},{$this->type}";
		}
	}
}