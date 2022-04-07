#!/bin/bash

# Import-File given as first script-argument
FILE=$1

# Static Files used for import
CONF=/home/frank/mysql_local_master_3306.conf
TRNC=/home/frank/truncate_mt_rm.tablefilter.sql
ADD=/home/frank/import_mt_rm.add_ak_fields.sql
DROP=/home/frank/import_mt_rm.drop_ak_fields.sql

# LOG USING FILES
echo "CONF $CONF"
echo "TRUNC-FILE $TRNC"
echo "ADD-AK-FIELDS-FILE $ADD"
echo "IMPORT-FILE $FILE"
echo "DROP-AK-FIELDS-FILE $DROP"

# ADD previous AK-Fields for import-compatibility
echo "ADD previous AK-Fields for import-compatibility"
mysql --defaults-file="$CONF" mt_rm < "$ADD"

# Truncate All Tables, which will be synced from vsrv02
echo "Truncate All Tables, which will be synced from vsrv02"
mysql --defaults-file="$CONF" mt_rm < "$TRNC"

# Start Data-Import without drop and create table statements
echo "Start Data-Import without drop and create table statements"
mysql --defaults-file="$CONF" mt_rm < "$FILE"

# Remove prevous AK-Fields, which was added for compatibility
echo "Remove prevous AK-Fields, which was added for compatibility"
mysql --defaults-file="$CONF" mt_rm < "$DROP"