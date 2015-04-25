sonurai.com
===========
- Install instructions after clone
```
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
chown "$HTTPDUSER":"$HTTPDUSER" app/cache app/logs -R
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
chown "$HTTPDUSER":"$HTTPDUSER" src/AppBundle/Resources/public/img -R
composer install
```
