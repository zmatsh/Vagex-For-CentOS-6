#!/bin/bash

declare -i t;
t=0;
XX=10; 
while [ 1 ]
do
	if [ !`pgrep firefox` ]; then 
		firefox &> /dev/null &
		t=0;
	fi

	sleep 1;
	t=$t+1;

	if [[ $t -gt $XX ]]; then
		echo "t > xx";
		pkill -9 firefox;
		sleep 1;
		firefox &> /dev/null &
		t=0;
	fi
done
