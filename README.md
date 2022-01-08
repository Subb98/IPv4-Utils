# IPv4 Utils
[![Build Status](https://api.travis-ci.com/Subb98/IPv4-Utils.svg?branch=master)](https://app.travis-ci.com/github/Subb98/IPv4-Utils)
[![StyleCI](https://styleci.io/repos/445891024/shield)](https://styleci.io/repos/445891024)
## Description
Library for calculating subnet masks in classful and CIDR addressing.
## Features
- Convert IPv4 address to binary
- Detection class of IPv4 address
- Detection default subnet mask
- Calculating classful subnet mask
- Calculating CIDR subnet masks (in progress)
## System requirements
- Docker  
or
- PHP 8.x
- Composer
## Usage
```
git clone git@github.com/Subb98/ipv4-utils.git
cd ipv4-utils
docker build . -t phpv4-utils:latest
docker run --rm phpv4-utils:latest vendor/bin/phpunit --testdox tests
```
## License
MIT
