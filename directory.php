<?
	// https://habrahabr.ru/post/121140/
	header("Content-type: text/xml");
	header("Connection: close");
	header("Expires: -1");

	$page=1;
	if(isset($_GET['page']))
	{
		$page = $_GET['page'];
		if(settype($page,"integer") == false)
		die("<b>BAD REQUEST (invalid type)</b>");
	}

	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

	$ldapconfig['host'] = '192.168.0.8';
	$ldapconfig['port'] = NULL;
	$ldapconfig['basedn'] = 'ou=users,dc=MyCompany,dc=ru';
	$ldapconfig['filter'] = "(&(uid=*)(objectClass=sambaSamAccount)(objectClass=inetOrgPerson))";

	print("<CiscoIPPhoneDirectory>\n");
	print("\t<Title> </Title>\n");
	print("\t<Prompt> </Prompt>\n");

	$DS = @ldap_connect($ldapconfig['host'], $ldapconfig['port']);

	if ( $DS === false )
		exit("ldap_connect problem: ".ldap_error($DS));

	$SRes = @ldap_search($DS, $ldapconfig['basedn'], $ldapconfig['filter']);

	if ( $SRes === false )
		exit("ldap_search problem: ".ldap_error($DS));

	$res = @ldap_get_entries($DS, $SRes);
	if ( $res === false )
		exit("ldap_get_entries problem: ".ldap_error($DS));


	$results = array();

	for ($i = 0; $i < $res["count"]; $i++)
	{
		if (!isset($res[$i]["telephonenumber"]))
			continue;
		if (!isset($res[$i]["displayname"]))
			continue;

		$r_ar = array();

		$r_ar['displayname']=$res[$i]["displayname"][0];
		$r_ar['telephonenumber']=$res[$i]["telephonenumber"][0];
		array_push($results, $r_ar);
	}

	for ($i = 0; $i < (count($results)-1); $i++)
	for ($k = $i+1; $k < count($results); $k++)
	{
		if (strcmp($results[$i]['displayname'],$results[$k]['displayname']) > 0)
		{
			$r_tmp = array();
			$r_tmp = $results[$i];
			$results[$i] = $results[$k];
			$results[$k] = $r_tmp;
		}
	}


	for ($i = (32*($page-1)); $i < (32*$page); $i++)
	{

		if ($i == count($results))
			break;

		print("\t<DirectoryEntry>\n");

		print("\t\t<Name>");
		print($results[$i]['displayname']);
		print("</Name>\n");

		print("\t\t<Telephone>");
		print($results[$i]['telephonenumber']);
		print("</Telephone>\n");

		print("\t</DirectoryEntry>\n");
	}

  print("<SoftKeyItem>");
  print("<Name>Dial</Name>");
  print("<URL>SoftKey:Dial</URL>");
  print("<Position>1</Position>");
  print("</SoftKeyItem>");

  if ($page > 1)
  {
    print("<SoftKeyItem>");
    print("<Name>Prev</Name>");
    print("<URL>http://".$_SERVER['SERVER_NAME']."/asterisk/directory.php?page=".($page-1)."</URL>");
    print("<Position>2</Position>");
    print("</SoftKeyItem>");
  }

  $count_pages = (int) (count($results) / 32);

  if ((count($results) % 32) !=0)
    $count_pages++;

  if ($page < $count_pages)
  {
    print("<SoftKeyItem>");
    print("<Name>Next</Name>");
    print("<URL>http://".$_SERVER['SERVER_NAME']."/asterisk/directory.php?page=".($page+1)."</URL>");
    print("<Position>3</Position>");
    print("</SoftKeyItem>");
  }

  print("<SoftKeyItem>");
  print("<Name>Exit</Name>");
  print("<URL>SoftKey:Exit</URL>");
  print("<Position>4</Position>");
  print("</SoftKeyItem>");

  print("</CiscoIPPhoneDirectory>\n");

?>
