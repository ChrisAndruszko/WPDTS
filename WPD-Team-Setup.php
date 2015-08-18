<?php

/*
 *	Plugin Name: WordPress Developer Team Setup
 *	Description: Easily import users to any WordPress site.
 *	Version: 1.0.0
 *	Author: Chris Andruszko
 *	License: GPL2
 *
*/

function wpdts_menu() {


	add_options_page(
		'WordPress Developer Team Setup',
		'WP Dev Team Setup',
		'manage_options',
		'WPDT_Setup',
		'WPDTS_authorize'
	);

}

add_action( 'admin_menu', 'wpdts_menu' );


function WPDTS_authorize() {

	if( !current_user_can( 'manage_options' ) ) {

		wp_die( 'You must be an administrator to access this page.' );

	}

	echo '<h1>Welcome!</h1>';

$serverURL = sanitize_text_field($_POST["server_url"]);
$username = sanitize_text_field($_POST["user_name"]);
$password = sanitize_text_field($_POST["server_password"]);
$dbname = sanitize_text_field($_POST["database_name"]);
$db_prefix_conn = sanitize_text_field($_POST["database_prefix"]);

global $wpdb;

require ('WPD-Team-Setup-markup.html');

$db_prefix = $wpdb->base_prefix;

$users = $db_prefix . 'users';

$userMeta = $db_prefix . 'usermeta';

$conn = new mysqli($serverURL, $username, $password, $dbname);
if (!mysqli_select_db($conn, $dbname)) {
    die('Could not connect to database: ' . mysqli_error($conn));
} else {
	echo "<h2><b>Connection to " . $dbname . " successful.</b></h2>";
}

$users_conn = $db_prefix_conn . 'users';

$userMeta_conn = $db_prefix_conn . 'usermeta';

$thisServerURL = $wpdb->dbhost;
$thisUsername = $wpdb->dbuser;
$thisPassword = $wpdb->dbpassword;
$thisDBname = $wpdb->dbname;

$thisSiteURL = get_site_url();
$thisSiteName = get_bloginfo('name');

$this_db = new mysqli($thisServerURL, $thisUsername, $thisPassword, $thisDBname);
if (!mysqli_select_db($this_db, $thisDBname)) {
    die('Could not connect to database: ' . mysqli_error($this_db));
}

$query = mysqli_query($conn, "SELECT COUNT(ID) AS COUNT FROM {$users_conn} ") or die(mysqli_error($conn));

while ($row = mysqli_fetch_assoc($query)) {
        $user_count = $row["COUNT"];
}

echo '<h2>' . $user_count . ' users found on ' . $dbname . ':</h2><hr>';

$i = 0;
//set $user_count-1 because $i needs to start at 0 to represent the indexes and it also prevents the statement from being looped an extra time.
while($i <= $user_count-1) {
if($result = $conn->query("SELECT * FROM {$users_conn}")) {
	if($count = $result->num_rows) {

// The following loop inserts the corresponding information from the users table.
		while($row = $result->fetch_object()) {
			$display_name[] = $row->display_name;
			$user_login[] = $row->user_login;
			$user_email[] = $row->user_email;
			$user_url[] = $row->user_url;
			$user_nicename[] = $row->user_nicename;
			$user_password[] = $row->user_pass;
		}

echo "<div class='user_info'>";

	echo '<b>Display Name: </b>';
	echo esc_html($display_name[$i]) . '</br>';
	echo '<b>User Login: </b>';
	echo esc_html($user_login[$i]) . '</br>';
	echo '<b>URL: </b>';
	echo esc_html($user_url[$i]) . '</br>';
	echo '<b>Email: </b>';
	echo esc_html($user_email[$i]) . '</br>';
	echo '<b>Nicename: </b>';
	echo esc_html($user_nicename[$i]) . '</br>';

// The Query
$user_query = new WP_User_Query( $args );

// User Loop
if ( ! empty( $user_query->results ) ) {
	foreach ( $user_query->results as $user ) {
		echo '<p>' . esc_html($user->display_name) . '</p>';
	}
}

// The following four queries target the corresponding information from the usermeta table

$query = mysqli_query($conn, "SELECT `meta_value` as `value` FROM `{$userMeta_conn}` WHERE `meta_key` = 'first_name'") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($query)) {
	$first_name[] = $row["value"];
}

$query = mysqli_query($conn, "SELECT `meta_value` as 'value' FROM `{$userMeta_conn}` WHERE `meta_key` = 'last_name'") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($query)) {
	$last_name[] = $row["value"];
}

$query = mysqli_query($conn, "SELECT `meta_value` as 'value' FROM `{$userMeta_conn}` WHERE `meta_key` = 'description'") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($query)) {
	$description[] = $row["value"];
}

$prefix_role = $db_prefix_conn . 'capabilities';

$query = mysqli_query($conn, "SELECT `meta_value` as 'value' FROM `{$userMeta_conn}` WHERE `meta_key` = '{$prefix_role}'") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($query)) {
	$role_array[] = $row["value"];
}

// Creates wp_capability meta_value as a string.
	$role= unserialize($role_array[$i]);
	while ($role_index = current($role)) {
		if ($role_index == 1) {
			$user_role[] = key($role);
		}

	next($role);
	}
			
	echo '<b>Name: </b>';
	echo esc_html($first_name[$i]) . ' ';
	echo esc_html($last_name[$i]) . '</br>';
	echo '<b>Description: </b>';
	echo esc_html($description[$i]) . '</br>';
	echo '<b>Role: </b>';
	echo esc_html($user_role[$i]) . '</br>';

echo '</div>';

$userdata = array(
	'user_login' => $user_login[$i],
	'display_name' => $display_name[$i],
	'first_name' => $first_name[$i],
	'last_name' => $last_name[$i],
	'user_email' => $user_email[$i],
	'role' => $user_role[$i],
	'user_nicename' => $user_nicename[$i],
	'user_url' => $user_url[$i],
	'description' => $user_description[$i]
	);

// Targets the user password corresponding to the user's login name. Using a direct SQL query prevents "rehashing" the hashed password.
$stmt = $this_db->prepare("UPDATE `{$users}` SET `user_pass` = ? WHERE `user_login` = ?") or trigger_error($mysqli->error);
$stmt->bind_param('ss', $user_password[$i], $user_login[$i]);

// The rest of the user information is inputed to the WordPress site.
$user_id = wp_insert_user($userdata);

if( is_wp_error($user_id) ) {
	echo "</br> User " . esc_html($user_login[$i]) . " could not be imported.</br>";
} else {
echo "User successfully imported : ". esc_html($user_login[$i]) . ' (' . esc_html($user_id) . ')' . '</br>';
}

// Sends email to notify the user of the import.
$to = $user_email[$i];
$subject = 'You have been added to a new WordPress site!';
$message = 'You have been added to a new WordPress site at ' . $thisSiteURL . ' via WordPress Developer Team Setup.';

$sent_message = wp_mail( $to, $subject, $message );

if ( $sent_message ) {
    echo 'An email has been sent to ' . esc_html($first_name[$i]) . ' ' . esc_html($last_name[$i]) . '.';
} else {
    echo 'The message was not sent!';
}

	echo '<hr>';

	}
}

// Auto-increment for the next loop.
$i++;

if(!$stmt->execute()){trigger_error("there was an error....".$conn->error, E_USER_WARNING);}


}

// Auto-incrementing has now stopped.

}

?>