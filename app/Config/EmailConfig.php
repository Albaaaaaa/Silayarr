<?php
namespace Config;

class EmailConfig {
	
	public $provider = 'Standard';
	// public $provider = 'Google';
	// public $provider = 'AmazonSES';

	public $client = [	'standard' => [
										'host' => 'mail.jagowebdev.com'
										, 'username' => 'support@jagowebdev.com'
										, 'password' => 'Password'
									]
						,'google' => ['client_id' => ''
										, 'client_secret' => ''
										, 'refresh_token' => ''
									]
						, 'ses' => ['username' => ''
										, 'password' => ''
									]
					];
	
	// Disesuaikan dengan konfigurasi username
	public $from = 'support@jagowebdev.com';
	public $fromTitle = 'Jagowebdev.com';
	public $emailSupport = 'support@jagowebdev.com';
}