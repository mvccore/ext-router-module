# MvcCore Extension - Router - Modules

[![Latest Stable Version](https://img.shields.io/badge/Stable-v4.3.1-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-router-module/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.3-brightgreen.svg?style=plastic)

MvcCore Router extension to manage multiple websites in single project, defined by domain routes, targeted by module property in URL completing.  
This router is the way, how to route your requests in domain level with params or variable sections, namespaces, default param values and more.

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
	4.2. [Usage - Targeting Custom Application Part](#user-content-42-usage---targeting-custom-application-part)  
    4.3. [Usage - Creating Module Domain Route](#user-content-43-usage---creating-module-domain-route)  
    4.4. [Domain Routes And Standard Routes Definition](#user-content-43-usage---domain-routes-and-standard-routes-definition)  

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

## 4.2. Usage - Targeting Custom Application Part
Module domain route is special kind of route how to define part of application, not directly controller or action.  
The definition of a custom part of the application is designed very freely so that you are able to do whatever you want.  
- Your custom application part also could be defined as namespace in module domain route, which is used for routed controller by 
  standard route(s), when the controller is not defined absolutely (by single backslash at the beginning or by double slashes 
  in the beginning).
- Your custom application part could be also defined by allowed modules definition, added into any standard route. By this way,
  you could define which standard route could be used for which module. There could be more than one allowed module name or none 
  (`NULL` means all modules are allowed).
- Your custom part of application could be defined by special param name, called `module`. This param is added into request 
  object every time when is any module domain route matched. It's value is completed in match by domain route module name.
  This param serves only for describing purposes, how to generate URL from one module to another. But you could use it in 
  controller processing and rendering for anything else how to generate application result.

## 4.3. Usage - Creating Module Domain Route
- Module domain route is special kind of route how to define part of application, not directly controller or action. 
- Module domain route pattern could contain any params or variable sections as standard route, it could contain percentage dynamic replacements for domain URL part as standard route (like `%domain%` or `%tld%` ...), but id can not contain anything else then sheme definition (`http://`, `https://` or `//`), domain part (and base part if you need), nothing else. 
- Module domain route is extended directly from standard `\MvcCore\Route` class. 
- Domain routes also could be defined as single configuration arrays passed into module domain route constructor, 
  when you define module domain routes on router instance by methods `SetDomainRoutes()`, `AddDomainRoutes()` or `AddDomainRoute()`.
  
```php
// Instance by specified all constructor params:
new \MvcCore\Ext\Routers\Modules\Route(
    "//blog.%sld%.%tld%",             // pattern
    "blog",        "Blog",            // module, namespace
    ["page" => 1], ["page" => "\d+"], // defaults, constraints
    [                                 // advanced configuration
        "allowedLocalizations" => ["en-US"],
        "allowedMediaVersions" => ["full" => ""]
    ]
);

// Or instance by single configuration arrray:
new \MvcCore\Ext\Routers\Modules\Route([
    "pattern"              => "//blog.%sld%.%tld%",
    "module"               => "blog",
    "namespace"            => "Blog",
    "defaults"             => ["page" => 1],
    "constraints"          => ["page" => "\d+"],
    "allowedLocalizations" => ["en-US"],
    "allowedMediaVersions" => ["full" => ""]
]);

// Or instance by single configuration arrray with directly defined 
// regular expression `match` pattern and `reverse` pattern`:
new \MvcCore\Ext\Routers\Modules\Route([
    "match"                => "#^//blog\.%sld%\.%tld%$#",
    "reverse"              => "//blog.%sld%.%tld%",
    "module"               => "blog",
    "namespace"            => "Blog",
    "defaults"             => ["page" => 1],
    "constraints"          => ["page" => "\d+"],
    "allowedLocalizations" => ["en-US"],
    "allowedMediaVersions" => ["full" => ""]
]);
```

[go to top](#user-content-outline)

## 4.4. Usage - Domain Routes And Standard Routes Definition
To work with modules, you need to specify more. With standard routes, you need to specify "module domain routes":
```php
// Define domain routes (domain routes also could be defined as single 
// configuration arrays passed into module domain route constructor):
$router->SetDomainRoutes([
    // to define blog website module:
    'blog'    => [
        'pattern'      => '//blog.example.com',
        'namespace'      => 'Blog',
    ],
    // to define main website module:
    'main'    => [
        'pattern'      => '//[<productsCategory>.]example.com',
        'constraints' => ['productsCategory' => '-a-z0-9]+'],
        'namespace'      => 'Main',
    ],
	// now all requests into `main` module will have `productsCategory` param
	// in request object. For request into `http://example.com/`, there will
	// be `NULL` value for this param, so you can recognize a homepage or there
	// are many other ways how to target a homepage.
]);

// Now let's define standard routes:
$router->SetRoutes([
    
    // Absolutely defined target controller in `\App\Controllers`:
    '\Admin\Index:Index'   => '/admin',
    
    // Relatively defined controllers in `\App\Controllers` by module route namespace:
    
    // There will be only used controllers `\Main\Categories` and
    // `\Main\Categories`, because there is allowed only `main` module.
    // Example match by: `http://phones.example.com/`, `http://phones.example.com/2`, ...
    'Categories:List'      => [
        'pattern'          => '/[<page>]',
        'defaults'         => ['page' => 1],
        'constraints'      => ['page' => '\d+'],
        'allowedModules'   => ['main'],
    ],
    // Example match by: `http://phones.example.com/products/brands-samsung/price-0-1000`, ...
    'Products:List'   => [
        'pattern'          => '/products[/<filter*>]',
        'constraints'      => ['filter' => '-a-zA-Z0-9_/]+'],
        'allowedModules'   => ['main'],
    ],
    // Example match by: `http://phones.example.com/product/samsung-galaxy-note-9/white`, ...
    'Products:Detail' => [
        'match'            => '#^/product/(?<id>\d+)(/(?<color>[a-z]+))?/?#',
        'reverse'          => '/product/<id>[/<color>]',
        'defaults'         => ['color' => 'red'],
        'allowedModules'   => ['main'],
    ],
    // There will be only used controller `\Blog\Posts`, 
    // because there is allowed only `blog` module.
    // Example match by: `http://blog.example.com/`, `http://blog.example.com/2`, ...
    'Posts:List'           => [
        'pattern'          => '/<page>]',
        'defaults'         => ['page' => 1],
        'constraints'      => ['page' => '\d+'],
        'allowedModules'   => ['blog'],
    ],
    // Example match by: `http://blog.example.com/post/which-phone-to-buy`, ...
    'Posts:Detail'         => [
        'pattern'          => '/post/[<path>]',
        'constraints'      => ['path' => '[-a-zA-Z0-9_/]+']
        'allowedModules'   => ['blog'],
    ],
    // There will be used controller `\Main\Index` but 
    // there could be also used controller `\Blog\Index`.
    // Example match by: `http://example.com/pages/contacts`, `http://blog.example.com/pages/contacts`, ...
    'Index:Index'          => [
        'pattern'          => '/pages/<path>',
        'constraints'      => ['path' => '[-a-zA-Z0-9_/]+'],
        //'allowedModules' => [NULL], //if there is aloowed `NULL`, all modules are allowed
    ],    
]);
```

[go to top](#user-content-outline)
