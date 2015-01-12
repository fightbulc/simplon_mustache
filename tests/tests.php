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

// ----------------------------------------------
echo '<hr>';

$tmpl = '
{{lang:group:key}}
{{lang:group2:key2}}
{{lang:group3:key3}}
';

$params = [];

$customParser = [
    [
        'pattern'  => '{{lang:(.*?):(.*?)}}',
        'callback' => function ($template, array $match)
        {
            foreach ($match[1] as $index => $key)
            {
                $langKey = 'lang:' . $match[1][$index] . ':' . $match[2][$index];
                $langString = 'LOCALE:' . $match[1][$index] . '-' . $match[2][$index];
                $template = str_replace('{{' . $langKey . '}}', $langString, $template);
            }

            return $template;
        },
    ],
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params, $customParser);

// ----------------------------------------------
echo '<hr>';

$tmpl = '
{{#foo}}
Show some text foo. {{#bar}}Show bar.{{/bar}} Some other foo.
{{/foo}}
';

$params = [
    'foo' => false,
    'bar' => false,
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params);

// ----------------------------------------------
echo '<hr>';

$tmpl = '
{{#products}}
    <div>
    {{#_}}
        {{label}}
    {{/_}}
    </div>
{{/products}}
';

$params = [
    'products' => [
        [
            ['label' => 'Snickers'],
            ['label' => 'Mars'],
            ['label' => 'Twix'],
        ],
        [
            ['label' => 'Snickers2'],
            ['label' => 'Mars2'],
            ['label' => 'Twix2'],
        ],
    ]
];

echo \Simplon\Mustache\Mustache::render($tmpl, $params);
