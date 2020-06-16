# RateLimitMicroFrameWork
A lightway php rate limit microframework  
This package uses users IP to specify users from each other.
# How to use?
Add package to your project with composer like this:
```shell
composer require amirhwsin/php-ratelimit
```
Then run composer autoload:
```php
require_once __DIR__ . '/vendor/autoload.php';
```
Then add this code where you want to ratelimit:
```php
use RateLimit\Limiter;

$tra = new RateLimit\Limiter();

// How many requests do you want to handle?
$tra->requests = 30;

// In what range? Enter in minutes: 1Min
$tra->inRange = 1;

// Returns true when user good to go and false if user limited
if(!$tra->track()) {
    echo 'rate limited';
    exit; // in case of you want to break users connection with server.
}
```

# Storage
This microframework uses json storage similiar to [jStorage](https://github.com/amireshoon/jStorage).

### Set storage path
```php
$tra->path = '../storage/rate.limit';
```
### Get storage path/content
```php
$storagePath = $tra->path;
fopen($storagePath);
```
