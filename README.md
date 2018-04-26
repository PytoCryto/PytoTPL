# PytoTPL template engine

## Installation
Install via composer
```cli
composer require pytocryto/pytotpl
```

Initiate & configure PytoTPL
```php
$tpl = new \PytoTPL\PytoTPL();
$tpl->setConfig([
    'tpl_folder'      => __DIR__ . '/dir/to/templates/',
    'cache_folder'    => __DIR__ . '/dir/to/cache/',
    'tpl_file_format' => '.tpl',
    'compress'        => false, // compression only available in a valid <HTML> document
    'event_emitter'   => true, // set to false for better performance
]);
```
_________________________________
### Outputs:
```html
{$variable}

{$array.key1.key2.key3}

{echo $variable}

{print $variable}

{$variable|substr:0,-1}
```
_________________________________
### Unescaped output:
```html
{!! $variable !!}
```
_________________________________
### Loops:
```html
{foreach: $myArray as $item}
..
{/foreach}

{for: $i = 0; $i < 99; $i++}
..
{/for}

{while: $i < 9}
..
{/while}
```
_________________________________
### If Statements:
```html
{if: date('Y') == 2017}
    Yey, its 2017!
{elseif: date('Y') == 2018}
    Yey, its 2018!
{else}
    NOOOOo :(
{/if}
```
PytoTPL provides a few shortcuts, instead of having to write conditions like:
```html
{if: isset($data)}
    Yes
{else}
    Nope
{/if}

{-- or --}

{if: empty($data)}
    ...
{/if}
```
you'd instead write:
```html
{isset: $data}
    Yes
{else}
    Nope
{/isset}

{-- or --}

{empty: $data}
    ...
{/empty}
```
_________________________________
### Conditions
```html
{continue}
{break}
````
Instead of writing the long-way condition, e.g:
```html
{if: $userid == 1}
    {continue}
{/if}
```
You can simply include the condition in one line:
```html
{continue: $userid == 1}
{break: $userid == 1}
```
_________________________________
### Extending/including an layout:
```html
{extend 'header'}
```
_________________________________
### Set a variable:
Note: When setting a variable with PytoTPL-syntax you don't have to end it with a semicolon ";"
```html
{var $myVariable = 'My value here'}

{-- or --}

{assign $myVariable = 'My value here'}
```
_________________________________
### Using ternary operators:
```html
{$_GET['id'] OR 'No id has been set!'}
```
_________________________________
### Comments:
```html
{-- Anything here is just a PHP comment --}
```
_________________________________
### Sections (NOT TESTED YET!):
Note: Section names must be written as word characters.
```html
{section: mySection}
...
{/section}
```
If you want to pass variables to the section you may do so:
```html
{section: mySection, varName=Value}
...
{/section}
```
You can also overwrite a section using the following syntax
```html
{section.overwrite: mySection}
...
{/section}
```
###### To render a section
```php
{section.view: mySection}
```
