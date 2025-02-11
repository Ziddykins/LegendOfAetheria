#!/bin/bash

NUM=$RANDOM
mkdir -p ../../admin/$NUM/
cat ../templates/admin.php > ../../admin/$NUM/admin
echo -e "\nADMIN_PANEL='/admin/$NUM/admin'\n" >> ../../.env