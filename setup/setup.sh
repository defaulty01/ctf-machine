#!/bin/bash

# NOTE: RUN THIS FILE AS ROOT


# ==================================================================================================
# Install Requirements
# ==================================================================================================
apt update
apt upgrade
apt install -y apache2 mariadb-server php7.4 php7.4-cli php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath

cp -R ../public/ /var/www/html/

# ==================================================================================================
# Configure Flags
# ==================================================================================================
export sqli_flag1="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export sqli_flag2="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export desser_flag="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export ssrf_flag="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export rce_flag="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export user_flag="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export root_flag="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)"
export RCE_FLAG="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)"
export DESSER_FLAG="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)"
export SSRF_FLAG="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)"
export config_path="/var/www/html/public/config"
export mysql_files="/opt/mysql_files"
export flag_files="/opt/flag"
export ssrf_files="/opt/www/html"

echo "define(DESSER_FLAG, \"$DESSER_FLAG\");" | tee -a $config_path/configs.php

mkdir $flag_files
echo "BBS{$desser_flag}" | tee $flag_files/$DESSER_FLAG.txt
echo "BBS{$rce_flag}" | tee $flag_files/$RCE_FLAG.txt
echo "BBS{$user_flag}" | tee /root/user.txt
echo "BBS{$root_flag}" | tee /root/root.txt
chmod 600 /root/user.txt
chmod 600 /root/root.txt

mkdir -p $ssrf_files
echo "BBS{$ssrf_flag}" | tee $ssrf_files/$SSRF_FLAG.txt

mkdir $mysql_files
chmod 777 $mysql_files
echo "BBS{$sqli_flag2}" | tee $mysql_files/flag.txt
chmod 644 $mysql_files/flag.txt


# ==================================================================================================
# Configure MYSQL Database
# ==================================================================================================
export DB_NAME="db_$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 5 | head -n 1)"
export DB_USER="user_$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 5 | head -n 1)"
export DB_PASS="$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)"

echo "define(DBPASS, \"$DB_PASS\");" | tee -a $config_path/configs.php
echo "define(DBNAME, \"$DB_NAME\");" | tee -a $config_path/configs.php
echo "define(DBUSER, \"$DB_USER\");" | tee -a $config_path/configs.php


systemctl start mysql 

mysql -u root << EOF
CREATE DATABASE $DB_NAME;
CREATE TABLE $DB_NAME.urls (
    id INT NOT NULL AUTO_INCREMENT,
    url_short VARCHAR(255) NOT NULL,
    url_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE $DB_NAME.definitely_not_a_flag (
    secret VARCHAR(255) NOT NULL
);

CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT FILE, SELECT, UPDATE, INSERT ON *.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;

INSERT INTO $DB_NAME.definitely_not_a_flag (secret) VALUES('BBS{$sqli_flag1}');
EOF


# ==================================================================================================
# Configure Mysql Server
# ==================================================================================================
cp /etc/mysql/mariadb.cnf /etc/mysql/mariadb.cnf.bak
echo "" | tee -a /etc/mysql/mariadb.cnf
echo "[mysqld]" | tee -a /etc/mysql/mariadb.cnf
echo "secure_file_priv=\"$mysql_files/\"" | tee -a /etc/mysql/mariadb.cnf

systemctl restart mysql 


# ==================================================================================================
# Configure Apache Server
# ==================================================================================================
systemctl start apache2
cp shortlink.conf /etc/apache2/sites-available/
cp ssrf.conf /etc/apache2/sites-available/
mv /etc/apache2/apache2.conf /etc/apache2/apache2.conf.bak
cp apache2.conf /etc/apache2/
a2ensite shortlink.conf
a2ensite ssrf.conf
a2dissite 000-default.conf
apache2ctl configtest
a2enmod rewrite
systemctl restart apache2


# ==================================================================================================
# Configure Firewall
# ==================================================================================================
apt install ufw

ufw enable
ufw allow "Apache"
ufw allow "Apache Full"
ufw allow "Apache Secure"
ufw allow "OpenSSH"


# ==================================================================================================
# Setup Cron
# ==================================================================================================
echo "*/5 * * * * find $mysql_files/ -type f -not -name \"flag.txt\" | xargs rm \$1" >> /var/spool/cron/crontabs/root
echo "* * * * * sleep 5; chmod 644 $mysql_files/*" >> /var/spool/cron/crontabs/root


echo "===================================================="
echo "              !!! IMPORTANT !!! "
echo "===================================================="
echo "git_flag : BBS{$git_flag}"
echo "sqli_flag1: BBS{$sqli_flag1}"
echo "sqli_flag2 : BBS{$sqli_flag2}"
echo "desser_flag : BBS{$desser_flag}"
echo "ssrf_flag : BBS{$ssrf_flag}"
echo "rce_flag : BBS{$rce_flag}"
echo "user_flag : BBS{$user_flag}"
echo "root_flag : BBS{$root_flag}"
echo "===================================================="
echo "              !!! Database Creds !!! "
echo "===================================================="
echo "database name: "$DB_NAME
echo "database user: "$DB_USER
echo "database pass: "$DB_PASS
echo "===================================================="