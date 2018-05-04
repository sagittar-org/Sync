# Sync - Synchronize data between different formats
[![Latest Stable Version](https://poser.pugx.org/pieni/sync/version)](https://packagist.org/packages/pieni/sync)
[![Total Downloads](https://poser.pugx.org/pieni/sync/downloads)](https://packagist.org/packages/pieni/sync)
[![License](https://poser.pugx.org/pieni/sync/license)](https://packagist.org/packages/pieni/sync)

## Requirement
MySQL 5.7.x / PHP 7.0.x

## Install
```bash
composer require pieni/sync:0.1.0
```

## License
MIT License

## Reference
### \pieni\Sync\Handler::__construct($name, $drivers)
- Add drivers to Handler instance

### \pieni\Sync\Handler::get($name = '')
- Get data from the latest driver
- Put data to non-latest drivers
- Return data

### \pieni\Sync\Driver::mtime($name = '')
- Return modification time

### \pieni\Sync\Driver::get($name = '')
- Return data

### \pieni\Sync\Driver::put($data, $mtime, $name = '')
- Put data
- Update modification time to mtime if possible
