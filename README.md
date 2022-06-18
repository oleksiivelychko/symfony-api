# symfony-api

### Sample API variations based on enterprise-level framework.

⚙️ Deployed on <a href="https://oleksiivelychkosymfonyapi.herokuapp.com/api">Heroku</a>

The first version of API based on <a href="https://api-platform.com/">API Platform</a>
and available at http://127.0.0.1:8000/api as default endpoint.
![API Platform](public/screens/api-platform.png)

💡 Before docker build remove `@php bin/console doctrine:migrations:migrate --no-interaction` from _composer.json_

💡 Install PostgreSQL client:
```
brew install libpq
ln -s /opt/homebrew/Cellar/libpq/14.3/bin/psql /usr/local/bin/psql
```
