#!/bin/bash
set -e

export APP_DEBUG=true
exec tissue-entrypoint.sh php "$@"
