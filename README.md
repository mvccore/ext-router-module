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
4. [Example](#user-content-3-example)

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

## 4. Example
Module domain routes definition in `Bootstrap.php`:
```php
// Patch router type:
$app = & \MvcCore\Application::GetInstance();
$app->SetRouterClass('\MvcCore\Ext\Routers\Module');

// Define domain routes:
$router->SetDomainRoutes([
			'blog'	=> [
				'pattern'	=> '//blog.example.com',
				'namespace'	=> 'Blog',
			],
			'main'	=> '//www.example.com'
		]);

// Define standard routes:
...
```

[go to top](#user-content-outline)
