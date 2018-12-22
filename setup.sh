#!/usr/bin/env bash
#mkdir -p /var/www/data/goals_means-data
#ln -s /var/www/data/goals_means-data data
configPath=/var/lib/config/goals
if [ -d ${configPath} ]; then
    cp ${configPath}/.env ./.env
    echo "ENV file vas setted up"
fi;
