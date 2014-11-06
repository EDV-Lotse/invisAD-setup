<?
/*
Examples file

To test any of the functions, just change the 0 to a 1.
*/
require_once('../config.php');
require_once('../ldap.php');
require_once('../inc/adfunctions.inc.php');

// Array mit Globalvariablen bilden
$options = array(
		    'domain_controllers' => array("$FQDN"),
		    'account_suffix' => "@$DOMAIN",
		    'base_dn' => "$LDAP_SUFFIX",
		    'admin_username' => "$LDAP_ADMIN",
		    'admin_password' => "$LDAP_BIND_PW");

//error_reporting(E_ALL ^ E_NOTICE);

include (dirname(__FILE__) . "/../inc/adLDAP.php");
try {
    $adldap = new adLDAP($options);
}
catch (adLDAPException $e) {
    echo $e;
    exit();   
}
//var_dump($ldap);

echo ("<pre>\n");

// authenticate a username/password
if (1) {
	$result = $adldap->authenticate("stefan", 'P@$$w0rd');
	if ($result == true) {
	    var_dump($result);
	}
}

// add a group to a group
if (0) {
	$result = $adldap->group()->addGroup("Parent Group Name", "Child Group Name");
	var_dump($result);
}

// add a user to a group
if (0) {
	$result = $adldap->group()->addUser("Group Name", "username");
	var_dump($result);
}

// create a group
if (0) {
	$attributes=array(
		"group_name"=>"Test Group",
		"description"=>"Just Testing",
		"container"=>array("Groups","A Container"),
	);
	$result = $adldap->group()->create($attributes);
	var_dump($result);
}

// retrieve information about a group
if (0) {
    // Raw data array returned
	$result = $adldap->group()->info("Guests");
	var_dump($result);
}

if (0) {
	// Raw data array returned
	$result = $adldap->group()->all();
	//var_dump($result);
	$json = array();
	foreach ($result as $i => $value) {
	    $collection = $adldap->group()->infoCollection("$result[$i]", array("*") );
	    //print_r($collection->member);
	    //print_r($collection->description);
	    $rid = ridfromsid(bin_to_str_sid($collection->objectsid));
	    echo "$result[$i] - $rid <br>";
	    $entry = array("$result[$i]",$rid);
	    // create JSON response
	    array_push($json, $entry);
	}
	return $json;
}

if (1) {
	// Raw data array returned
	$result = $adldap->user()->all();
	//var_dump($result);
	$json = array();
	foreach ($result as $i => $value) {
	    $collection = $adldap->user()->infoCollection("$result[$i]", array("*") );
	    //print_r($collection->member);
	    //print_r($collection->description);
	    $rid = ridfromsid(bin_to_str_sid($collection->objectsid));
	    echo "$result[$i] - $rid <br>";
	    $entry = array("$result[$i]",$rid);
	    // create JSON response
	    array_push($json, $entry);
	}
	return $json;
}

// create a user account
if (0) {
	$attributes=array(
		"username"=>"freds",
		"logon_name"=>"freds@mydomain.local",
		"firstname"=>"Fred",
		"surname"=>"Smith",
		"company"=>"My Company",
		"department"=>"My Department",
		"email"=>"freds@mydomain.local",
		"container"=>array("Container Parent","Container Child"),
		"enabled"=>1,
		"password"=>"Password123",
	);
	
    try {
    	$result = $adldap->user()->create($attributes);
	    var_dump($result);
    }
    catch (adLDAPException $e) {
        echo $e;
        exit();   
    }
}

// retrieve the group membership for a user
if (0) {
	$result = $adldap->user()->groups("username");
	print_r($result);
}

// retrieve information about a user
if (1) {
    // Raw data array returned
	$result = $adldap->user()->infoCollection("administrator", array("*"));
	//print_r($result);
	echo $result->givenname."<br>";
	echo $result->sn."<br>";
	echo $result->displayname."<br>";
	echo $result->samaccountname."<br>";
	echo adtstamp2date($result->accountExpires)."<br>";
	echo ridfromsid(bin_to_str_sid($result->objectsid))."<br>";

}

// check if a user is a member of a group
if (1) {
	$result = $adldap->user()->inGroup("stefan","mobilusers");
	var_dump($result);
}

// modify a user account (this example will set "user must change password at next logon")
if (0) {
	$attributes=array(
		"change_password"=>1,
	);
	$result = $adldap->user()->modify("username",$attributes);
	var_dump($result);
}

// change the password of a user. It must meet your domain's password policy
if (0) {
    try {
        $result = $adldap->user()->password("username","Password123");
        var_dump($result);
    }
    catch (adLDAPException $e) {
        echo $e; 
        exit();   
    }
}

// see a user's last logon time
if (0) {
    try {
        $result = $adldap->user()->getLastLogon("username");
        var_dump(date('Y-m-d H:i:s', $result));
    }
    catch (adLDAPException $e) {
        echo $e; 
        exit();   
    }
}

// list the contents of the Users OU
if (0) {
    $result=$adldap->folder()->listing(array('Users'), adLDAP::ADLDAP_FOLDER, false);
    var_dump ($result);   
}
?>