#!/bin/bash
#
# ./stat.sh | sort -n -k3 | tac
#

START_CODE=000
END_CODE=123

for I in $(eval echo {$START_CODE..$END_CODE}); do
	COUNT=$(grep ">$I<" *.html 2>/dev/null | wc -l)
	if [ $COUNT != 0 ]; then
		echo "[$I] - $COUNT"
	fi
done


