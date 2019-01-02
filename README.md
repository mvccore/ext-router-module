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
    4.2. [Usage - Default Localization](#user-content-42-usage---domain-routes-and-standard-routes-definition)  

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
