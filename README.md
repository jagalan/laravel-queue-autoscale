laravel-queue-autoscale
=======================

A queue listener that creates child workers up to a maximum limit. Each child runs for a certain amount of executions, after that is killed and recreated.

To execute run `php artisan queue:autolisten´

Caution: This package uses PHP's pcntl functions and you're code might not play well with that. For example, when forking using pcntl_fork the child process inherits any open MySQL connection which is a common reason of failures. See http://php.net/manual/en/function.pcntl-fork.php#70721