#!/bin/bash
set -e

if [[ "$APP_DEBUG" == "true" ]]; then
  export PHP_INI_SCAN_DIR=":/usr/local/etc/php/php.d"
fi

exec docker-php-entrypoint "$@"
