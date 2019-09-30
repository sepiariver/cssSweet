# Test

1. From this directory `composer install`  
2. Copy a valid, working MODX config.core.php file from your local environment into this directory
3. cssSweet must be installed, or symlinked from the components folders. `ln -s /path/to/repo/core/components/csssweet /path/to/modx/core/components/csssweet` `ln -s /path/to/repo/assets/components/csssweet /path/to/modx/assets/components/csssweet`
3. Run `vendor/bin/phpunit`