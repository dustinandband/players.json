#!/bin/bash

# Checks player_aliases.php for duplicate keys
# (Once php initializes arrays duplicates keys automatically get overridden, so can't check from within PHP)

grep -Eo '[0-9]{15,20}' class/data.php | sort | uniq -d > DuplicateKeys.txt

#remove empty lines
#sed -i '/^[[:space:]]*$/d;s/[[:space:]]*$//' DuplicateKeys.txt

if test -s DuplicateKeys.txt ; then
	echo -e "Duplicate Keys found in data.php file. \n\tCheck 'DuplicateKeys.txt'"
else
	rm DuplicateKeys.txt
fi