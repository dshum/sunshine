1) git clone git@github.com:dshum/sunshine.git
2) Copy .env.example to .env
3) php artisan key:generate
4) Setup database and configure your environment variables.
5) php artisan migrate
6) Sign up on APIXU and get the API key: https://www.apixu.com/signup.aspx
7) Define environment variable APIXU_KEY=
8) php artisan config:clear
9) Generate the initial data by php artisan weather:load

Update the forecast data by php artisan weather:update
