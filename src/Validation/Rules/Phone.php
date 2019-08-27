<?php

namespace Codification\Common\Validation\Rules
{
	use Codification\Common\Phone\PhoneType;
	use Codification\Common\Validation\Contracts\ValidatorRule;
	use Codification\Common\Validation\Contracts\ValidatorRuleReplacer;
	use Illuminate\Validation\Validator;

	class Phone implements ValidatorRule, ValidatorRuleReplacer
	{
		/** @var string|null */
		protected $countryField;

		/** @var PhoneType */
		protected $type;

		/**
		 * @param string|null $country_field = null
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
		 * @param \Codification\Common\Phone\PhoneType $type
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

			$region_code = data_get($target, $key, null);
			$region_code = sanitize($region_code);

			if ($region_code === null)
			{
				return false;
			}

			$type = intval($type);
			$type = PhoneType::make($type);

			return \Codification\Common\Phone\Phone::validate($value, $region_code, $type);
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

			$region_code = data_get($target, $key, null);
			$region_code = sanitize($region_code);

			/** @var string $string */
			$string = str_replace(':country', $region_code, $message);

			return $string;
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