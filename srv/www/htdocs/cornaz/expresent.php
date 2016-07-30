<?php
// Anwesend
$vorgang = "Sie haben sich als <font color=\"#EE4000\"><b>Anwesend</b></font> eingetragen.";
if ($status == "Anwesend") {
	$ausgabe = "<b>Status:</b> Das Abrufen Ihrer Mails ist bereits aktiviert.";
} else {
	// Am LDAP per SimpleBind anmelden
	if ($bind) {
		$filter="(&(fspExtMailServer=*)(fspLocalMailAddress=$corusername*))";
		$justthese = array( "fspExtMailAddress", "fspExtMailProto", "fspExtMailUsername", "fspExtMailServer", "fspExtMailUserPw", "fspMailfetchOpts");
		$sr=ldap_search($ditcon, $COR_LDAP_SUFFIX, $filter, $justthese);
		$entries = ldap_get_entries($ditcon, $sr);
	} else {
		echo "Verbindung zum LDAP Server nicht möglich!";
	}
	// Warum auch immer, ich musste das erste Element des entries-Arrays löschen.
	array_shift($entries);
	$i=0;
	foreach ($entries as $zugangsdaten) {
		$fh = fopen("$COR_FETCHMAILRC_BUILD","a");
		$Server = $entries[$i]["fspextmailserver"][0];
		$Proto = $entries[$i]["fspextmailproto"][0];
		$Extuser = $entries[$i]["fspextmailusername"][0];
		$Passwd = $entries[$i]["fspextmailuserpw"][0];
		$Opts = $entries[$i]["fspmailfetchopts"][0];
		$zeile = ("poll $Server proto $Proto user $Extuser pass $Passwd is $corusername $Opts\n");
		fwrite($fh, "$zeile");
		fclose($fh);
		$i++;
	}
	
	// fetchcopy ausfuehren
	sudocmd('fetchcopy');
	
	$ausgabe = "<b>Status:</b> Das regelmäßige Abrufen Ihrer eMails wurde für folgende Adressen aktiviert:<p>";
	$i=0;
	foreach ($entries as $zugangsdaten) {
		$Address = $entries[$i]["fspextmailaddress"][0];
		$ausgabe = "$ausgabe <b>$Address</b><p>";
		$i++;
	}
}

// Info Zeile
$margin = "";
$info = "<p><hr size=\"1\" noshade width=\"300\" center><p>
<center>$vorgang</center><p><center>$ausgabe</center><p>
<hr size=\"1\" noshade width=\"300\" center><p>";
site_info($margin, $info);

?>