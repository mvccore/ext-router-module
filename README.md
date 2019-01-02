# MvcCore Extension - Router - Modules

[![Latest Stable Version](https://img.shields.io/badge/Stable-v4.3.1-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-router-module/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.3-brightgreen.svg?style=plastic)

MvcCore Router extension to manage multiple websites in single project, defined by domain routes, targeted by module property in URL completing.

## Outline  
1. [Installation](#user-content-1-installation)  
2. [Features](#user-content-2-features)  
    2.1. [Features - Routing](#user-content-21-features---routing)  
    2.2. [Features - Url Generating](#user-content-22-features---url-generating)  
3. [How It Works](#user-content-3-how-it-works)  
    3.1. [How It Works - Routing](#user-content-31-how-it-works---routing)  
    3.2. [How It Works - Url Completing](#user-content-32-how-it-works---url-completing)  
4. [Usage](#user-content-4-usage)  
    4.1. [Usage - `Bootstrap` Initialization](#user-content-41-usage---bootstrap-initialization)  
    4.2. [Usage - Default Localization](#user-content-42-usage---default-localization)  
    4.3. [Usage - Allowed Localizations](#user-content-43-usage---allowed-localizations)  
    4.4. [Usage - Routes Configuration](#user-content-44-usage---routes-configuration)  
    4.5. [Usage - Allow Non-Localized Routes](#user-content-45-usage---allow-non-localized-routes)  
    4.6. [Usage - Detect Localization Only By Language](#user-content-46-usage---detect-localization-only-by-language)  
    4.7. [Usage - Localization Equivalents](#user-content-47-usage---localization-equivalents)  
    4.8. [Usage - Route Records By Language And Locale](#user-content-48-usage---route-records-by-language-and-locale)  
    4.9. [Usage - Redirect To Default And Back In First Request](#user-content-49-usage---redirect-to-default-and-back-in-first-request)  

```
## 1. Installation
```shell
composer require mvccore/ext-router-module
```

## 2. Features

### 2.1. Features - Routing
- Router works with domains and targets requests with different module name in request params 
- Router could target various application controller namespaces (various application parts) by defined namespace in "domain route".
- Router stores another collection of routes, so-called "domain routes". 
- There is processed domain routes routing before standard router routing. For routing strategy by rewrite routes and also for routing strategy by query string.
- Each domain route has required `module` name and `pattern` property (or `match` with `reverse`) describing only scheme and domain URL part (or base path). 
  If route matches incoming request base part, there is assigned property `module` name into request params by matched domain route. Then there is processed standard routing by standard routes.
- Each domain route could have defined `namespace` property for targeted controller by standard route or by query string, if that controller path is not defined absolutely. It means if it doesn't start with single backslash `\` or with double slash `//`.
- Every standard route could have defined advanced configuration property called `allowedModules` with array of strings describing for which module name is the route allowed. If route has not defined that advanced property, it means "route is allowed for all modules".
- Any standard route still could have defined `pattern` (or `match` and `reverse`) absolutely. That route is then used in matching process only for it's fixed defined domain or scheme.

[go to top](#user-content-outline)

### 2.2. Features - Url Generating
- Router could generate URL addresses into different modules only by adding `module` record with target module name into `Url()` method second argument - `$params` array.
- If there is no `module` record in second argument `$params`, there is not generated absolute part of URL, only if standard route requires it or if there is `absolute` record in second argument `$params` with `TRUE` value.
- Canonical and `self` URL addresses are also solved by module domain routes.

[go to top](#user-content-outline)

## 3. How It Works

### 3.1. How It Works - Routing
- After router strategy is solved and before standard routes routing is processed, there is processed module domain routes routing.
- After module domain routes are processed, current module domain route is initialized.
- Then every processed standard route is checked if there is allowed module name or not, if route is not allowed, is skipped.
- After routing, if there is matched any module domain route with namespace and target controller is not defined absolutely, module route namespace is added before target controller path.
- There is not required but recommended to define module domain routes by method `$router->SetDomainRoutes(...);` in `Bootstrap.php`.

[go to top](#user-content-outline)
    
### 3.2. How It Works - Url Completing
- If there is defined `module` record name in second arguments `$params` array, there
  is completed base url part (scheme, domain and base path) by module domain route. 
- If standard route has defined absolute part or if standard route is defined as absolute and if there
  is specified any different `module` record name in second argument `$params` array, error is generated,
  because it's logic conflict.

[go to top](#user-content-outline)

## 4. Usage

## 4.1. Usage - `Bootstrap` Initialization
Add this to `Bootstrap.php` or to **very application beginning**, 
before application routing or any other extension configuration
using router for any purposes:
```php
$app = & \MvcCore\Application::GetInstance();
$app->SetRouterClass('\MvcCore\Ext\Routers\Module');
...
// to get router instance for next configuration:
/** @var $router \MvcCore\Ext\Routers\Module */
$router = & \MvcCore\Router::GetInstance();
```

[go to top](#user-content-outline)

## 4.2. Usage - Domain Routes And Standard Routes Definition
To work with modules, you need to specify more. With standard routes, you need to specify "module domain routes":
```php
// Define domain routes:
$router->SetDomainRoutes([
	// to define blog website module:
	'blog'	=> [
		'pattern'	=> '//blog.example.com',
		'namespace'	=> 'Blog',
	],
	// to define main website module:
	'main'	=> [
		'pattern'	=> '//www.example.com',
		'namespace'	=> 'Main',
]);

// Now let's define standard routes:
$router->SetRoutes([
	// absolutely defined target controller:
    '\Admin\Index:Index'    => '/admin',
	// relatively defined controllers:
    'Product:List'   => [
        'pattern'          => [
            'en'           => '/products-list[/<page>]',
            'de'           => '/produkte-liste[/<page>]',
        ],
        'defaults'         => ['page' => 1],
        'constraints'      => ['page' => '\d+'],
    ],
    'Product:Detail' => [
        'match'            => [
            'en'           => '#^/product/(?<id>\d+)(/(?<color>[a-z]+))?/?#',
            'de'           => '#^/produkt/(?<id>\d+)(/(?<color>[a-z]+))?/?#'
        ],
        'reverse'          => [
            'en'           => '/product/<id>[/<color>]',
            'de'           => '/produkt/<id>[/<color>]'
        ],
        'defaults'         => [
            'en'           => ['color' => 'red'],
            'de'           => ['color' => 'rot'],
        ]
    ],
    'Posts:List'    => [
        'pattern'          => '/[<page>]',
		'defaults'			=> ['page' => 1],
        'constraints'      => ['page'         => '\d+']
    ],
    'Index:Index'    => [
        'pattern'          => '/<path>',
        'constraints'      => [
            'path'         => '[-a-zA-Z0-9_/]*'
        ]
    ],    
]);
```
```

[go to top](#user-content-outline)
