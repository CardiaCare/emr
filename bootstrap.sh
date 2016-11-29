# Variables
DBHOST=localhost
DBNAME=emr
DBUSER=emr
DBPASSWD=emrvagrant
DOCUMENT_ROOT_YII="/var/www/emr/web"

echo -e "\n--- Updating packages list ---\n"
apt-get update

# MySQL setup for development purposes ONLY
echo -e "\n--- Install MySQL specific packages and settings ---\n"
debconf-set-selections <<< "mysql-server mysql-server/root_password password $DBPASSWD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DBPASSWD"
apt-get -y install mariadb-server

echo -e "\n--- Setting up our MySQL user and db ---\n"
mysql -uroot -p$DBPASSWD -e "CREATE DATABASE $DBNAME"
mysql -uroot -p$DBPASSWD -e "grant all privileges on $DBNAME.* to '$DBUSER'@'localhost' identified by '$DBPASSWD'"

echo "-- Install tools and helpers --"
apt-get install -y --force-yes python-software-properties vim htop curl git npm

echo "-- Set up Apache2 --"
apt-get -y install -y apache2 git curl
apt-get -y install php7.0 libapache2-mod-php7.0
apt-get -y install php7.0-mysql php7.0-curl php7.0-gd php7.0-intl php7.0-mbstring
echo "
<VirtualHost *:80>
    ServerName emr.local
    DocumentRoot $DOCUMENT_ROOT_YII
    <Directory $DOCUMENT_ROOT_YII>
        DirectoryIndex index.php
        AllowOverride All
        Order allow,deny
        Allow from all
	<Limit GET HEAD POST PUT DELETE OPTIONS>
          # Deprecated apache 2.2 syntax:
          # Order Allow,Deny
          # Allow from all
          # Apache > 2.4 requires:
          Require all granted
	</Limit>
    </Directory>
</VirtualHost>
" > /etc/apache2/sites-available/emr.conf
a2enmod rewrite
a2dissite 000-default
a2ensite emr
service apache2 restart
cd /var/www/emr
echo "-- Set up composer --"
curl -Ss https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer
composer install --no-progress
echo "-- Initialize yii --"
php init.php --env=Development --overwrite=All
echo "
<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=emr',
            'username' => 'emr',
            'password' => 'emrvagrant',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
    ],
];
" > /var/www/emr/config/common-local.php
echo "-- Migrate DB --"
php yii migrate --interactive=0
echo "-- RBAC Update --"
php yii rbac/update
echo "** [YII2] Visit http://localhost:8089 in your browser for to view the application **"
