<?php

namespace hussainalihussain\phpmvclaravelclonecore\form;

use hussainalihussain\phpmvclaravelclonecore\Model;

abstract class Field
{
    public $model;
    public $name;

    abstract public function renderField(): string;

    public function __construct(string $name, Model $model)
    {
        $this->name  = $name;
        $this->model = $model;
    }

    public function __toString()
    {
        return sprintf('
            <div class="mb-3">
                <label for="%s" class="form-label">%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>
            ',
            $this->name,
            $this->model->getLabel($this->name),
            $this->renderField(),
            $this->model->getFirstError($this->name) ?? ''
        );
    }
}