#!/usr/bin/env sh
max_counts=30
error_count=0

echo "GitHub reports service port as $MYSQL_PORT"
while [ $error_count -lt $max_counts ]
do
	error_count=$(expr $error_count + 1);
	echo "Run: $error_count/$max_counts";
	mysql --host=localhost --port="$MYSQL_PORT" --user=root --password="$MYSQL_PASSWORD" --execute='SHOW DATABASES;' > /dev/null;
	mysql --host=mysql --port="$MYSQL_PORT" --user=root --password="$MYSQL_PASSWORD" --execute='SHOW DATABASES;' > /dev/null;
	mysql --host=127.0.0.1 --port="$MYSQL_PORT" --user=root --password="$MYSQL_PASSWORD" --execute='SHOW DATABASES;' > /dev/null;
	if [ $? -eq 0 ]; then
		error_count=999
	else
		sleep 2
	fi
done
if [ $error_count -eq 999 ]; then
	echo "Successfully waited.";
else
	echo "Wait failed, expect other jobs to fail.";
fi
exit 0