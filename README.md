# IPv4 Utils
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
