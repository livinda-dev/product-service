# first commit
- set up project
- create `docker-compose.yml` file
- creat the `Dockerfile`
- create the `default.conf` file

# second commit

- access to container by `docker compose exec app bash
`
- install the laravel project `composer create-project laravel/laravel . `
- give storage permission `chmod -R 777 storage bootstrap/cache`

# third commit

- migrate the table product `php artisan make:migration create_products_table`
- create the model `php artisan make:model Product`
- create controller and request
- `php artisan make:controller Api/V1/ProductController --api`
- `php artisan make:request ProductStoreRequest`
- `php artisan make:request ProductStoreRequest`
