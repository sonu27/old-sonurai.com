# sonurai.com

[![Codacy Badge](https://www.codacy.com/project/badge/4214eeec857145c7b9e6f9c9df9f2e60)](https://www.codacy.com/app/sonu27/sonurai-com)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sonu27/sonurai.com/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sonu27/sonurai.com/?branch=master)
![License](https://img.shields.io/github/license/sonu27/sonurai.com.svg)

### Install instructions after clone
```
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
chown "$HTTPDUSER":"$HTTPDUSER" app/cache app/logs -R
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
chown "$HTTPDUSER":"$HTTPDUSER" src/AppBundle/Resources/public/wallpaper -R
composer install --prefer-dist -o
php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump --env=prod --no-debug
```
