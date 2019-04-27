# sonurai.com

[![Build Status](https://travis-ci.org/sonu27/sonurai.com.svg?branch=master)](https://travis-ci.org/sonu27/sonurai.com)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sonu27/sonurai.com/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sonu27/sonurai.com/?branch=master)
![License](https://img.shields.io/github/license/sonu27/sonurai.com.svg)

### Install instructions after clone
Replace `[HTTPDUSER]` user with your web server user. E.g. nginx, apache, www-data
```
chown [HTTPDUSER]:[HTTPDUSER] ./var/cache ./var/logs ./public/wallpaper -R
composer install --prefer-dist --no-dev -a
rm -rf ./var/cache/*
```
