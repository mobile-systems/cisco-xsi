<?php require 'config.php' ?>
<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneMenu>
	<Title>Message List <? $title ?></Title>
	<Prompt>Two Messages</Prompt>
	<MenuItem>
		<Name>Message One</Name>
		<URL>QueryStringParam:message=1</URL>
	</MenuItem>
	<MenuItem>
		<Name>Message Two</Name>
		<URL>QueryStringParam:message=2</URL>
	</MenuItem>
	<SoftKeyItem>
		<Name>Read</Name>
		<URL>http://<? $hostname ?>/read.asp</URL>
	</SoftKeyItem>
	<SoftKeyItem>
		<Name>Delete</Name>
		<URL>http://<? $hostname ?>/delete.asp</URL>
	</SoftKeyItem>
</CiscoIPPhoneMenu>
