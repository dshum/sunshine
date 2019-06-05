1) git clone git@github.com:dshum/sunshine.git
2) Setup database.
3) php artisan migrate
4) Sign up on APIXU and get the API key: https://www.apixu.com/signup.aspx
5) Define an environment variable APIXU_KEY=
6) php artisan config:clear
7) Generate the initial data by php artisan weather:load

Update the forecast data by php artisan weather:update
