<?
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
if (
	isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && 
	isset($_POST['do']) &&
	isset($_POST['x']) && ($_POST['x']=='secure')
	)
{
	header('Content-Type: text/html; charset=utf-8');
	$lang = 'ru';
	
	include_once($_SERVER["DOCUMENT_ROOT"].'/shadow/modules/general/mysql.php');
	include_once($_SERVER["DOCUMENT_ROOT"].'/shadow/modules/general/api.php');
	
	$mail_to = Array();
	//$s=mysql_query("SELECT * FROM `i_user` WHERE `id`=1 ORDER BY `id` ASC LIMIT 1");
	//if (mysql_num_rows($s)>0) { while($r=mysql_fetch_array($s)) { $mail_to[]=$r["email"]; } }
	
	if ($api->Users->check_auth() == true)
	{	
		$sUser=mysql_query("SELECT * FROM `i_shop_users` WHERE `id`='".$api->Users->user_id."'");
		if (mysql_num_rows($sUser)>0)
			$rUser=mysql_fetch_array($sUser);
			
		// СОЗДАНИЕ PDF или EXCEL ФАЙЛОВ
		if (
			($_POST['do'] == 'doFile') &&
			(isset($_POST['id']) && intval($_POST['id']) != '0') &&
			(isset($_POST['file']) && $ob->pr($_POST['file']) != '') &&
			(isset($_POST['type']) && $ob->pr($_POST['type']) != '')
			)
		{
			$id = intval($_POST["id"]);
			$file = $ob->pr($_POST["file"]);
			$type = $ob->pr($_POST["type"]);
			$need = intval($_POST["need"]);
						
			$table = "";
			if ($type=='schet')					$table = "i_schet_na_oplatu";
			else if ($type=='account')			$table = 'i_account_cash';
			else if ($type=='cash')				$table = 'i_cash_order';
			else if ($type=='invoice')			$table = 'i_invoice';
			else if ($type=='completion')		$table = 'i_completion';
			else if ($type=='attorney')			$table = 'i_power_attorney';
			else if ($type=='tmz')				$table = 'i_tmz';
			else if ($type=='statement')		$table = 'i_statement';
			else if ($type=='payorder')			$table = 'i_payorder';			
			
			if ($table!='' && ($file == 'pdf' || $file == 'excel' || ($file == '1c_to' && $type=='payorder')))
			{
				$sDoc=mysql_query("SELECT * FROM `".$table."` WHERE `id_user`='".$api->Users->user_id."' AND `id`='".$id."'");	
				if (mysql_num_rows($sDoc)>0)
				{
					$rDoc=mysql_fetch_array($sDoc);
					
					$dirFilePdf = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/'.($need == 1 && $file == 'pdf' ? 'p/'.$rDoc[$file."_file_stamp"] : $rDoc[$file."_file"]);
					if (!is_file($dirFilePdf))
					{																
						
						if ($file == 'pdf')
						{
						require($_SERVER["DOCUMENT_ROOT"]."/".$lang."/documents/".$file."/".$type.".php");	
							include($_SERVER["DOCUMENT_ROOT"]."/".$lang."/mpdf/mpdf.php"); 				
							
							$name_file = str_replace(' ', '_', $type.'_'.$rUser["id"].'_'.date("YmdHsi").'.pdf');							
							if ($need == 1) // ======== если есть печать и подпись
							{
								if ($rUser["user_signature"]!='' || $rUser["user_stamp"]!='')
								{
									$mpdfW=new mPDF(); 
									$mpdfW->WriteHTML($strPdfWith);						
									$mpdfW->Output($_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/p/'.$name_file,'F');
								}
							}
							else
							{
								$mpdf=new mPDF(); 
								$mpdf->WriteHTML($strPdf);		
								$mpdf->Output($_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/'.$name_file,'F');	
							}																																						
						}
						else if ($file == 'excel')
						{

							// $name_file = $type.''.$rUser["id"].'.xls';
							$name_file = $type.''.$rUser["id"].''.date("YmdHsi").'.xlsx';

						//include libs
							require_once($_SERVER["DOCUMENT_ROOT"]."/".$lang."/documents/".$file."/".$type.".php");
						}
						else if ($file == '1c_to')
						{
						require($_SERVER["DOCUMENT_ROOT"]."/".$lang."/documents/".$file."/".$type.".php");	

							$name_file = str_replace(' ', '_', $type.'_'.$rUser["id"].'_'.date("YmdHsi").'.txt');
							
							$file_dir = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/'.$name_file;
							$fp = fopen($file_dir, "w");
							$fp = fopen($file_dir, "a");
														
							$writeFile = fwrite($fp, $swift_text);
							if ($writeFile) fclose($fp);
						}
						
						$sql_update_doc = "UPDATE `".$table."` SET `".$file."_file".($need == 1 && $file == 'pdf' ? '_stamp' : '')."`='".$name_file."' WHERE `id`='".$rDoc["id"]."'";
						$update_doc = mysql_query($sql_update_doc);																			
					}
				}
				
				echo '
				<script type="text/javascript">
					setTimeout(function() { self.location = "/'.$lang.'/documents/'.$file.'.php?load='.$id.'&type='.$type.'"; }, 50);
				</script>
				';					
			}
		}		
				
		else if (
			($_POST['do'] == 'chooseNeed') &&
			(isset($_POST['id']) && intval($_POST['id']) != '0') &&
			(isset($_POST['need'])) &&
			(isset($_POST['type']) && $_POST['type'] != '')
			)
		{
			$id = intval($_POST["id"]);
			$need = intval($_POST["need"]);
			$type = $ob->pr($_POST["type"]);
						
			$table  = "";
			if ($ob->pr($_POST["type"])=='schet') 				$table = "i_schet_na_oplatu";
			else if ($ob->pr($_POST["type"])=='completion')		$table = "i_completion";
			else if ($ob->pr($_POST["type"])=='tmz')			$table = "i_tmz";
			else if ($ob->pr($_POST["type"])=='invoice')		$table = "i_invoice";
			else if ($ob->pr($_POST["type"])=='account')		$table = "i_account_cash";
			else if ($ob->pr($_POST["type"])=='cash')			$table = "i_cash_order";
			else if ($ob->pr($_POST["type"])=='attorney')		$table = "i_power_attorney";
			else if ($ob->pr($_POST["type"])=='statement')		$table = "i_statement";		
			
			if ($table!='' && ($rUser["user_signature"]!='' || $rUser["user_stamp"]!=''))
			{									
				$sql_update = "UPDATE `".$table."` SET `withstamp`='".$need."' WHERE `id_user`='".$api->Users->user_id."' AND `id`='".$id."'";
				$update = mysql_query($sql_update);	
															
				if ($update)
				{
					echo '
					<script type="text/javascript">
						'.($need==1 ? '
						jQuery(".with_signature").show();
						jQuery(".without_signature").hide();
						jQuery(".with_stamp").show();
						' : '
						jQuery(".with_signature").hide();
						jQuery(".without_signature").show();
						jQuery(".with_stamp").hide();
						').'
					</script>
					';	
				}
			}
		}
		
		else if (
			($_POST['do'] == 'deleteDocument') &&
			(isset($_POST['id']) && intval($_POST['id']) != '0') &&
			(isset($_POST['type']) && $_POST['type'] != '')
			)
		{
			$id = intval($_POST["id"]);
			$type = $ob->pr($_POST["type"]);
				
			$sql_field = "`pdf_file`, `pdf_file_stamp`, `excel_file`, `id`";			
			$table = ""; $link_to = 'cabinet';
			if ($ob->pr($_POST["type"])=='schet') 				{ $table = "i_schet_na_oplatu"; $link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='completion')		{ $table = "i_completion"; 		$link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='tmz')			{ $table = "i_tmz"; 			$link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='invoice')		{ $table = "i_invoice"; 		$link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='account')		{ $table = "i_account_cash"; 	$link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='cash')			{ $table = "i_cash_order"; 		$link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='attorney')		{ $table = "i_power_attorney"; 	$link_to = 'documents'; }
			else if ($ob->pr($_POST["type"])=='statement')		{ $table = "i_statement"; 		$link_to = 'documents'; }		
			else if ($ob->pr($_POST["type"])=='payorder')		{ $table = "i_payorder"; 		$link_to = 'documents'; $sql_field .= ", `swift_opv_file`, `swift_soc_file`, `swift_ocmc_file`, `swift_zp_file`, `reestr_opv_file`, `reestr_soc_file`, `reestr_ocmc_file`, `reestr_zp_file`, `1c_to_file`"; }					
			
			if ($table!='')
			{				
				$sql_query = "SELECT ".$sql_field." FROM `".$table."` WHERE `id_user`='".$api->Users->user_id."' AND `id`='".$id."'";
				$sql = mysql_query($sql_query);
				if (mysql_num_rows($sql) > 0)
				{
					$r=mysql_fetch_array($sql);
					
					$dirFile = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/'.$r["pdf_file"];
					if (is_file($dirFile)) { unlink($dirFile); }
					
					$dirFile = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/'.$r["excel_file"];
					if (is_file($dirFile)) { unlink($dirFile); }
					
					if ($table == 'i_schet_na_oplatu')
					{
						$dirFileWith = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/p/'.$r["pdf_file_stamp"];
						if (is_file($dirFileWith)) { unlink($dirFileWith); }
					}
					else
					{
						$dirFileWith = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/p/'.$r["pdf_file"];
						if (is_file($dirFileWith)) { unlink($dirFileWith); }	
					}
					
					// M100, реестры и swift
					if ($table == 'payorder')
					{
						$dirFileSwift = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/swift/'.$r["swift_opv_file"];
						if (is_file($dirFileSwift)) { unlink($dirFileSwift); }
						
						$dirFileSwift = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/swift/'.$r["swift_soc_file"];
						if (is_file($dirFileSwift)) { unlink($dirFileSwift); }
						
						$dirFileSwift = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/swift/'.$r["swift_ocmc_file"];
						if (is_file($dirFileSwift)) { unlink($dirFileSwift); }
						
						$dirFileSwift = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/swift/'.$r["swift_zp_file"];
						if (is_file($dirFileSwift)) { unlink($dirFileSwift); }
						
						$dirFileReestr = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/reestr/'.$r["reestr_opv_file"];
						if (is_file($dirFileReestr)) { unlink($dirFileReestr); }
						
						$dirFileReestr = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/reestr/'.$r["reestr_soc_file"];
						if (is_file($dirFileReestr)) { unlink($dirFileReestr); }
						
						$dirFileReestr = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/reestr/'.$r["reestr_ocmc_file"];
						if (is_file($dirFileReestr)) { unlink($dirFileReestr); }
						
						$dirFileReestr = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/reestr/'.$r["reestr_zp_file"];
						if (is_file($dirFileReestr)) { unlink($dirFileReestr); }
						
						$dirFile1C_to = $_SERVER['DOCUMENT_ROOT'].'/upload/payorder/'.$r["1c_to_file"];
						if (is_file($dirFile1C_to)) { unlink($dirFile1C_to); }
					}
				}	
				
				if ($table == 'i_schet_na_oplatu')
				{
					$sql_completion = "UPDATE `i_completion` SET `id_schet`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_schet`='".$id."'";
					$update_completion = mysql_query($sql_completion);
					
					$sql_invoice = "UPDATE `i_invoice` SET `id_schet`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_schet`='".$id."'";
					$update_invoice = mysql_query($sql_invoice);
					
					$sql_tmz = "UPDATE `i_tmz` SET `id_schet`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_schet`='".$id."'";
					$update_tmz = mysql_query($sql_tmz);		
				}
				else if ($table == 'i_completion')
				{
					$sql_schet = "UPDATE `i_schet_na_oplatu` SET `id_completion`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_completion`='".$id."'";
					$update_schet = mysql_query($sql_schet);
					
					$sql_invoice = "UPDATE `i_invoice` SET `id_completion`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_completion`='".$id."'";
					$update_invoice = mysql_query($sql_invoice);										
				}
				else if ($table == 'i_invoice')
				{
					$sql_schet = "UPDATE `i_schet_na_oplatu` SET `id_invoice`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_invoice`='".$id."'";
					$update_schet = mysql_query($sql_schet);
					
					$sql_completion = "UPDATE `i_completion` SET `id_invoice`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_invoice`='".$id."'";
					$update_completion = mysql_query($sql_completion);			
					
					$sql_tmz = "UPDATE `i_tmz` SET `id_invoice`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_invoice`='".$id."'";
					$update_tmz = mysql_query($sql_tmz);
					
					$sql_cash = "UPDATE `i_cash_order` SET `id_invoice`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_invoice`='".$id."'";
					$update_cash = mysql_query($sql_cash);					
				}
				else if ($table == 'i_tmz')
				{
					$sql_schet = "UPDATE `i_schet_na_oplatu` SET `id_tmz`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_tmz`='".$id."'";
					$update_schet = mysql_query($sql_schet);
					
					$sql_invoice = "UPDATE `i_invoice` SET `id_tmz`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_tmz`='".$id."'";
					$update_invoice = mysql_query($sql_invoice);										
				}
				else if ($table == 'i_account_cash')
				{
					$sql_statement = "UPDATE `i_statement` SET `id_account`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_account`='".$id."'";
					$update_statement = mysql_query($sql_statement);																				
				}
				else if ($table == 'i_cash_order')
				{
					$sql_invoice = "UPDATE `i_invoice` SET `id_cash`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_cash`='".$id."'";
					$update_invoice = mysql_query($sql_invoice);																				
				}
				else if ($table == 'i_statement')
				{
					$sql_account = "UPDATE `i_account_cash` SET `id_statement`='0' WHERE `id_user`='".$api->Users->user_id."' AND `id_statement`='".$id."'";
					$update_account = mysql_query($sql_account);																				
				}
				else if ($table == 'i_payorder')
				{
					if (isset($_POST['t']) && (intval($_POST['t']) == 1 || intval($_POST['t']) == 2))
					{
						$sql_update_910 = "UPDATE `i_reports_910` SET `payorder_".(intval(@$_POST["t"])==2 ? "ipn_" : '')."id`='0' WHERE `id_statement`='".$id."'";	
						$update_910 = mysql_query($sql_update_910);																			
					}
				}
				
				if (mysql_num_rows($sql) > 0)
				{
					$sql_delete = "DELETE FROM `".$table."` WHERE `id`='".$r["id"]."'";
					$delete = mysql_query($sql_delete);
				}
				
				if ($delete)
				{
					echo '
					<script type="text/javascript">
						jQuery("#window_alert").html("<h3>Вы успешно удалили документ!</h3>");
						show_window_alert();
						setTimeout(function() { self.location = "/'.$lang.'/'.$link_to.'/"; }, 50);
					</script>
					';	
				}
			}
		}
				
		else if (
			($_POST['do'] == 'sendDocument') &&
			(isset($_POST['mail']) && $_POST['mail'] != '') &&
			(isset($_POST['type']) && $_POST['type'] != '') &&
			(isset($_POST['id']) && $_POST['id'] != '')
			)
		{
			$id = $ob->pr($_POST["id"]);
			$type = $ob->pr($_POST["type"]);
			$need = intval($_POST["need"]);
			$file = 'pdf';			
			
			$table  = "";
			if ($ob->pr($_POST["type"])=='schet') 				$table = "i_schet_na_oplatu";
			else if ($ob->pr($_POST["type"])=='completion')		$table = "i_completion";
			else if ($ob->pr($_POST["type"])=='invoice')		$table = "i_invoice";			
			else if ($ob->pr($_POST["type"])=='attorney')		$table = "i_power_attorney";
			else if ($ob->pr($_POST["type"])=='statement')		$table = "i_statement";	
			else if ($ob->pr($_POST["type"])=='tmz')			$table = "i_tmz";			
			else if ($ob->pr($_POST["type"])=='payorder')		$table = "i_payorder";	
			
			if ($table != '')
			{
				$sql_query = "SELECT * FROM `".$table."` WHERE `id`='".$id."' AND `id_user`='".$api->Users->user_id."'";
				$sDoc=mysql_query($sql_query);	
				if (mysql_num_rows($sDoc)>0)
				{
					$rDoc=mysql_fetch_array($sDoc);
				
					$number = intval($rDoc["number"]);
					$date_text = $api->Strings->date($lang, $rDoc["date"], 'sql', 'datetext');
					$nameFile = $rDoc["pdf_file".($need==1 && $rDoc["pdf_file_stamp"]!='' ? '_stamp' : '')];
					
					if ($type=='schet')
					{					
						$summa = intval($rDoc["summa_goods"]);					
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Счёт №'.$number.' от '.$date_text.' на сумму '.number_format($summa, 0, '', ' ').' тг.');																					
					}
					else if ($type=='completion')
					{										
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Акт №'.$number.' от '.$date_text.'.');					
					}			
					else if ($type=='invoice')
					{
						$summa = intval($rDoc["summa_goods"]);					
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Счёт-фактура №'.$number.' от '.$date_text.' на сумму '.number_format($summa, 0, '', ' ').' тг.');										
					}					
					else if ($type=='attorney')
					{										
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Доверенность №'.$number.' от '.$date_text.'.');					
					}
					else if ($type=='statement')
					{										
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Авансовый отчет №'.$number.' от '.$date_text.'.');										
					}
					else if ($type=='tmz')
					{					
						$summa = intval($rDoc["summa_goods"]);						
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Накладная №'.$number.' от '.$date_text.' на сумму '.number_format($summa, 0, '', ' ').' тг.');					
					}					
					else if ($type=='payorder')
					{									
						$subject = ($_POST["subject"]!='' ? $ob->pr($_POST["subject"]) : 'Платежное поручение №'.$number.' от '.$date_text.'.');					
					}
					
					
					$dirFilePdf = $_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/'.($need == 1 && $file == 'pdf' ? 'p/'.$rDoc[$file."_file_stamp"] : $rDoc[$file."_file"]);
					if (!is_file($dirFilePdf))
					{
						require($_SERVER["DOCUMENT_ROOT"]."/".$lang."/documents/".$file."/".$type.".php");
						
						include($_SERVER["DOCUMENT_ROOT"]."/".$lang."/mpdf/mpdf.php"); 				
							
						$name_file = str_replace(' ', '_', $type.'_'.$rUser["id"].'_'.date("YmdHsi").'.pdf');							
						if ($need == 1) // ======== если есть печать и подпись
						{
							if ($rUser["user_signature"]!='' || $rUser["user_stamp"]!='')
							{
								$mpdfW=new mPDF(); 
								$mpdfW->WriteHTML($strPdfWith);						
								$mpdfW->Output($_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/p/'.$name_file,'F');
							}
						}
						else
						{
							$mpdf=new mPDF(); 
							$mpdf->WriteHTML($strPdf);		
							$mpdf->Output($_SERVER['DOCUMENT_ROOT'].'/upload/'.$type.'/'.$name_file,'F');	
						}																																						
					
						$sql_update_doc = "UPDATE `".$table."` SET `".$file."_file".($need == 1 && $file == 'pdf' ? '_stamp' : '')."`='".$name_file."' WHERE `id`='".$rDoc["id"]."'";
						$update_doc = mysql_query($sql_update_doc);	
						
						$nameFile = $name_file;
					}				
					
					$mail = $ob->pr($_POST["mail"]);
					$text = str_replace('\n', '<br />', $ob->pr($_POST["text"])).'
					<div style="padding:20px 0; margin:20px 0; border-top:1px solid #f00; border-bottom:1px solid #f00">
						Это письмо создано автоматически и отвечать на него не нужно. Пожалуйста, ПИШИТЕ на почту '.($rUser["user_mail"]!='' ? '<a href="mailto:'.$rUser["user_mail"].'">'.$rUser["user_mail"].'</a>' : '<a href="mailto:'.$rUser["mail"].'">'.$rUser["mail"].'</a>').'
					</div>
					';
					
					$message = $text;	
					
					$sM=mysql_query("SELECT `mail` FROM `i_mails_send` WHERE `mail`='".$mail."' LIMIT 1");
					if (mysql_num_rows($sM)==0)
					{
						$sql_insert_mail = "INSERT INTO `i_mails_send` (`mail`) VALUES ('".$mail."')";
						$insert_mail = mysql_query($sql_insert_mail);			
					}
					
					require($_SERVER["DOCUMENT_ROOT"].'/ru/libmail.php');
					
					$m = new Mail;
					$m->From("noreply@asistent.kz");
					$m->Organization('asistent.kz');
					$m->To($mail);
					$m->Subject($subject);
					$m->Body($message, "html");    
					$m->Priority(2);
					$m->Attach($_SERVER['DOCUMENT_ROOT']."/upload/".$type."/".($need == 1 && (($rUser["user_signature"]!='' || $rUser["user_signature2"]!='') && $rUser["user_stamp"]!='') ? 'p/' : '').$nameFile, "", "", "attachment");
					$m->smtp_on($_SERVER['HTTP_HOST'], "noreply@asistent.kz", "GV0Vf#Qv;Nv6");
					$m->log_on(true);			
					$send = $m->Send();
				}
			}
			
			echo '
			<script type="text/javascript">';
			
			if ($send)
			{
				echo '
				jQuery("#protocol_send").html("<p style=\"color:#079914;padding:0 0 10px;\">Ваше сообщение успешно отправлено!</p>").slideDown(700);
				setTimeout(function() { close_windows(); }, 3000);
				';
				
				if ($table!='')
					$updateSend = mysql_query("UPDATE `".$table."` SET `send`='1', `send_date`='".date("Y-m-d H:i:s")."' WHERE `id`='".$id."' AND `id_user`='".$api->Users->user_id."'");
			}
			else
				echo 'jQuery("#protocol_send").html("<p style=\"color:#f00;padding:0 0 10px;\">Невозможно отправить сообщение, внутренняя ошибка сервера!<p>").slideDown(700);';
		
			echo '
			</script>';
		}	
		
		// ОКНО С КОНТРАГЕНТАМИ
		else if (
			($_POST['do'] == 'showContr')
			)
		{
			$id = intval($_POST["id"]);
					
			$s=mysql_query("SELECT * FROM `i_client` WHERE `id_user`='".$api->Users->user_id."' ORDER BY `name` ASC");
			if (mysql_num_rows($s)>0)
			{
				?>
				<script type="text/javascript">							
					
					function chooseContr(iin) 
					{ 
						close_window2(); 
						<? if ($id == 10) { ?>						
						jQuery("#document_name").val(iin);
						<? } else { ?>
						jQuery("#document_iin").val(iin); choose_client(<?=($id!=0 ? $id : '')?>); 
						<? } ?>
					}
					<? if (mysql_num_rows($s) > 15) { ?>
					function get_searchContr()
					{
						if (jQuery("#search_contr").val().length > 2 || jQuery("#search_contr").val().length == 0)
						{	
							jQuery.ajax(
							{
								url: "/<?=$lang?>/documents/ajax.php",
								data: "do=getContr&value="+jQuery("#search_contr").val()+"&id=<?=$id?>&x=secure",
								type: "POST",
								dataType : "html",
								cache: false,
								
								beforeSend: function()  {  },
								success: function(responseText)
								{							
									jQuery("#block_contr").empty();
									jQuery("#block_contr").html(responseText);
								},
								error: function()   {  }
							});
						}
					}
					<? } ?>			
				</script>
                <? if (mysql_num_rows($s) > 15) { ?>				
				<div class="search_code">
					<input type="text" id="search_contr" onkeyup="get_searchContr();" placeholder="Поиск... - начните вводить наименование контрагента или ИИН/БИН" />
				</div>
				<?				
				}
				echo '
				<div class="knp" id="block_contr">
					<table cellpadding="0" cellspacing="0" style="width:99%">
						<tr>
							<td><strong>№</strong></td>						
							<td><strong>ИИН/БИН</strong></td>
							<td><strong id="cl_">Наименования контрагента</strong></td>							
						</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{
					if ($id == 10) 
					{
						echo '
						<tr>
							<td>'.$i.'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')">'.$r["bin"].'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')" id="cl_'.$r["bin"].'">'.htmlspecialchars_decode($ob->pr_plus($r["name"])).'</a></td>
						</tr>
						';
					}
					else
					{
						echo '
						<tr>
							<td>'.$i.'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.$r["bin"].'\')">'.$r["bin"].'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.$r["bin"].'\')" id="cl_'.$r["bin"].'">'.htmlspecialchars_decode($ob->pr_plus($r["name"])).'</a></td>
						</tr>
						';
					}
					$i++;
				}	
				echo '
					</table>
				</div>
				';
			}
		}
		
		// ОКНО С КОНТРАГЕНТАМИ -> ПОСЛЕ ПОИСКА
		else if (
			($_POST['do'] == 'getContr') &&
			(isset($_POST["value"]))
			)
		{
			$id = intval($_POST["id"]);
			$value = $ob->pr($_POST["value"]);
			
			$sql_search = "";
			if ($value != '')
				$sql_search = " AND (INSTR(`name`, '".$value."') || INSTR(`bin`, '".$value."'))";
					
			$s=mysql_query("SELECT * FROM `i_client` WHERE `id_user`='".$api->Users->user_id."'".$sql_search." ORDER BY `name` ASC");
			if (mysql_num_rows($s)>0)
			{
				echo '			
				<table cellpadding="0" cellspacing="0" style="width:99%">
					<tr>
						<td><strong>№</strong></td>						
						<td><strong>ИИН/БИН</strong></td>
						<td><strong>Наименования контрагента</strong></td>							
					</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{	
					if ($id == 10) 
					{
						echo '
						<tr>
							<td>'.$i.'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')">'.$r["bin"].'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')" id="cl_'.$r["bin"].'">'.htmlspecialchars_decode($ob->pr_plus($r["name"])).'</a></td>
						</tr>
						';
					}
					else
					{
						echo '
						<tr>
							<td>'.$i.'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.$r["bin"].'\')">'.$r["bin"].'</td>
							<td><a title="Выбрать контрагента - '.$r["bin"].'" onclick="chooseContr(\''.$r["bin"].'\')" id="cl_'.$r["bin"].'">'.htmlspecialchars_decode($ob->pr_plus($r["name"])).'</a></td>
						</tr>
						';
					}
					
					$i++;
				}	
				echo '
				</table>			
				';
			}
		}	
		
		// ОКНО С КОНТРАГЕНТАМИ
		else if (
			($_POST['do'] == 'showStaff')
			)
		{
			$id = intval($_POST["id"]);
			
			if ($id == 1)
			{
				$sF=mysql_query("SELECT * FROM `i_staff` WHERE `id_user`='".$api->Users->user_id."' ORDER BY `firstname` ASC");
				if (mysql_num_rows($sF)>0)
				{
					?>
					<script type="text/javascript">							
						
						function chooseStaff(name) { close_window2(); jQuery("#document_issue_fio").val(name); choose_staff(); }
								
					</script>
					<?
					echo '
					<div class="knp" id="block_contr">
						<table cellpadding="0" cellspacing="0" style="width:99%">
							<tr>
								<td><strong>№</strong></td>		
								<td><strong>ФИО</strong></td>											
								<td><strong>Номер</strong></td>							
							</tr>
					';
					
					$i=1;
					while($rF=mysql_fetch_array($sF))
					{	
						echo '
						<tr>
							<td>'.$i.'</td>
							<td><a onclick="chooseStaff(\''.stripslashes($rF["firstname"]).' '.stripslashes($rF["name"]).'\')">'.stripslashes($rF["firstname"]).' '.stripslashes($rF["name"]).'</td>
							<td><a onclick="chooseStaff(\''.stripslashes($rF["firstname"]).' '.stripslashes($rF["name"]).'\')">'.stripslashes($rF["udv"]).'</a></td>
						</tr>
						';
						
						$i++;
					}	
					echo '
						</table>
					</div>
					';
				}	
			}
			else if ($id == 2)			
			{
				$sF=mysql_query("SELECT `issue_fio`, `pasport_number` FROM `i_power_attorney` WHERE `id_user`='".$api->Users->user_id."' AND `issue_fio`!='' GROUP BY `issue_fio` ORDER BY `issue_fio` ASC");
				if (mysql_num_rows($sF)>0)
				{
					?>
					<script type="text/javascript">							
						
						function chooseStaff(name) { close_window2(); jQuery("#document_issue_fio").val(name); choose_client2(); }
								
					</script>
					<?
					echo '
					<div class="knp" id="block_contr">
						<table cellpadding="0" cellspacing="0" style="width:99%">
							<tr>
								<td><strong>№</strong></td>		
								<td><strong>ФИО</strong></td>											
								<td><strong>Номер</strong></td>							
							</tr>
					';
					
					$i=1;
					while($rF=mysql_fetch_array($sF))
					{	
						echo '
						<tr>
							<td>'.$i.'</td>
							<td><a onclick="chooseStaff(\''.stripslashes($rF["issue_fio"]).'\')">'.stripslashes($rF["issue_fio"]).'</td>
							<td><a onclick="chooseStaff(\''.stripslashes($rF["issue_fio"]).'\')">'.stripslashes($rF["pasport_number"]).'</a></td>
						</tr>
						';
						
						$i++;
					}	
					echo '
						</table>
					</div>
					';
				}
			}									
		}	
		
		// ОКНО С выданной В НАКЛАДНОЙ
		else if (
			($_POST['do'] == 'showIssued')
			)
		{
			
			$s=mysql_query("SELECT * FROM `i_tmz` WHERE `id_user`='".$api->Users->user_id."' AND `issued`!='' GROUP BY `issued`");
			if (mysql_num_rows($s)>0)
			{
				?>
				<script type="text/javascript">							
					
					function chooseIssued(name) { close_window2(); jQuery("#document_issued").val(name); }
							
				</script>
				<?
				echo '
				<div class="knp" id="block_issued">
					<table cellpadding="0" cellspacing="0" style="width:99%">
						<tr>
							<td><strong>№</strong></td>		
							<td><strong>выданной</strong></td>																		
						</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{	
					echo '
					<tr>
						<td>'.$i.'</td>
						<td><a onclick="chooseIssued(\''.str_replace("'", "\'", str_replace("&#039;", "\&#039;", stripslashes($r["issued"]))).'\')">'.stripslashes($r["issued"]).'</td>						
					</tr>
					';
					
					$i++;
				}	
				echo '
					</table>
				</div>
				';
			}																
		}	
		
		// ОКНО С КОНТРАГЕНТАМИ
		else if (
			($_POST['do'] == 'showContract')
			)
		{
			$id = intval($_POST["id"]);
					
			$s=mysql_query("SELECT * FROM `i_contract` WHERE `id_user`='".$api->Users->user_id."' ORDER BY `id` DESC");
			if (mysql_num_rows($s)>0)
			{
				?>
				<script type="text/javascript">							
					
					function chooseContract(number, iin, id) 
					{ 
						close_window2(); 
						<? if ($id == 1) { ?>
						jQuery("#document_contract").val(number); 
						<? } else if ($id == 2 || $id == 3) { ?>
						jQuery("#document_dogovor").val(number); 
						<? } ?>
						jQuery("#document_iin").val(iin); 
						jQuery("#id_contract").val(id);
						choose_client(<?=($id == 3 ? '1' : '')?>); 
					}
					<? if (mysql_num_rows($s) > 15) { ?>
					function get_searchContract()
					{
						if (jQuery("#searchContract").val().length > 2 || jQuery("#searchContract").val().length == 0)
						{	
							jQuery.ajax(
							{
								url: "/<?=$lang?>/documents/ajax.php",
								data: "do=getContract&value="+jQuery("#searchContract").val()+"&x=secure",
								type: "POST",
								dataType : "html",
								cache: false,
								
								beforeSend: function()  {  },
								success: function(responseText)
								{							
									jQuery("#block_contr").empty();
									jQuery("#block_contr").html(responseText);
								},
								error: function()   {  }
							});
						}
					}
					<? } ?>			
				</script>
                <? if (mysql_num_rows($s) > 15) { ?>				
				<div class="search_code">
					<input type="text" id="searchContract" onkeyup="get_searchContract();" placeholder="Поиск... - начните вводить № договора или описание" />
				</div>
				<?				
				}
				echo '
				<div class="knp" id="block_contr">
					<table cellpadding="0" cellspacing="0" style="width:99%">
						<tr>
							<td><strong>№</strong></td>						
							<td><strong>Контрагент</strong></td>
							<td><strong>Договор</strong></td>							
						</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{
					$date_text = $api->Strings->date($lang,$r["data"],'sql','datetext');
					$iin_contr  = ''; $name_contr = '';
					if (intval($r["id_client"])!=0)
					{
						$sContr = mysql_fetch_array(mysql_query("SELECT * FROM `i_client` WHERE `id`='".$r["id_client"]."'"));				
						$iin_contr = $sContr["bin"];
						$name_contr = $ob->pr_plus($sContr["name"]);
					}
						
					echo '
					<tr>
						<td>'.$i.'</td>
						<td><a title="Выбрать договор - '.$r["number"].'" onclick="chooseContract(\''.$r["number"].'\', \''.$iin_contr.'\', \''.$r["id"].'\')">'.$name_contr.'</td>
						<td><a title="Выбрать договор - '.$r["number"].'" onclick="chooseContract(\''.$r["number"].'\', \''.$iin_contr.'\', \''.$r["id"].'\')">Договор №'.$r["number"].' от '.$date_text.'</a></td>
					</tr>
					';
					
					$i++;
				}	
				echo '
					</table>
				</div>
				';
			}
		}
		
		// ОКНО С КОНТРАГЕНТАМИ -> ПОСЛЕ ПОИСКА
		else if (
			($_POST['do'] == 'getContract') &&
			(isset($_POST["value"]))
			)
		{
			$value = $ob->pr($_POST["value"]);
			
			$sql_search = "";
			if ($value != '')
				$sql_search = " AND (INSTR(`number`, '".$value."') || INSTR(`about`, '".$value."'))";
					
			$s=mysql_query("SELECT * FROM `i_contract` WHERE `id_user`='".$api->Users->user_id."'".$sql_search." ORDER BY `id` DESC");
			if (mysql_num_rows($s)>0)
			{
				echo '			
				<table cellpadding="0" cellspacing="0" style="width:99%">
					<tr>
						<td><strong>№</strong></td>						
						<td><strong>Контрагент</strong></td>
						<td><strong>Договор</strong></td>							
					</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{
					$date_text = $api->Strings->date($lang,$r["data"],'sql','datetext');
					$iin_contr  = ''; $name_contr = '';
					if (intval($r["id_client"])!=0)
					{
						$sContr = mysql_fetch_array(mysql_query("SELECT * FROM `i_client` WHERE `id`='".$r["id_client"]."'"));				
						$iin_contr = $sContr["bin"];
						$name_contr = $ob->pr_plus($sContr["name"]);
					}
						
					echo '
					<tr>
						<td>'.$i.'</td>
						<td><a title="Выбрать договор - '.$r["number"].'" onclick="chooseContract(\''.$r["number"].'\', \''.$iin_contr.'\', \''.$r["id"].'\')">'.$name_contr.'</td>
						<td><a title="Выбрать договор - '.$r["number"].'" onclick="chooseContract(\''.$r["number"].'\', \''.$iin_contr.'\', \''.$r["id"].'\')">Договор №'.$r["number"].' от '.$date_text.'</a></td>
					</tr>
					';
					
					$i++;
				}	
				echo '
				</table>			
				';
			}
		}
		
		// ОКНО С ТОВАРАМИ
		else if (
			($_POST['do'] == 'showGoods')
			)
		{
			$id = intval($_POST["id"]);
					
			$s=mysql_query("SELECT * FROM `i_goods` WHERE `id_user`='".$api->Users->user_id."' ORDER BY `name` ASC");
			if (mysql_num_rows($s)>0)
			{
				?>
				<script type="text/javascript">							
					
					function chooseGood(name) 
					{ 
						close_window2(); 						
						jQuery("#document_product_<?=$id?>").val(name); choose_good(<?=$id?>); 
					}
					<? if (mysql_num_rows($s) > 15) { ?>
					function get_searchGoods()
					{
						if (jQuery("#search_goods").val().length > 2 || jQuery("#search_goods").val().length == 0)
						{	
							jQuery.ajax(
							{
								url: "/<?=$lang?>/documents/ajax.php",
								data: "do=getGoods&value="+jQuery("#search_goods").val()+"&x=secure",
								type: "POST",
								dataType : "html",
								cache: false,
								
								beforeSend: function()  {  },
								success: function(responseText)
								{							
									jQuery("#block_goods").empty();
									jQuery("#block_goods").html(responseText);
								},
								error: function()   {  }
							});
						}
					}
					<? } ?>			
				</script>
                <? if (mysql_num_rows($s) > 15) { ?>				
				<div class="search_code">
					<input type="text" id="search_goods" onkeyup="get_searchGoods();" placeholder="Поиск... - начните вводить наименование товара" />
				</div>
				<?				
				}
				echo '
				<div class="knp" id="block_goods">
					<table cellpadding="0" cellspacing="0" style="width:99%">
						<tr>
							<td><strong>№</strong></td>						
							<td><strong>Наименования товара</strong></td>
							<td><strong>Цена</strong></td>							
						</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{
				echo '
					<tr>
						<td>'.$i.'</td>
						<td><a title="Выбрать товара" onclick="chooseGood(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')">'.htmlspecialchars_decode($ob->pr_plus($r["name"])).'</td>
						<td><a title="Выбрать контрагента" onclick="chooseGood(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')">'.$r["price"].'</a></td>
					</tr>
					';
					
					$i++;
				}	
				echo '
					</table>
				</div>
				';
			}
		}
		
		// ОКНО С ТОВАРАМИ -> ПОСЛЕ ПОИСКА
		else if (
			($_POST['do'] == 'getGoods') &&
			(isset($_POST["value"]))
			)
		{
			$value = $ob->pr($_POST["value"]);
			
			$sql_search = "";
			if ($value != '')
				$sql_search = " AND (INSTR(`name`, '".$value."'))";
					
			$s=mysql_query("SELECT * FROM `i_goods` WHERE `id_user`='".$api->Users->user_id."'".$sql_search." ORDER BY `name` ASC");
			if (mysql_num_rows($s)>0)
			{
				echo '			
				<table cellpadding="0" cellspacing="0" style="width:99%">
					<tr>
						<td><strong>№</strong></td>						
						<td><strong>Наименования товара</strong></td>
						<td><strong>Цена</strong></td>							
					</tr>
				';
				
				$i=1;
				while($r=mysql_fetch_array($s))
				{
	
					echo '
					<tr>
						<td>'.$i.'</td>
						<td><a title="Выбрать товара" onclick="chooseGood(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')">'.htmlspecialchars_decode($ob->pr_plus($r["name"])).'</td>
						<td><a title="Выбрать контрагента" onclick="chooseGood(\''.str_replace("'", "\'", $ob->pr_plus($r["name"])).'\')">'.$r["price"].'</a></td>
					</tr>
					';
					
					$i++;
				}	
				echo '
				</table>			
				';
			}
		}		
	}
	else
	{
		echo '
		<script type="text/javascript">
			setTimeout(function() { self.location = "/"; }, 50);
		</script>
		';
	}
	
	exit;
}
?>