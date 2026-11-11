<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'string' => 'The :attribute field must be a string.',
    'integer' => 'The :attribute field must be an integer.',
    'max' => ['string' => 'The :attribute field must not be greater than :max characters.'],
    'between' => ['numeric' => 'The :attribute field must be between :min and :max.'],
    'alpha_dash' => 'The :attribute field may only contain letters, numbers, dashes, and underscores.',
    'unique' => 'The :attribute has already been taken.',
    'exists' => 'The selected :attribute is invalid.',
    'in' => 'The selected :attribute is invalid.',
    'attributes' => [
        'server_category_id' => 'category',
    ],
];
