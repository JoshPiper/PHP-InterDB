#!/usr/bin/env sh
error_count=0

while
	((error_count++))
	mysql --host=mysql --user=root --password="$MYSQL_ROOT_PASSWORD" --execute='SHOW TABLES;'
	sleep 2
	[ $error_count -gt 20 -o $? -eq 0 ]
do :; done