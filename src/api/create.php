<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "PUT, POST, OPTIONS");


require_once 'class/Rest.php';
require_once 'lib/phpqrcode/qrlib.php';
require_once 'lib/googleauthenticator/GoogleAuthenticator.php';

$api = new Rest();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'POST':
		/**
		 * allow only if AUTHenticatied user
		 */
		if ( DeviceTAC::read( 'auth' ) ) {
			if (array_key_exists('permissions', $_GET)) {
				$data = file_get_contents("php://input");
				$transaction = $api->create($data, (string)$_GET['permissions'], (string)constant("DATASTORE_" . strtoupper($_GET['permissions'])), json_decode($data)->id);
			} 
			if (isset($transaction)) {
				if ($transaction['errno'] === 409) { 
					header("HTTP/1.0 409 Conflict");
				} elseif ($transaction['errno'] !== 0) { 
					header("HTTP/1.0 422 Unprocessable Entity");
				} else {
					header("HTTP/1.0 201 Created");
				}
				header('Content-Type: application/json');
				echo json_encode($transaction, JSON_PRETTY_PRINT);
			} else {
				header("HTTP/1.0 404 Not Found");
			}
		} else {
			$GA = new GoogleAuthenticator();

			$transaction = [];

			if (array_key_exists('otpauth', $_GET)) {
				$transaction['display'] = "OTPAuth";

				if (isset($_GET['otpauth']) && (string)$_GET['otpauth'] == "qrcode")  {
					$data = json_decode(file_get_contents("php://input"), true);

					if ( isset($data['username']) && $data['username'] != "" && isset($data['key']) /* && $data['key'] != "" */ && isset($data['pid']) && isset($data['properties']) && isset($data['properties'][PUBLIC_ID]) && $data['properties'][PUBLIC_ID] == $data['pid'] ) {
						$Issuer = "zso-aargausued.ch";
						$Username = $data['username'];

						DeviceTAC::restore( DeviceTAC::read( 'expiration' ) );

						// check if user exists and get data
						$Person = DeviceTAC::getuser( '{ "id":' . $data['properties']['IPN'] . ' }' );
						if ( $Person["user"] && isset($Person["user"]["properties"]["Secret"]) ) {
							$Secret = $Person["user"]["properties"]["Secret"];
						} else {
							$Secret = DeviceTAC::read( 'secret' );
						}

						if ( !isset($Secret) || $Secret == "" ) {
							$Secret = $GA->generateSecret();
							
							$OTPAuthURL = 'otpauth://totp/'.$Issuer.':'.$Username.'?secret='.$Secret.'&issuer='.$Issuer;

							ob_start();
								QRCode::png($OTPAuthURL, null, QR_ECLEVEL_M, 5, 0, false);
								$OTPAuthQRCode = base64_encode(ob_get_contents()); 	//<img src="data:image/png;base64,'.$OTPAuthQRCode.'">
							ob_end_clean();
						} else {
							$OTPAuthURL = "";
							$OTPAuthQRCode = "";
						}

						if ( isset($data['key']) && $data['key'] != "" ) {
							$Password = password_hash($data['key'],  PASSWORD_DEFAULT);
						} else {
							$Password = password_hash($Secret,  PASSWORD_DEFAULT);
						}

						DeviceTAC::write( 'secret', $Secret );
						DeviceTAC::write( 'password', $Password );
						DeviceTAC::commit();

						$transaction['executingdevice'] = session_id();
						$transaction['result'] = "success";
						$transaction['key'] = $Password;
						$transaction['OTPAuthURL'] = $OTPAuthURL;
						$transaction['OTPAuthQRCode'] = $OTPAuthQRCode;
						$transaction['errno'] = 0;
						$transaction['error'] = "";
					} else {
						$transaction['executingdevice'] = session_id();
						$transaction['result'] = "failed";
						$transaction['key'] = "";
						$transaction['OTPAuthURL'] = ""; 
						$transaction['OTPAuthQRCode'] = "";
						$transaction['errno'] = 422;
						$transaction['error'] = "missing data";
					}
				} elseif (isset($_GET['otpauth']) && (string)$_GET['otpauth'] == "sms")  {
					$data = file_get_contents("php://input");
	
					$Person = DeviceTAC::getuser( $data );
					$Secret = DeviceTAC::read( 'secret' );
	
					//The url you wish to send the POST request to
					$url = SMS_URL;
	
					//The data you want to send via POST
					$json = json_encode([
						"UserName" => SMS_ID,
						"Password" => SMS_PW,
						"Originator" => "ZSO-Kommando",
						"Recipients" => array(preg_replace('/\s+/', '', $Person["user"]["person"]["properties"]["Mobilnummer"])), //preg_replace('/\s+/', '', $Person["user"]["person"]["properties"]["Mobilnummer"])
						"MessageText" => (string)$GA->getCode($Secret),
						"ForceGSM7bit" => true
					]);
	
					//open connection
					$smsblaster = curl_init();
	
					//set the url, number of POST vars, POST data
					curl_setopt($smsblaster,CURLOPT_URL, $url);
					curl_setopt($smsblaster,CURLOPT_POST, true);
					curl_setopt($smsblaster,CURLOPT_POSTFIELDS, $json);
					curl_setopt($smsblaster,CURLOPT_HTTPHEADER,
						array(
							'Content-Type:application/json',
							'Content-Length: ' . strlen($json)
						)
					);
	
					//So that curl_exec returns the contents of the cURL; rather than echoing it
					curl_setopt($smsblaster,CURLOPT_RETURNTRANSFER, true); 
	
					//execute post
					$transaction['executingdevice'] = session_id();
					$transaction['result'] = curl_exec($smsblaster);
					/*
					$transaction['person'] = $Person;
					$transaction['mobile'] = preg_replace('/\s+/', '', $Person["user"]["person"]["properties"]["Mobilnummer"]);
					$transaction['code'] = curl_getinfo($smsblaster, CURLINFO_HTTP_CODE);
					$transaction['json'] = $json;
					*/
					$transaction['errno'] = 0;
					$transaction['error'] = "";
				} else {
					$transaction['executingdevice'] = session_id();
					$transaction['result'] = "failed";
					$transaction['errno'] = 422;
					$transaction['error'] = "method not defined";
				}		
			} elseif (array_key_exists('register', $_GET)) {
				$data = json_decode(file_get_contents("php://input"), false);
	
				$otp = $data->otp;

				$json = $data;
				unset($json->otp);
				$json = json_encode( $json );

				$Secret = DeviceTAC::read( 'secret' );

				if ( $GA->getCode($Secret) == $otp ) {
					DeviceTAC::restore();
					DeviceTAC::write( 'person', $json );

					if (array_key_exists('permissions', $_GET)) {
						$Person = DeviceTAC::getuser( $json );

						if ( $Person["user"] ) {
							$access = $Person["user"];
							$access['display'] = $access['data'];

							if ( $Person["duration"] < 60*60*24*USER_AUTO_LOCKOUT ) {
								$access['properties']['Periode'] = "+" . USER_AUTO_LOCKOUT . " days";

								if ( $Person["duration"] <= 0 ) {
									$access['properties']['Rechte']['Optionen'] = 254;
									$access['properties']['Rechte']['Berichte'] = 0;
								}
							}

							$access['properties']['Passwort'] = DeviceTAC::read( 'password' );

							unset($access['data']);
							$result = $api->update(json_encode($access, JSON_PRETTY_PRINT), (string)$_GET['permissions'], (string)constant("DATASTORE_" . strtoupper($_GET['permissions'])), $data->id);
						} else {
							$access = new stdClass();
							$access->id = $data->id;
							$access->display = $data->properties->Name.$data->properties->Vorname;
							$access->properties = new stdClass();
							$access->properties->Rechte = new stdClass();
							$access->properties->Rechte->Optionen = 254;
							$access->properties->Rechte->Berichte = 0;
							$access->properties->Secret = DeviceTAC::read( 'secret' );
							$access->properties->Periode = "+" . USER_AUTO_LOCKOUT . " days";
							$access->properties->Passwort = DeviceTAC::read( 'password' );
							$access->forceconcurrentobjects = false;
							$access->concurrentobjectsallowed = false;

							$result = $api->create(json_encode($access, JSON_PRETTY_PRINT), (string)$_GET['permissions'], (string)constant("DATASTORE_" . strtoupper($_GET['permissions'])), $data->id);
						}

						$transaction['display'] = $result['data'];
						$transaction['executingdevice'] = $result['executingdevice'];
						$transaction['result'] = "success";
						$transaction['errno'] = $result['errno'];
						$transaction['error'] = $result['error'];
					} else {
						$transaction['display'] = $data['display'];
						$transaction['executingdevice'] = session_id();
						$transaction['result'] = "failed";
						$transaction['errno'] = 0;
						$transaction['error'] = "permissions not set";
					}

					DeviceTAC::commit();
				} else {
					$transaction['errno'] = 422;
				}
			}
			if (isset($transaction)) {
				if ($transaction['errno'] === 409) { 
					header("HTTP/1.0 409 Conflict");
				} elseif ($transaction['errno'] !== 0) { 
					header("HTTP/1.0 422 Unprocessable Entity");
				} else {
					header("HTTP/1.0 201 Created");
				}
				header('Content-Type: application/json');
				echo json_encode($transaction, JSON_PRETTY_PRINT);
			} else {
				header("HTTP/1.0 403 Forbidden");
			}
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}
?>
