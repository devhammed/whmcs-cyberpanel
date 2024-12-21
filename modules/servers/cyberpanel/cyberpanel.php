<?php

if ( ! defined('WHMCS')) {
	die('This file cannot be accessed directly.');
}

function cyberpanel_MetaData(): array
{
	return [
		'DisplayName' => 'CyberPanel Provisioning Module',
		'APIVersion' => '1.0',
		'RequiresServer' => true,
		'DefaultNonSSLPort' => '8090',
		'DefaultSSLPort' => '8090',
		'ServiceSingleSignOnLabel' => 'Login as User',
		'AdminSingleSignOnLabel' => 'Login as Admin',
	];
}

function cyberpanel_ConfigOptions(): array
{
	return [
		'Package Name' => [
			'Type' => 'text',
			'Default' => 'Default',
			'Description' => 'Enter package name for this product',
		],
		'ACL' => [
			'Type' => 'text',
			'Default' => 'user',
			'Description' => 'ACL to be assigned to the new user',
		],
	];
}

function cyberpanel_CreateAccount(array $params): string
{
	$response = cyberpanel_ApiRequest($params, 'CreateAccount');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return 'Error: ' . $error;
	}

	return 'success';
}

function cyberpanel_SuspendAccount(array $params): string
{
	$params['status'] = 'Suspend';

	$response = cyberpanel_ApiRequest($params, 'submitWebsiteStatus');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return 'Error: ' . $error;
	}

	return 'success';
}

function cyberpanel_UnsuspendAccount(array $params): string
{
	$params['status'] = 'Activate';

	$response = cyberpanel_ApiRequest($params, 'submitWebsiteStatus');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return 'Error: ' . $error;
	}

	return 'success';
}

function cyberpanel_TerminateAccount(array $params): string
{
	$response = cyberpanel_ApiRequest($params, 'deleteWebsite');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return 'Error: ' . $error;
	}

	return 'success';
}

function cyberpanel_ChangePackage(array $params): string
{
	$response = cyberpanel_ApiRequest($params, 'changePackageAPI');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return 'Error: ' . $error;
	}

	return 'success';
}

function cyberpanel_ChangePassword(array $params): string
{
	$response = cyberpanel_ApiRequest($params, 'changeUserPassAPI');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return 'Error: ' . $error;
	}

	return 'success';
}

function cyberpanel_TestConnection(array $params): array
{
	$response = cyberpanel_ApiRequest($params, 'verifyConn');

	$error = $response['error_message'] ?? null;

	if ($error) {
		return [
			'success' => false,
			'error' => $error,
		];
	}

	return [
		'success' => true,
		'error' => null,
	];
}

function cyberpanel_ClientArea(array $params): string
{
	return '<form class="cyberpanel" action="' . cyberpanel_ConstructUrl($params, 'loginAPI') . '" method="post" target="_blank">
<input type="hidden" name="username" value="'.$params['username'] . '" />
<input type="hidden" name="password" value="'.$params['password'] . '" />
<input type="submit" value="Login to Control Panel" />
</form>';
}

function cyberpanel_AdminLink(array  $params): string
{
	return '<form class="cyberpanel" action="' . cyberpanel_ConstructUrl($params, 'loginAPI') . '" method="post" target="_blank">
<input type="hidden" name="username" value="'.$params['serverusername'] . '" />
<input type="hidden" name="password" value="'.$params['serverpassword'] . '" />
<input type="submit" value="Login to Control Panel" />
</form>';
}

function cyberpanel_ApiRequest(array $params, string $action): array
{
	try {
		$ch = curl_init();

		$url = cyberpanel_ConstructUrl($params, $action);

		switch ($action) {
			case 'createWebsite':
				$postData =  [
					'adminUser'     => $params['serverusername'],
					'adminPass'     => $params['serverpassword'],
					'domainName'    => $params['domain'],
					'ownerEmail'    => $params['clientsdetails']['email'],
					'packageName'   => $params['configoption1'],
					'websiteOwner'  => $params['username'],
					'ownerPassword' => $params['password'],
					'acl'           => $params['configoption2'],
				];
				break;

			case 'submitWebsiteStatus':
				$postData = [
					'adminUser'    => $params['serverusername'],
					'adminPass'    => $params['serverpassword'],
					'websiteName'   => $params['domain'],
					'state'       => $params['status'],
				];
				break;

			case 'deleteWebsite':
				$postData = [
					'adminUser'    => $params['serverusername'],
					'adminPass'    => $params['serverpassword'],
					'domainName'   => $params['domain'],
				];
				break;

			case 'changePackageAPI':
				$postData = [
					'adminUser'    => $params['serverusername'],
					'adminPass'    => $params['serverpassword'],
					'websiteName'   => $params['domain'],
					'packageName'  => $params['configoption1'],
				];
				break;

			case 'changeUserPassAPI':
				$postData = [
					'adminUser'    => $params['serverusername'],
					'adminPass'    => $params['serverpassword'],
					'websiteOwner'   => $params['username'],
					'ownerPassword'  => $params['password'],
				];
				break;

			case 'verifyConn':
				$postData = [
					'adminUser' => $params['serverusername'],
					'adminPass' => $params['serverpassword'],
				];
				break;

			default:
				return [
					'error_message' => 'Unsupported action',
				];
		}

		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => json_encode($postData),
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json',
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => $params['serversecure'],
			CURLOPT_SSL_VERIFYPEER => $params['serversecure'],
		]);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			return [
				'error_message' => curl_error($ch),
			];
		}

		curl_close($ch);

		return json_decode($response, true);
	} catch (Throwable $e) {
		logModuleCall(
			'cyberpanel',
			$action,
			$postData,
			$e->getMessage(),
			$e->getTraceAsString()
		);

		return [
			'error_message' => $e->getMessage(),
		];
	}
}

function cyberpanel_ConstructUrl(array $params, string $action): string
{
	return (($params['serversecure']) ? 'https' : 'http') . '://' . $params['serverhostname'] . ':' . $params['serverport'] . '/api/' . $action;
}
