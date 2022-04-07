<?php

namespace app\core\form;

class TextareaField extends Field
{
    public function renderField(): string
    {
        return sprintf('
            <label for="%s">%s</label>
            <textarea
                class="form-control%s"
                name="%s"
                id="%s"
            >%s</textarea>
            ',
            $this->name,
            $this->model->getLabel($this->name),
            isset($this->model->errors[$this->name]) ? ' is-invalid' : '',
            $this->name,
            $this->name,
            $this->model->{$this->name}
        );
    }
}