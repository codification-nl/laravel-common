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
		 *
		 * @return $this
		 */
		public static function make(string $country_field = null) : self
		{
			$rule = new static();

			$rule->countryField = $country_field;
			$rule->type         = PhoneType::BOTH();

			return $rule;
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
		 * @throws \Codification\Common\Exceptions\LocaleException
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool
		{
			[$country_field, $type] = $parameters;

			$key   = sanitize($country_field) ?: "{$attribute}_country";
			$array = $validator->getData();

			$country = Arr::get($array, $key, null);

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