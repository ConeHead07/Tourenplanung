#!/bin/bash

DATE=`date +%Y-%m-%d`
FILE="dump_vsrv02_mt_rm.dataonly.$DATE.sql"
TABLES=""
TABLES="$TABLES mr_auftragskoepfe_dispofilter mr_auftragskoepfe_refs mr_bestellkoepfe"
TABLES="$TABLES mr_bestellpositionen mr_extern mr_fuhrpark mr_fuhrpark_categories"
TABLES="$TABLES mr_fuhrpark_categories_lnk mr_lager mr_lieferscheindruck_dispofilter"
TABLES="$TABLES mr_lieferscheindruckkopf_dispofilter mr_mitarbeiter"
TABLES="$TABLES mr_mitarbeiter_categories mr_mitarbeiter_categories_lnk"
TABLES="$TABLES mr_ressourcen_dispozeiten mr_ressourcen_leistungskatalog"
TABLES="$TABLES mr_ressourcen_sperrzeiten mr_touren_dispo_aktivitaet"
TABLES="$TABLES mr_touren_dispo_attachments mr_touren_dispo_auftraege"
TABLES="$TABLES mr_touren_dispo_auftragspositionen mr_touren_dispo_auftragspositionen_txt"
TABLES="$TABLES mr_touren_dispo_fuhrpark mr_touren_dispo_log mr_touren_dispo_mitarbeiter"
TABLES="$TABLES mr_touren_dispo_mitarbeiter_txt mr_touren_dispo_vorgaenge"
TABLES="$TABLES mr_touren_dispo_vorgaenge_txt mr_touren_dispo_werkzeug mr_touren_portlets"
TABLES="$TABLES mr_touren_timelines mr_user mr_user_profile mr_werkzeug mr_werkzeug_categories"
TABLES="$TABLES mr_werkzeug_categories_lnk mr_wws_ak_keys mr_wws_ap_keys mr_wws_bk_keys"
TABLES="$TABLES mr_wws_bp_keys mr_wws_wb_keys view_verbrauch"

# --complete-insert, -c
# --no-create-info, -t
mysqldump --defaults-file=/home/frank/mysql_10.10.1.23.conf -c -t mt_rm $TABLES > $FILE
echo $FILE