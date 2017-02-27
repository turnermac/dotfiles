#!/bin/bash

for filename in "$@"; do

	case "$filename" in
		*\ *|*-*|*__*)
			dirname=$(dirname "$filename")
			filename_new=$(echo "$filename" | sed 's/ /_/g; s/-/_/g; s/__/_/g')
			mv -iv "$dirname"/"$filename" "$filename_new"
			;;
	esac
done
