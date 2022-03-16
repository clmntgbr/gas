#!/usr/bin/env bash

FILE=./.env
if ! test -f "$FILE"; then
   cp ./.env.dist ./.env
fi

while IFS= read -r line
do
  echo "$line"
  echo "***"
done < "$FILE"