<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Enums\PhoneType;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Codification\Common\Validation\Contracts\ValidatorRuleReplacer;
	use Illuminate\Validation\Validator;
	use Codification\Common\Support\Country;

	class Phone implements ValidatorRule, ValidatorRuleReplacer
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
		 */
		public function validate(string $attribute, $value, array $parameters, Validator $validator) : bool
		{
			[$country_field, $type] = $parameters;

			$target = $validator->getData();
			$key    = sanitize($country_field) ?: "{$attribute}_country";

			$country = data_get($target, $key, null);
			$country = sanitize($country);

			if ($country === null || !Country::isValid($country))
			{
				return false;
			}

			$type = intval($type);
			$type = PhoneType::make($type);

			return \Codification\Common\Support\Phone::validate($value, $country, $type);
		}

		/**
		 * @param string                           $message
		 * @param string                           $attribute
		 * @param string                           $rule
		 * @param string[]                         $parameters
		 * @param \Illuminate\Validation\Validator $validator
		 *
		 * @return string
		 */
		public function replace(string $message, string $attribute, string $rule, array $parameters, Validator $validator) : string
		{
			[$country_field] = $parameters;

			$target = $validator->getData();
			$key    = sanitize($country_field) ?: "{$attribute}_country";

			$country = data_get($target, $key, null);
			$country = sanitize($country);

			return str_replace(':country', $country, $message);
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