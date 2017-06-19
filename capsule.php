<!-- https://github.com/analytically/capsulecrm-ciscoipphonedir/blob/master/src/main/scala/uk/co/coen/ciscoipphonedir/Main.scala //-->
<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneMenu>
	<Title><? $title ?></Title>
	<Prompt>Search Capsule CRM</Prompt>
	<MenuItem>
		<Name>Search by name</Name>
		<URL>http://<? $hostname ?>/inputname.php</URL>
	</MenuItem>
	<MenuItem>
		<Name>Search by tag</Name>
		<URL>http://<? $hostname ?>/inputtag.php</URL>
	</MenuItem>
<?
	for (search <- lastSearches.get(ip).getOrElse(Nil)) yield {
?>
	<MenuItem>
		<Name>'<? $search ?>'</Name>
		<URL>http://<? $hostname ?>/search.php?q=<? $search ?></URL>
	</MenuItem>
<?
	}
?>
</CiscoIPPhoneMenu>
