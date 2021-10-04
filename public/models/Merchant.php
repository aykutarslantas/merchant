<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Messages\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class Merchant extends Model
{
    public function validation()
    {
        $validator = new Validation();

        if ($this->name == "") {
            $this->appendMessage(
                new Message("name is can not empty")
            );
        }

        if ($this->password == "") {
            $this->appendMessage(
                new Message("name is can not empty")
            );
        }

        // Validate the validator
        return $this->validate($validator);
    }
}