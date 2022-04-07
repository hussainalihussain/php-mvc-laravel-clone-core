<?php

namespace hussainalihussain\phpmvclaravelclonecore;

use hussainalihussain\phpmvclaravelclonecore\database\QueryBuilder;

abstract class Model extends QueryBuilder
{
    const RULE_REQUIRED = 'required';
    const RULE_MIN      = 'min';
    const RULE_MAX      = 'max';
    const RULE_MATCH    = 'match';
    const RULE_EMAIL    = 'email';
    const RULE_UNIQUE   = 'unique';
    public $errors      = [];


    abstract public function rules(): array;
    abstract public function labels(): array;

    public function load($data)
    {
        foreach ($data as $variable=> $value)
        {
            if(property_exists($this, $variable))
            {
                $this->{$variable} = $value;
            }
        }
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute=> $rules)
        {
            $value = $this->{$attribute};

            foreach ($rules as $rule)
            {
                $ruleName = $rule;

                if(is_array($ruleName))
                {
                    $ruleName = $ruleName[0];
                }

                if($ruleName === self::RULE_REQUIRED && !$value)
                {
                    $this->addRuleError($attribute, self::RULE_REQUIRED);
                }

                if($ruleName === self::RULE_MIN && strlen($value) < $rule[self::RULE_MIN])
                {
                    $this->addRuleError($attribute, self::RULE_MIN, $rule);
                }

                if($ruleName === self::RULE_MAX && strlen($value) > $rule[self::RULE_MAX])
                {
                    $this->addRuleError($attribute, self::RULE_MAX, $rule);
                }

                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL))
                {
                    $this->addRuleError($attribute, self::RULE_EMAIL);
                }

                if($ruleName === self::RULE_MATCH && $value != $this->{$rule[self::RULE_MATCH]})
                {
                    $this->addRuleError($attribute, self::RULE_MATCH, $rule);
                }

                if ($ruleName === self::RULE_UNIQUE)
                {
                    $table      = static::table();
                    $primaryKey = static::primaryKey();
                    $whereField = $attribute;
                    $sql        = "SELECT {$primaryKey} FROM {$table} WHERE {$whereField}=:value LIMIT 1";

                    try {
                        $statement = self::prepare($sql);
                        $statement->bindValue(':value', $value);
                        $statement->execute();

                        if($statement->rowCount() > 0)
                        {
                            $this->addRuleError($attribute, self::RULE_UNIQUE);
                        }
                    }
                    catch (\PDOException $e) {
                        echo '<pre>';
                        var_dump($e);
                        echo '</pre>';
                        exit;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function addRuleError(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule];

        foreach ($params as $key=> $param)
        {
            $message = str_replace("{{$key}}", $param, $message);
        }

        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED=> 'This field is required!',
            self::RULE_MIN     => 'This field must be at least {min} characters!',
            self::RULE_MAX     => 'This field should not exceed {max} characters!',
            self::RULE_MATCH   => 'This field must be same as {match} field!',
            self::RULE_EMAIL   => 'Email is incorrect!',
            self::RULE_UNIQUE  => 'This field must be unique!',
        ];
    }

    public function getFirstError(string $attribute)
    {
        return $this->errors[$attribute][0] ?? '';
    }

    public function getLabel(string $attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }
}