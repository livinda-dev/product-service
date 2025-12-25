# what this git hub for

- is to work on product service where user can add the product or remove
- deploy in the cloud by using google service as VM and use coolify to deploy

# How CI/CD work

- set the rule to the branch main that can merge from development only if the branch development pass the CI
- after merge to main the coolify will auto deploy




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
