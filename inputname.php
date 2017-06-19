<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneInput>
	<Title><? $title ?></Title>
	<Prompt>Search by name</Prompt>
		<URL>http://<? $hostname ?>/search.php</URL>
		<InputItem>
			<DisplayName>Enter the name</DisplayName>
			<QueryStringParam>q</QueryStringParam>
			<InputFlags>A</InputFlags>
		</InputItem>
</CiscoIPPhoneInput>
