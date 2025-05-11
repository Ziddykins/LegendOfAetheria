#!/bin/bash
# LoA Admin Helper

function make_admin() {
    NUM=$RANDOM
    mkdir -p ../../admin/$NUM/
    cat ../templates/admin.php > ../../admin/$NUM/admin
    echo -e "\nADMIN_PANEL='/admin/$NUM/admin'\n" >> ../../.env
}

# Generate mysqldumps for templates
function do_dumpin() {

    echo -ne "\e[0;32m - Adding initial drop and create statements\n\e[0m";
    echo -e "DROP DATABASE IF EXISTS \`###REPL_SQL_DB###\`;\nCREATE DATABASE \`###REPL_SQL_DB###\`;\nUSE \`###REPL_SQL_DB###\`;\n" > /tmp/db.sql;

    echo -e "\e[0;32m - Dumping current database to /tmp/db.sql\e[0m";
    mysqldump db_loa --add-drop-database --add-drop-table --add-locks --no-data >> /tmp/db.sql

    echo -e "\e[0;32m - Adding SQL user drops, creates and grants + flushing privs\e[0m";
    echo "DROP USER IF EXISTS ###REPL_SQL_USER###;
          CREATE USER ###REPL_SQL_USER###;
          GRANT SELECT, INSERT, UPDATE, DELETE ON ###REPL_SQL_DB###.* TO ###REPL_SQL_USER### IDENTIFIED BY '###REPL_SQL_PASS###';
          FLUSH PRIVILEGES;" >> /tmp/db.sql;

    echo -e "\e[0;32m - Making schema replacements for templates\e[0m";
    for i in ACCOUNTS BANNED CHARACTERS FAMILIARS FRIENDS GLOBALS LOGS MAIL MONSTERS BANK GLOBALCHAT STATISTICS;
    do
        LCTBL=`echo $i | perl -e 'while(<>){chomp;print lc$_;}'`;
        SEDREPLTBL="'s/tbl_$LCTBL/###REPL_SQL_TBL_$i###/g'";
        sh -c "sed -i $SEDREPLTBL /tmp/db.sql";
    done

    sed -i 's/db_loa/###REPL_SQL_DB###/g' /tmp/db.sql;
    sed -i 's/user_loa/###REPL_SQL_USER###/g' /tmp/db.sql;

    echo -e "\e[0;32m - Resetting AutoIncrements\e[0m";
    sed -i 's/AUTO_INCREMENT=[[:digit:]]\+/AUTO_INCREMENT=1/g' /tmp/db.sql;

    echo -e " -\e[0;32m Moving file to templates folder";
    mv /tmp/db.sql ../templates/sql.template

    echo -e "\e[0;32m Finished\e[0m";
}

echo "whatcha wanna do?";
echo "1. dump tables for sql templates";
echo "2. make a random admin directory";
echo "3. make perms & g/uids all devvy";
read CHOICE;

if [[ $CHOICE -eq "1" ]]; then
    do_dumpin;
elif [[ $CHOICE -eq "2" ]]; then
    make_admin;
elif [[ $CHOICE -eq "3" ]]; then
    find ../.. -type f -exec chmod 0664 {} \+;
    find ../.. -type d -exec chmod 0775 {} \+;
    echo "enter ya username";
    read USRNM;
    chown -R www-data:$USRNM ../..
else
    echo "no?";
fi



