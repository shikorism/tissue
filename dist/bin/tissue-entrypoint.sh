#!/bin/bash
set -e

if [[ "$APP_DEBUG" == "true" ]]; then
  export PHP_INI_SCAN_DIR=":/usr/local/etc/php/php.d"
  export PHP_XDEBUG_REMOTE_HOST=$(cat /etc/hosts | awk 'END{print $1}' | sed -r -e 's/[0-9]+$/1/g')
fi

exec docker-php-entrypoint "$@"
