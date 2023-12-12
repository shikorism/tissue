#!/bin/bash
set -e

if [[ "$APP_DEBUG" == "true" ]]; then
  export PHP_INI_SCAN_DIR=":/usr/local/etc/php/php.d"

  php -r "if (gethostbyname('host.docker.internal') === 'host.docker.internal') exit(1);" &> /dev/null && :
  if [[ $? -eq 0 ]]; then
    # Docker for Windows/Mac
    export PHP_XDEBUG_REMOTE_HOST='host.docker.internal'
  else
    # Docker for Linux
    export PHP_XDEBUG_REMOTE_HOST=$(cat /etc/hosts | awk 'END{print $1}' | sed -r -e 's/[0-9]+$/1/g')
  fi
fi

exec docker-php-entrypoint "$@"
