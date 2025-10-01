

1. add fiel 'description' at table assets_contents
2. create table guset
3. add field 'role_id' at table fo_invites 
4. add field 'other_data' at table fo_invites 
5. add field 'other_data' at table fo_user_profiles 
6. add field 'board_id' at table activity_logs 
7. create table notification 

$ php artisan make:model Product -m
(-m migrage)

$ php artisan migrate

$ php artisan make:seeder ProductsTableSeeder

$ php artisan db:seed --class=ProductsTableSeeder

apidoc app -o public/doc/ -c documentation/ApiDocJs

php artisan make:resource User/UserCollection --collection

##Passport
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client Secret: hzg1w189rNjAeAG3gaza67rI7gOi1lXKuxXX0J57
Password grant client created successfully.
Client ID: 2
Client Secret: TYOicnfkD1Tf8spFTegbm3fxGPncHo1jYV5LgdAS

## 500 internal server
composer update
composer require barryvdh/laravel-cors

php artisan key:generate
chmod 755 -R ../public_html
chmod -R o+w storage
php artisan cache:clear
composer dump-autoload
php artisan config:clear
php artisan vendor:publish --provider="Barryvdh\Cors\ServiceProvider"

php composer.phar require barryvdh/laravel-cors:0.11.0
## set virtusl host apache
<VirtualHost *:80>
     <Directory D:/Lavarel/HTPortal/public>
         DirectoryIndex index.php
         AllowOverride All
         Require all granted
         Order allow,deny
         Allow from all
     </Directory>
 </VirtualHost>
 # vhost_start astootee.com
<VirtualHost 206.189.39.243:80>
	ServerName astootee.com
	ServerAlias www.astootee.com
	ServerAdmin webmaster@astootee.com
	DocumentRoot /home/astootee/public_html/laravel/public
	UseCanonicalName Off
	ScriptAlias /cgi-bin/ /home/astootee/public_html/cgi-bin/

	# Custom settings are loaded below this line (if any exist)
	# Include "/usr/local/apache/conf/userdata/astootee/astootee.com/*.conf

	<IfModule mod_userdir.c>
		UserDir disabled
		UserDir enabled astootee
	</IfModule>

	<IfModule mod_suexec.c>
		SuexecUserGroup astootee astootee
	</IfModule>

	<IfModule mod_suphp.c>
		suPHP_UserGroup astootee astootee
		suPHP_ConfigPath /home/astootee
	</IfModule>

	<Directory "/home/astootee/public_html/laravel">
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>

	<Directory "/home/astootee/public_html/laravel/public">
		Options Indexes FollowSymLinks MultiViews
		DirectoryIndex index.php
		AllowOverride All
		Require all granted
		Order allow,deny
		Allow from all
	</Directory>


</VirtualHost>
# vhost_end astootee.com

# vhost_start astootee.com
<VirtualHost *:80>
	ServerName astootee.com
	ServerAlias *.astootee.com
	ServerAdmin webmaster@astootee.com
	DocumentRoot /home/astootee/public_html/laravel/public

	# Custom settings are loaded below this line (if any exist)
	# Include "/usr/local/apache/conf/userdata/astootee/astootee.com/*.conf

	<IfModule mod_userdir.c>
		UserDir disabled
		UserDir enabled astootee
	</IfModule>

	<IfModule mod_suexec.c>
		SuexecUserGroup astootee astootee
	</IfModule>

	<IfModule mod_suphp.c>
		suPHP_UserGroup astootee astootee
		suPHP_ConfigPath /home/astootee
	</IfModule>

	<Directory "/home/astootee/public_html/laravel">
             Options Indexes FollowSymLinks MultiViews
             AllowOverride All
            Order allow,deny
            allow from all
	</Directory>

	<Directory "/home/astootee/public_html/laravel/public">
             Options Indexes FollowSymLinks MultiViews
             DirectoryIndex index.php
             AllowOverride All
             Require all granted
             Order allow,deny
             Allow from all
	</Directory>

</VirtualHost>
# vhost_end astootee.com
