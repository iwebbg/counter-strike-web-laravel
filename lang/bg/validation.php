<?php

return [
    'required' => 'Полето :attribute е задължително.',
    'email' => 'Полето :attribute трябва да съдържа валиден имейл адрес.',
    'string' => 'Полето :attribute трябва да бъде текст.',
    'integer' => 'Полето :attribute трябва да бъде цяло число.',
    'max' => ['string' => 'Полето :attribute не може да бъде по-дълго от :max знака.'],
    'between' => ['numeric' => 'Полето :attribute трябва да бъде между :min и :max.'],
    'alpha_dash' => 'Полето :attribute може да съдържа само букви, цифри, тирета и долни черти.',
    'unique' => 'Тази стойност за :attribute вече се използва.',
    'exists' => 'Избраната стойност за :attribute е невалидна.',
    'in' => 'Избраната стойност за :attribute е невалидна.',
    'attributes' => [
        'name' => 'име', 'slug' => 'slug', 'email' => 'имейл', 'password' => 'парола',
        'server_category_id' => 'категория', 'game' => 'игра', 'protocol' => 'протокол',
        'host' => 'host', 'port' => 'port', 'description' => 'описание', 'location' => 'локация',
    ],
];
