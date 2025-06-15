
## prerequisite

#### make sure php is 8.1 and above, run this to check

```
  php -v
```

#### then make sure that openssl is enabled.

```
  php -m
```

and find openssl in the list. if it is not there, you need to enable it

to enable a specific module in your php installation:

go to installed php folder > php.ini 
or just php

uncomment 
```
extension=openssl
extension_dir = "ext"

```

if you dont find php.ini, just duplicate a php.ini-development and rename it to php.ini

## running the backend

run: 
```
composer install
```

when installation is successful, run 
make sure it runs differently with the frontend port
```
php -S localhost:8000 -t public
```
