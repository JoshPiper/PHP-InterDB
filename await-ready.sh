#!/usr/bin/env sh
error_count=0

while [ $error_count -lt 20 ]
do
	error_count=$(expr $error_count + 1);
	echo "Run: $error_count/20";
	mysql --host=mysql --user=root --password="$MYSQL_ROOT_PASSWORD" --execute='SHOW DATABASES;';
	echo "Exit Code: $?";
	if [ $? -eq 0 ]; then
		error_count=999
	else
		sleep 2
	fi
done