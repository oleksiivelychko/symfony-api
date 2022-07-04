# symfony-api

### Sample API variations based on enterprise-level framework.

⚙️ Deployed on <a href="https://oleksiivelychkosymfonyapi.herokuapp.com">Heroku</a>
![API Platform](public/screens/api-platform.png)

📌 The first version of API based on <a href="https://api-platform.com/">API Platform</a>
will be available at <a href="http://127.0.0.1:8000/api">/api</a> as default endpoint.

📌 The second version of API will be available at <a href="http://127.0.0.1:8000/api-v2">/api-v2</a>.

💡 Get access to RabbitMQ web management interface using credentials **_guest:guest_**:
```
symfony open:local:rabbitmq
```

💡 Before `docker build` remove `@php bin/console doctrine:migrations:migrate --no-interaction` from _composer.json_

💡 _(Optional)_ Install PostgreSQL client:
```
brew install libpq
ln -s /opt/homebrew/Cellar/libpq/14.3/bin/psql /usr/local/bin/psql
```
