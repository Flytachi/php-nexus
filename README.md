# Flytachi Nexus — lightweight event broker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/flytachi/nexus.svg?style=flat-square )](https://packagist.org/packages/flytachi/nexus )
[![PHP Version Require](https://img.shields.io/packagist/php-v/flytachi/nexus.svg?style=flat-square )](https://packagist.org/packages/flytachi/nexus )
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square )](https://opensource.org/licenses/MIT )

<p align="center">
    <img src="https://raw.githubusercontent.com/Flytachi/php-nexus/main/public/favicon.svg" width="150">
</p>

### 📌 Description

Nexus is a lightweight event broker and process orchestrator built to work with queues.
Its task is to launch the main process, which raises the required number of units (workers), 
distributes tasks between them and controls their life cycle.

🔧 Features:<br>
✅ Starting and managing a worker process pool<br>
✅ Monitoring their condition (health check, logs)<br>
✅ Automatic restart in case of failures<br>
✅ Scaling: you can set the number of units for each task<br>
✅ Working through message queues (pub/sub model)<br>
✅ Suitable for microservice architecture and backend workers<br>

## Installation

### Requirements
- RabbitMQ host
- php 8.3
- composer

### Settings (composer)
-- Be sure to set up an environment variable (Environment)

```sh
  composer check-platform-reqs
```
#### Install all missing components
```sh
  composer install
```

<hr>

## Environment
The environment must contain a list of values.
In an environment variable or in a `.env` file (example `root/.env`)
```.env
TIME_ZONE=UTC
DEBUG=false
LOGGER_LEVEL_ALLOW=INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY
LOGGER_MAX_FILES=10
LOGGER_FILE_DATE_FORMAT=Y-m-d
LOGGER_LINE_DATE_FORMAT="Y-m-d H:i:s P"

AMQP_API_PORT=15672
AMQP_API_VHOST=/
AMQP_HOST=localhost
AMQP_PORT=5672
AMQP_USER=guest
AMQP_PASS=guest
```

<hr>

## Service command
Commands for service management! The shell must be responsive (php >= 8.3),<br>
otherwise the commands will not work
### Start service:
```sh
  php extra run script main.service start 
```

### Stop service:
```sh 
  php extra run script main.service stop 
```

### Service status:
```sh 
  php extra run script main.service status 
```

<hr>

### Api Interface
<strong>started (php/composer)</strong>
started web interface
```sh 
php extra run serve --port=8000
```

In the browser, contact the address `http://0.0.0.0:8000/`

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
