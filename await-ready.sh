#!/usr/bin/env sh
error_count=0

while [ $error_count -lt 20 ]
do
	error_count=$(expr $error_count + 1);
	echo "Run: $error_count/20";
	mysql --host=mysql --user=root --password="$MYSQL_ROOT_PASSWORD" --execute='SHOW DATABASES;' > /dev/null 2>&1;
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