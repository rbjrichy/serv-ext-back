#!/bin/sh

# The application should have already been installed in the Dockerfile;
# no need to repeat it.

# Create key material if necessary.
if ! grep -q APP_KEY .env 2>/dev/null ; then
  php artisan key:generate
fi

# Run the main container command
exec "$@"
