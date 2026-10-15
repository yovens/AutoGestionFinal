<?php

// Mesaj validasyon an fransè (sèlman règ aplikasyon an itilize yo + kèk règ kouran).
return [
    'accepted'         => 'Le champ :attribute doit être accepté.',
    'boolean'          => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed'        => 'La confirmation du :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date'             => 'Le champ :attribute n’est pas une date valide.',
    'email'            => 'Le champ :attribute doit être une adresse e-mail valide.',
    'exists'           => 'Le :attribute sélectionné est introuvable.',
    'file'             => 'Le champ :attribute doit être un fichier.',
    'image'            => 'Le champ :attribute doit être une image.',
    'in'               => 'La valeur choisie pour :attribute n’est pas valide.',
    'integer'          => 'Le champ :attribute doit être un nombre entier.',
    'max'              => [
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'file'    => 'Le fichier :attribute ne peut pas dépasser :max Ko.',
        'string'  => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'array'   => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],
    'mimes'            => 'Le fichier :attribute doit être de type : :values.',
    'min'              => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'file'    => 'Le fichier :attribute doit faire au moins :min Ko.',
        'string'  => 'Le champ :attribute doit contenir au moins :min caractères.',
        'array'   => 'Le champ :attribute doit contenir au moins :min éléments.',
    ],
    'numeric'          => 'Le champ :attribute doit être un nombre.',
    'required'         => 'Le champ :attribute est obligatoire.',
    'string'           => 'Le champ :attribute doit être un texte.',
    'unique'           => 'Ce :attribute est déjà utilisé.',
    'uploaded'         => 'Le fichier :attribute n’a pas pu être envoyé.',

    'custom' => [
        'plate' => ['unique' => 'Cette plaque est déjà enregistrée sur un autre véhicule.'],
        'email' => ['unique' => 'Cet e-mail est déjà utilisé par un autre compte.'],
    ],

    'attributes' => [
        'nom'                   => 'nom',
        'email'                 => 'e-mail',
        'password'              => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'current_password'      => 'mot de passe actuel',
        'telephone'             => 'téléphone',
        'subject'               => 'objet',
        'message'               => 'message',
        'profile_image'         => 'photo',
        'image'                 => 'photo',
        'role'                  => 'rôle',
        'bio'                   => 'présentation',
        'duration'              => 'durée',
        'additional_days'       => 'nombre de jours',
    ],
];
