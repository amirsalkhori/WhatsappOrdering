# Discount challenge

### Requirements
-   PHP 8.0 (included in Docker)

### Docker
It has a docker-compose.yml file to spin up nginx and php containers and postgresql container and initialize the application.

-   `docker-compose up -d`
-    `docker-compose exec php composer install`
-    `docker-compose exe php sh`
-    `php bin/console make:migration and php bin/console doctrine:migration:migrate`
     `OR`
-    `php bin/console doctrine:schema:update -f`
-    `php bin/console lexik:jwt:generate-keypair`
-    `bin/console doctrine:fixture:load`

With these commands you can create a user with admin role and a wallet for your user and create a discount
with this 'Arvan-football' code.


### With this url you can access to your documentation
```
http://localhost:8000/api
```

### Your endpoints
```
Login and register
http://localhost:8000/api/users/otp
method = post
request body
{
  "phoneNumber": "9397326612"
}

//////////////////////////////////////
Use discount code
http://localhost:8000/api/discounts/discount_check
method = post
request body
{
  "code": "Arvan-football",
  "phoneNumber": "+989397326613"
}

/////////////////////////////////////
Check your wallet
http://localhost:8000/api/wallets/6
method = get


/////////////////////////////////////
Users list use charge code
http://localhost:8000/api/discounts/report
method = post
request body
{
  "code": "Arvan-football"
}


/////////////////////////////////////
Show all transaction per user
http://localhost:8000/api/wallets?page=1&order[createdAt]=desc
method = get
You need token to show your transaction and show your amount

//////////////////////////////////////
Add order
http://localhost:8000/api/orders/wowcher
method = post
request body
{
  "amount": 100,
  "code": "example"
}
You need token to create order


```

--------------------------------------------------------
### Testing

-   `docker-compose exec php bin/phpunit`
