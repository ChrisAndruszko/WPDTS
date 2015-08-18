Welcome to the WordPress Developer Team Setup (version 1.0.0)!

This plugin allows you to easily transfer users from one WordPress site to another.


USING THIS PLUGIN

1.1 GETTING STARTED
After being activated, this plugin can be found under "Settings" in your Dashboard.

YOU WILL NEED THE FOLLOWING INFORMATION READY:

	- Server URL:
		This is the URL of your server that hosts the MySQL database for the WordPress site containing the users you want to copy. Your MySQL server MUST allow remote connections, but this is not a concern if both of the WordPress sites you want to copy from and copy to are hosted on the same server.

	- User Name:
		This is a user name for your MySQL server. This user MUST have privileges to SELECT, INSERT, and UPDATE.

	- Server Password:
		The password corresponding to your user name to access your MySQL database.

	- Database Name:
		This is the name of your MySQL database.

	-Database Prefix:
		This is a string of characters that come before your MySQL database tables. This is usually created during the installation of WordPress.

			EXAMPLE:
						If your prefix is "wp_" (without quotes), the "users" table in your
						MySQL database will be displayed as "wp_users".

		You may also find this prefix in your "wp_config.php" file. If you do not have a prefix set, you must leave this field blank.

1.2 IMPORTING USERS
After you enter your MySQL database's credentials, you may click on "Import Users". This will immediately copy all the users to the current WordPress site that you are operating from.

This plugin will copy the following information:
	
	Display Name

	User Login

	User URL

	User Email

	Nicename

	First Name

	Last Name

	Description

	User Role (capabilities)

	User Password

Upon successfully importing, the users will be sent an email alerting them of the import.


TROUBLESHOOTING

2.1 CANNOT CONNECT TO DATABASE
If you cannot connect to the database after entering your credentials, the following message will be displayed:
"Could not connect to database:" followed by an error message.

Some of the most common reasons why this plugin may not connect to the database:
	- Your credentials are invalid.
	- The database you are trying to connect to is hosted on a different server than your current WordPress site, and does not allow remote connections.
	- The database you are trying to connect to has custom tables that alter WordPress's default setup.
	- The database you are trying to connect to is not a MySQL database.

2.2 CANNOT IMPORT USER(S)
If you cannot import a user, the following message will be displayed:
"User (User Login) could not be imported."

Some of the most common reasons users cannot be imported include:
	- User has already been imported.
	- User has the same email address as another user currently on this WordPress site.
	- User has incomplete required information necessary for WordPress to create a user.




Thank you for using WordPress Developer Team Setup!
