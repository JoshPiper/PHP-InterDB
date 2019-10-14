#!/usr/bin/env sh
error_count=0

while [ $error_count -lt 20 -a $? -ne 0 ]
do
	error_count=$[$error_count+1]
	mysql --host=mysql --user=root --password="$MYSQL_ROOT_PASSWORD" --execute='SHOW TABLES;'
	sleep 2
done