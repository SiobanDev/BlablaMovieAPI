composer install
git init
Create a file .env and a file .env.local
Add the next variables to the .env.local :
DATABASE_URL
OMDB_API_KEY
MAX_MOVIES_NUMB
JWT_SECRET_KEY
JWT_PUBLIC_KEY
JWT_PASSPHRASE
CORS_ALLOW_ORIGIN

-> for the URL variables, it depends on the server connexion : "Listening on ...." or the virtualhosts -> copy the structure of the DATABASE_URL in the .env file. -> MAX_MOVIES_NUMB is a number for the maximal number of allowed votes in a week.

-> the JWT variables depend on the LexikJWTAuthenticationBundle (see the doc)

-> complete the CORS_ALLOW_ORIGIN variable with the api url.