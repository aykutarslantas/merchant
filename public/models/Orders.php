<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Messages\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class Orders extends Model
{
    public function newOrdered()
    {
        $validator = new Validation();

        $validator->add(
            "orderCode",
            new PresenceOf(
                [
                    'message' => 'The order code is required',
                ]
            )
        );

        $validator->add(
            "product_id",
            new PresenceOf(
                [
                    'message' => 'The product id is required',
                ]
            )
        );

        $validator->add(
            "quantity",
            new PresenceOf(
                [
                    'message' => 'The quantity is required',
                ]
            )
        );

        $validator->add(
            "adress",
            new PresenceOf(
                [
                    'message' => 'The adress is required',
                ]
            )
        );

        // Validate the validator
        return $this->validate($validator);
    }
}