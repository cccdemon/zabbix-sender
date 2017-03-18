# Zabbix-sender [![Build Status](https://travis-ci.org/disc/zabbix-sender.svg?branch=master)](https://travis-ci.org/disc/zabbix-sender)

## Synopsis

Php implementation of zabbix_sender utility.
Works with Zabbix 2.0.8 and 2.1.7+ versions.

## Code Example
Easy to use:
```
$sender = new \Disc\Zabbix\Sender('localhost', 10051);
$sender->addData('hostname', 'some.key.2', 0.567);
$sender->send();
```


## Installation

Use composer for installation
`composer require disc/php-zabbix-sender`

## Tests

Run `vendor/bin/phpunit` for tests

## Contributors

Aleksandr Khachikyants [disc@mydbg.ru]

## License

MIT