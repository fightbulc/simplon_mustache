<?php

require __DIR__ . '/../vendor/autoload.php';

$tmpl = '
Hello {{name}}
You have just won {{value}} dollars!
{{#in_ca}}
    Well, {{taxed_value}} dollars, after taxes.
{{/in_ca}}
';

$params = [
    "name"        => "Chris",
    "value"       => 10000,
    "taxed_value" => 10000 - (10000 * 0.4),
    "in_ca"       => true,
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params);

// ----------------------------------------------
echo '<hr>';

$tmpl = '
* {{name}}<br>
* {{age}}<br>
* {{company}}<br>
* {{{company}}}
';

$params = [
    "name"    => "Chris",
    "company" => "<b>GitHub</b>",
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params);

// ----------------------------------------------
echo '<hr>';

$tmpl = '
{{#person}}
  <b>{{name}}</b>
{{/person}}
';

$params = [
    "person" => [
        [
            'name' => 'John',
        ],
        [
            'name' => 'Tino',
        ],
    ],
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params);

// ----------------------------------------------
echo '<hr>';

$tmpl = '
{{#repo}}
  --> <b>{{name}}</b>
{{/repo}}
{{^repo}}
  No repos :(
{{/repo}}
';

$params = [
    "repo" => '',
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params);
