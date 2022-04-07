<?php

namespace app\core\form;

use app\core\Model;

class InputField extends Field
{
    const TYPE_TEXT     = 'text';
    const TYPE_NUMBER   = 'number';
    const TYPE_EMAIL    = 'email';
    const TYPE_PASSWORD = 'password';
    public $type;

    public function renderField(): string
    {
        return sprintf('
            <input
                type="%s"
                value="%s"
                class="form-control%s"
                id="%s"
                name="%s"
                aria-describedby="%s"
                placeholder="%s"
            />',
            $this->type,
            $this->model->{$this->name},
            isset($this->model->errors[$this->name]) ? ' is-invalid' : '',
            $this->name,
            $this->name,
            $this->model->getLabel($this->name),
            $this->model->getLabel($this->name),
        );
    }

    public function __construct(string $name, Model $model)
    {
        parent::__construct($name, $model);
        $this->type = self::TYPE_TEXT;
    }

    public function password(): Field
    {
        $this->type = self::TYPE_PASSWORD;

        return $this;
    }

    public function email(): Field
    {
        $this->type = self::TYPE_EMAIL;

        return $this;
    }

    public function number(): Field
    {
        $this->type = self::TYPE_NUMBER;

        return $this;
    }
}