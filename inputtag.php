<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneInput>
	<Title><? $title ?></Title>
	<Prompt>Search by tag</Prompt>
		<URL>http://<? $hostname ?>/search.php</URL>
		<InputItem>
			<DisplayName>Enter the tag</DisplayName>
			<QueryStringParam>tag</QueryStringParam>
			<InputFlags>A</InputFlags>
		</InputItem>
</CiscoIPPhoneInput>
