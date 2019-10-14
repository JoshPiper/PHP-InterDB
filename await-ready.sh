#!/usr/bin/env sh
error_count=0

while [ $error_count -lt 20 ]
do
	error_count=$(expr $error_count + 1)
	echo "Run: $error_count/20"
	mysql --host=mariadb --user=root --password="$MYSQL_ROOT_PASSWORD" --execute='SHOW TABLES;'
	echo "Exit Code: $?"
	sleep 2
done