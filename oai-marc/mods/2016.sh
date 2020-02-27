#!/bin/bash

for I in $(echo {2016..2020}); do
	for J in $(echo {1..12}); do
		# fix from month
		L=$J
		for K in $(seq 1 9); do
			if [ "$J" -eq "$K" ]; then
				L="0$J"
			fi
		done
		FROM="$I-$L-01 00:00:00"
		if [ $J == 12 ]; then
			J='01'
			I=$(($I + 1))
		else
			J=$((J+1))
		fi
		for M in $(seq 1 9); do
			if [ "$J" -eq "$M" ]; then
				J="0$M"
			fi
		done
		UNTIL="$I-$J-01 00:00:00"
		if [ "$I" == '2020' -a "$J" == '03' ]; then
			exit 1
		fi
		# RUN!
		echo "$FROM -> $UNTIL" >> 2016.log
		oai-marc --set UCLA --from "$FROM" --until "$UNTIL" --get ident >> 2016.log 2>> 2016-err.log
		sleep 10
	done
done

