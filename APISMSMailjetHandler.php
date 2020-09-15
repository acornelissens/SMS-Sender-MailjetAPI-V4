<?php

require_once 'APISMSMailjet.php';
require_once('config.php');

$APISMSMailjet = new APISMSMailjet($user, $MJ_APIKEY_PUBLIC,$MJ_APIKEY_PRIVATE, $MailJetContactlistID, $filename) ;

header('Content-type: application/json');

if(isset( $_POST['action'] )){

	switch($_POST['action']){
		case "get_list":
			$response_array['status'] = 'success';
			$response_array['action'] = 'get_list';
			$response_array['data']['contactListID'] = $APISMSMailjet->getContactListID();
		  	$response_array['data']['count'] = $APISMSMailjet->getCountFile();
		  	$response_array['data']['file'] = $APISMSMailjet->getFile();
		  	$response_array['data']['defaultFrom'] = $defaultFrom;
		  	$response_array['data']['defaultMessage'] = $defaultMessage;

		break;
		case "send_SMS":

			//Get data
			$num = $_POST['numero'];
			$from = $_POST['from'];
			$message = $_POST['message'];

			//Clean Num
			$cleanNum = $APISMSMailjet->testNumero($num);
			if(!empty($cleanNum)){

				$body = ["To" =>  $cleanNum, "From" => $from, "text" => $message];
				if(!$debug){

					//Send SMS
					$result = $APISMSMailjet->sendSMS($body);
					$response_array['status'] = 'success';
					$response_array['action'] = 'send_SMS';
					$response_array['data']['numero'] = $cleanNum;
					$response_array['data']['result'] = $result;
				}else{
					$response_array['status'] = 'success';
					$response_array['action'] = 'send_SMS';
					$response_array['data']['numero'] = $cleanNum;
					$response_array['data']['result'] = "TEST - DEBUG : ".$cleanNum;
				}
			}else{
				$response_array['data']['numero'] = $num;
				$response_array['status'] = 'error';
				$response_array['data']['result'] = "Erreur sur le numero suivant : ".$num;
			}
		break;
	}
	echo json_encode($response_array);
}