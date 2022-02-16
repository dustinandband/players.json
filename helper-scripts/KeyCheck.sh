#!/bin/bash

# Checks player_aliases.php for duplicate keys
# (Once php initializes arrays duplicates keys automatically get overridden, so can't check from within PHP)

#cat includes/player_aliases.php | awk -F '\\ => ' '{print $1}' | cut -d, -f1  | sort | uniq -d > DuplicateKeys.txt
grep -Eo '[0-9]{15,20}' includes/player_aliases.php | sort | uniq -d > DuplicateKeys.txt

#remove empty lines
#sed -i '/^[[:space:]]*$/d;s/[[:space:]]*$//' DuplicateKeys.txt

if test -s DuplicateKeys.txt ; then
	echo -e "Duplicate Keys found in player_aliases.php. \n\tCheck 'DuplicateKeys.txt'"
else
	rm DuplicateKeys.txt
fi