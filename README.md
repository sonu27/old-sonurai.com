# sonurai.com

[![Build Status](https://travis-ci.org/sonu27/sonurai.com.svg?branch=master)](https://travis-ci.org/sonu27/sonurai.com)
[![Codacy Badge](https://www.codacy.com/project/badge/4214eeec857145c7b9e6f9c9df9f2e60)](https://www.codacy.com/app/sonu27/sonurai-com)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sonu27/sonurai.com/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sonu27/sonurai.com/?branch=master)
![License](https://img.shields.io/github/license/sonu27/sonurai.com.svg)

### Install instructions after clone
```
HTTPDUSER=$(grep -E ^'apache|httpd|[_]www|www-data|nginx' /etc/passwd | cut -d":" -f1)
chown "$HTTPDUSER":"$HTTPDUSER" var/cache var/logs src/AppBundle/Resources/public/wallpaper -R
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs
composer install --prefer-dist -o
php bin/console cache:clear --env=prod --no-debug
ln -s ../src/AppBundle/Resources/public/wallpaper web/wallpaper
```
