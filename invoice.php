<?php	

namespace PhpOffice\PhpSpreadsheet\Style;
require 'vendor/autoload.php';	
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$rate 					 = ($rUser["user_nds"]==1 ? '12%' : 'без НДС');
$number 			     = intval($rDoc['number']);
$date				     = $rDoc['date'];
$date_text 				 = $api->Strings->date($lang,$date,'sql','datetext');
$dogovor 			     = stripslashes($rDoc['dogovor']);
$cash_condition			 = stripslashes($rDoc['terms_payment']);
$address 			     = stripslashes($rDoc['adress']);
$trust	 				 = stripslashes($rDoc['postavka']);
$delivery    			 = stripslashes($rDoc['administration']);
$bill 			     	 = stripslashes($rDoc['nakladn']);
$accounter_name		   	 = stripslashes($rDoc['accountant']);
$position_customer 		 = stripslashes($rDoc['position_compiler']);
$name_customer     		 = stripslashes($rDoc['fio_compiler']);
$iik_customer 		   	 = intval($rDoc['bankAccount']);

$bank_num = '';
if ($iik_customer>1)	$bank_num = $iik_customer;

$sClient = mysql_fetch_array(mysql_query("SELECT * FROM `i_client` WHERE `id`='".$rDoc["id_client"]."'"));

$bin 		= substr($sClient["bin"], 0, 12);
$name 	 	= htmlspecialchars_decode($ob->pr_plus($sClient["name"]), ENT_QUOTES);
$address 	= stripslashes($sClient["adres"]);
$iik 		= $sClient['iik'];
$bik 		= $sClient['bik'];
$bank 	 	= htmlspecialchars_decode(stripslashes($sClient['bank']), ENT_QUOTES);
$name_dest	= $name;
$address2 	= $address; 
$bin_dest   = $bin;
if ($rDoc["id_client"] != $rDoc["id_client2"]){
	$sClient2 = mysql_fetch_array(mysql_query("SELECT * FROM `i_client` WHERE `id`='".$rDoc["id_client2"]."'"));
	$bin_dest 		= $sClient2["bin"];
	$name_dest 		= htmlspecialchars_decode($ob->pr_plus($sClient2["name"]), ENT_QUOTES);
	$address2 	= stripslashes($sClient2["adres"]);
}
$objPHPExcel = new Spreadsheet();
$styleFontBold = array(
	'font' => array(
		'bold' => true,
		'size' => 14
	)
);
$styleFontBold12 = array(
	'font' => array(
		'bold' => true,
		'size' => 11
	)
);
$styleFontBold10 = array(
	'font' => array(
		'bold' => true,
		'size' => 10
	)
);
$styleFont12 = array(
	'font' => array(
		'size' => 11
	)
);
$styleFont11 = array(
	'font' => array(
		'size' => 11
	)
);
$styleFontItalic8 = array(
	'font' => array(
		'italic' => true,
		'size' => 8
	)
);
$styleFontItalic10 = array(
	'font' => array(
		'italic' => true,
		'size' => 10
	)
);
$styleFontItalic9 = array(
	'font' => array(
		'italic' => true,
		'size' => 9
	)
);
$styleFont8 = array(
	'font' => array(
		'size' => 8
	)
);
$BorderBottomThick = array(
	'borders' => array(
		'bottom' => array(
			'borderStyle' => Border::BORDER_THICK,
			'color' => array('argb' => '000'),
		),
	)
);
$BorderBottomThin = array(
	'borders' => array(
		'bottom' => array(
			'borderStyle' => Border::BORDER_THIN,
			'color' => array('argb' => '000'),
		),
	)
);
$styleBorderThin = array(
	'borderStyle' => Border::BORDER_THIN,
			'color' => array('argb' => '000')
		);
$styleBorder = array(
	'borders' => array(
		'top' => array(
			'borderStyle' => Border::BORDER_THIN,
			'color' => array('argb' => '000'),
		),
		'bottom' => array(
			'borderStyle' => Border::BORDER_THIN,
			'color' => array('argb' => '000'),
		),
		'right' => array(
			'borderStyle' => Border::BORDER_THIN,
			'color' => array('argb' => '000'),
		),
		'left' => array(
			'borderStyle' => Border::BORDER_THIN,
			'color' => array('argb' => '000'),
		)
	)
);
$styleCenter = array(						
	'alignment' => array(
		'horizontal' => Alignment::HORIZONTAL_CENTER,
		'vertical' => Alignment::VERTICAL_CENTER,
		'wrapText' => TRUE
	)
);
$styleCenterNoWrap = array(						
	'alignment' => array(
		'horizontal' => Alignment::HORIZONTAL_CENTER,
	)
);
$styleRight = array(						
	'alignment' => array(
		'horizontal' => Alignment::HORIZONTAL_RIGHT,
		// 'vertical' => Alignment::VERTICAL_JUSTIFY,
		// 'wrapText' => TRUE
	)
);
$styleLeft = array(						
	'alignment' => array(
		'horizontal' => Alignment::HORIZONTAL_LEFT,
		'wrapText' => TRUE
	)
);
$styleLeftVCenter = array(						
	'alignment' => array(
		'horizontal' => Alignment::HORIZONTAL_LEFT,
		'vertical' => Alignment::VERTICAL_JUSTIFY,
		'wrapText' => TRUE
	)
);
$styleVCenter = array(						
	'alignment' => array(
		'vertical' => Alignment::VERTICAL_CENTER
	)
);
$styleWrap = array(						
	'alignment' => array(
		'wrapText' => TRUE
	)
);

$page = $objPHPExcel->setActiveSheetIndex(0);
$page->setTitle('Счёт фактура');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(9);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(45);

//ГРАНИЦЫ
$page->getStyle('A3:K3')->applyFromArray($BorderBottomThin);
$page->getStyle('A4:K4')->applyFromArray($BorderBottomThin);
$page->getStyle('A5:K5')->applyFromArray($BorderBottomThin);
$page->getStyle('A6:K6')->applyFromArray($BorderBottomThin);
$page->getStyle('A7:K7')->applyFromArray($BorderBottomThin);
$page->getStyle('A8:K8')->applyFromArray($BorderBottomThin);
$page->getStyle('A8:K8')->applyFromArray($BorderBottomThin);
$page->getStyle('A11:K11')->applyFromArray($BorderBottomThin);
$page->getStyle('A10:K10')->applyFromArray($BorderBottomThin);
$page->getStyle('A12:K12')->applyFromArray($BorderBottomThin);
$page->getStyle('A13:K13')->applyFromArray($BorderBottomThin);
$page->getStyle('A15:K15')->applyFromArray($BorderBottomThin);
$page->getStyle('A17:K17')->applyFromArray($BorderBottomThin);
$page->getStyle('A18:K18')->applyFromArray($BorderBottomThin);
$page->getStyle('A19:K19')->applyFromArray($BorderBottomThin);


// ВЕРХУШКА ТАБЛИЦЫ С ПРИЛОЖЕНИЕМ
$page->getStyle('A1:K1')->applyFromArray($styleFont8);
$page->getRowDimension(4)->setRowHeight(20);

$page->mergeCells('A1:H1');
$page->setCellValue('I1', 'Приложение 1 к приказу 
Министра государственных доходов 
Республики Казахстан
от 14 июля 2000г. №712');
$page->mergeCells('I1:K1');
$page->getStyle('I1')->applyFromArray($styleVCenter);
$page->getStyle('I1')->applyFromArray($styleRight);
$page->getStyle('I1')->applyFromArray($styleWrap);

//ЗАГОЛОВОК
$page->setCellValue('A2', 'Счёт-фактура №'.$number.' от '.$date_text);
$page->mergeCells('A2:K2');

//СТИЛЬ ЗАГОЛОВКА
$page->getStyle('A2:K2')->applyFromArray($styleFontBold);
$page->getStyle('A2:K2')->applyFromArray($styleCenter);

//ПОСТАВЩИК
$page->setCellValue('A3', 'Поставщик: '.htmlspecialchars_decode(stripslashes($rUser["user_name"]), ENT_QUOTES));
$page->mergeCells('A3:J3');
//СТИЛЬ
$page->getStyle('A3:K3')->applyFromArray($styleFontBold12);
$page->getStyle('A3:J3')->applyFromArray($styleLeft);

//БИН и адрес
$page->setCellValue('A4', 'БИН и адрес местонахождения поставщика: '.$rUser["user_iin"].', '.htmlspecialchars_decode(stripslashes($rUser["user_adres"]), ENT_QUOTES));
$page->mergeCells('A4:J4');
//СТИЛЬ
$page->getStyle('A4:K4')->applyFromArray($styleFont12);
$page->getStyle('A4:J4')->applyFromArray($styleLeft);

//РАСЧЁТНЫЙ СЧЁТ
$page->setCellValue('A5', 'Расчётный счёт поставщика: '.$rUser["user_iik".$bank_num].', в банке: '.htmlspecialchars_decode(stripslashes($rUser["user_bank".$bank_num])).', БИК:'.$rUser["user_bik".$bank_num]);
$page->mergeCells('A5:J5');
//СТИЛЬ
$page->getStyle('A5:K5')->applyFromArray($styleFont12);
$page->getStyle('A5:J5')->applyFromArray($styleLeft);

//ДОГОВОР
$page->setCellValue('A6', 'Договор (контракт) на поставку товаров (работ, услуг): '.$dogovor);
$page->mergeCells('A6:J6');
//СТИЛЬ
$page->getStyle('A6:K6')->applyFromArray($styleFont12);
$page->getStyle('A6:J6')->applyFromArray($styleLeft);

//УСЛОВИЯ ОПЛАТЫ
$page->setCellValue('A7', 'Условия оплаты по договору (контракту): '.$cash_condition);
$page->mergeCells('A7:J7');
//СТИЛЬ
$page->getStyle('A7:K7')->applyFromArray($styleFont12);
$page->getStyle('A7:J7')->applyFromArray($styleLeft);

//РАЗДЕЛ ПУНКТ НАЗНАЧЕНИЯ
$page->setCellValue('A8', 'Пункт назначения поставляемых товаров (работ, услуг):'.$address);
$page->mergeCells('A8:J8');
$page->setCellValue('A9', 'государство, регион, область, город, район');
$page->mergeCells('A9:J9');
//СТИЛЬ
$page->getStyle('A8:K7')->applyFromArray($styleFont12);
$page->getStyle('A8:J8')->applyFromArray($styleLeft);
$page->getStyle('A9:K9')->applyFromArray($styleFontItalic8);
$page->getStyle('A9:J9')->applyFromArray($styleCenter);

//ДОВЕРЕННОСТЬ
$page->setCellValue('A10', 'Поставка товаров (работ,услуг) осуществлена по доверенности:'.$trust);
$page->mergeCells('A10:J10');
//СТИЛЬ
$page->getStyle('A10:K10')->applyFromArray($styleFont12);
$page->getStyle('A10:J10')->applyFromArray($styleLeft);

//ДОСТАВКА
$page->setCellValue('A11', 'Способ отправления: '.$delivery);
$page->mergeCells('A11:J11');
//СТИЛЬ
$page->getStyle('A11:K11')->applyFromArray($styleFont12);
$page->getStyle('A11:J11')->applyFromArray($styleLeft);

//НАКЛАДНАЯ
$page->setCellValue('A12', 'Товарно-транспортная накладная: № '.$bill_number);
$page->mergeCells('A12:J12');
//СТИЛЬ
$page->getStyle('A12:K12')->applyFromArray($styleFont12);
$page->getStyle('A12:J12')->applyFromArray($styleLeft);

//ГРУЗООТПРАВИТЕЛЬ
$page->setCellValue('A13', 'Грузоотправитель: БИН:'
		.$rUser["user_iin"].', '.htmlspecialchars_decode(stripslashes($rUser["user_name"]), ENT_QUOTES).', '.htmlspecialchars_decode(stripslashes($rUser["user_adres"]), ENT_QUOTES));
$page->mergeCells('A13:J13');
$page->setCellValue('A14', '(БИН, наименование и адрес)');
$page->mergeCells('A14:J14');
//СТИЛЬ
$page->getStyle('A13:K13')->applyFromArray($styleFont12);
$page->getStyle('A13:J13')->applyFromArray($styleLeft);
$page->getStyle('A14:K14')->applyFromArray($styleFontItalic8);
$page->getStyle('A14:J14')->applyFromArray($styleCenter);

//ГРУЗОПОЛУЧАТЕЛЬ
$page->setCellValue('A15', 'Грузополучатель: БИН:'.$bin_dest.', '.$name_dest.', '.$address2);
$page->mergeCells('A15:J15');
$page->setCellValue('A16', '(БИН, наименование и адрес)');
$page->mergeCells('A16:J16');
//СТИЛЬ
$page->getStyle('A15:K15')->applyFromArray($styleFont12);
$page->getStyle('A15:J15')->applyFromArray($styleLeft);
$page->getStyle('A16:K16')->applyFromArray($styleFontItalic8);
$page->getStyle('A16:J16')->applyFromArray($styleCenter);

//ПОКУПАТЕЛЬ
$page->setCellValue('A17', 'Покупатель: '.$name_dest);
$page->mergeCells('A17:J17');
$page->setCellValue('A18', 'БИН и адрес местонахождения покупателя: БИН: '.$bin_dest.', '.$address2);
$page->mergeCells('A18:J18');
$page->setCellValue('A19', 'Расчетный счёт покупателя: '.$iik_customer);
$page->mergeCells('A19:J19');
//СТИЛЬ
$page->getStyle('A17:K17')->applyFromArray($styleFont12);
$page->getStyle('A17:J17')->applyFromArray($styleLeft);
$page->getStyle('A18:K18')->applyFromArray($styleFont12);
$page->getStyle('A18:J18')->applyFromArray($styleLeft);
$page->getStyle('A19:K19')->applyFromArray($styleFont12);
$page->getStyle('A19:J19')->applyFromArray($styleLeft);

//КОЛОНКА С НУМЕРАЦИЕЙ ПУНКТОВ
$page->setCellValue('K3', '[2]');
$page->setCellValue('K4', '[2а]');
$page->setCellValue('K5', '[2б]');
$page->setCellValue('K6', '[3]');
$page->setCellValue('K7', '[4]');
$page->setCellValue('K10', '[5]');
$page->setCellValue('K11', '[6]');
$page->setCellValue('K12', '[7]');
$page->setCellValue('K13', '[8]');
$page->setCellValue('K15', '[9]');
$page->setCellValue('K17', '[10]');
$page->setCellValue('K18', '[10а]');
$page->setCellValue('K19', '[10б]');
//СТИЛЬ
$page->getStyle('K3:K19')->applyFromArray($styleRight);

//ТАБЛИЦА
//ШАПКА ТАБЛИЦЫ
$page->setCellValue('A20', '(№ п/п)');
$page->mergeCells('A20:A21');
$page->setCellValue('B20', 'Наименование товаров (работ, услуг)');
$page->mergeCells('B20:B21');
$page->setCellValue('C20', 'Ед.изм.');
$page->mergeCells('C20:C21');
$page->setCellValue('D20', 'Кол-во (объем)');
$page->mergeCells('D20:D21');
$page->setCellValue('E20', 'Цена тенге');
$page->mergeCells('E20:E21');
$page->setCellValue('F20', 'Стоимость товаров (работ, услуг) без НДС');
$page->mergeCells('F20:F21');
$page->setCellValue('G20', 'НДС');
$page->mergeCells('G20:H20');
$page->setCellValue('G21', 'Ставка');
$page->setCellValue('H21', 'Сумма');
$page->setCellValue('I20', 'Всего стоимость реализации');
$page->mergeCells('I20:I21');
$page->setCellValue('J20', 'Акциз');
$page->mergeCells('J20:K20');
$page->setCellValue('J21', 'Ставка');
$page->setCellValue('K21', 'Сумма');
//СТИЛЬ
$page->getStyle('A20:K22')->applyFromArray($styleCenter);
$page->getRowDimension(20)->setRowHeight(60);
$page->getRowDimension(21)->setRowHeight(30);
for ($i = 1; $i < 12; $i++) {
    $page->setCellValueByColumnAndRow($i, 22, $i);
    $page->getStyleByColumnAndRow($i, 22)->applyFromArray($styleCenter);
    $page->getStyleByColumnAndRow($i, 22)->applyFromArray($styleFontItalic9);
}

//ЗАПОЛНЕНИЕ ТАБЛИЦЫ ПОЗИЦИЙ
$sum = 0;
$num_positions = 0;
$goods = explode("|-|", $rDoc["goods_info"]);

foreach($goods as $k=>$v){
	$i = $k + 1;
	if ($v!=''){
		$gg = explode('|=|', $v);	
		
		$product 	= htmlspecialchars_decode($ob->pr_plus($gg[0]), ENT_QUOTES);
		$count 		= floatval($gg[1]);
		$unit 		= stripslashes($gg[2]);
		$price 		= floatval($gg[3]);
																		
		if ($product!=''){										
			if ($rate=='12%')
				$price = $price / 1.12;							
				
			$sum = $sum + ($count*$price);
			$num_positions++;
			
			$real = ($rate=='12%' ? ($count*$price)*1.12 : ($count*$price));
			$all_real = $all_real + $real;
			
			$nds = ($rate=='12%' ? $real - ($count*$price) : '0');
			$all_nds = $all_nds + $nds;
			
			$page->setCellValueByColumnAndRow(1, $i+22, $i);
			$page->setCellValueByColumnAndRow(2, $i+22, $product);
			$page->setCellValueByColumnAndRow(3, $i+22, $unit);
			$page->setCellValueByColumnAndRow(4, $i+22, $count);
			$page->setCellValueByColumnAndRow(5, $i+22, number_format($price, 2, ',', ' '));
			$page->setCellValueByColumnAndRow(6, $i+22, number_format(round(($count*$price), 2), 2, ',', ' '));
			$page->setCellValueByColumnAndRow(7, $i+22, $rate);
			$page->setCellValueByColumnAndRow(8, $i+22, ($rate=='12%' ? number_format($nds, 2, ',', ' ') : ''));
			$page->setCellValueByColumnAndRow(9, $i+22, number_format($real, 2, ',', ' '));
			$page->setCellValueByColumnAndRow(10, $i+22, '');
			$page->setCellValueByColumnAndRow(11, $i+22, '');
		}
	}
}

$sum = round($sum, 2);
$sum_goods = $sum;
if ($rUser["user_nds"]==1)
	$sum_goods = $all_real;						

//FOOTER таблицы
$page->setCellValue('A'.(23+$num_positions), 'Всего по счету:');
$page->setCellValue('F'.(23+$num_positions), number_format($sum, 2, ',', ' '));
$page->setCellValue('H'.(23+$num_positions), number_format($all_nds, 2, ',', ' '));
$page->setCellValue('I'.(23+$num_positions), number_format($all_real, 2, ',', ' '));
$page->mergeCells('A'.(23+$num_positions).':E'. (23+$num_positions));
$page->getStyle('A'.(23+$num_positions).':K'. (23+$num_positions))->applyFromArray($styleFontBold10);
$page->getStyle('A'.(23+$num_positions))->applyFromArray($styleLeft);
$page->getStyle('F'.(23+$num_positions))->applyFromArray($styleRight);
$page->getStyle('G'.(23+$num_positions))->applyFromArray($styleFontBold10);
$page->getStyle('G'.(23+$num_positions))->applyFromArray($styleRight);
$page->getStyle('J'.(23+$num_positions))->applyFromArray($styleFontBold10);
$page->getStyle('J'.(23+$num_positions))->applyFromArray($styleRight);

//ГРАНИЦЫ ТАБЛИЦЫ 
$page->getStyle('A20:K'.(23+$num_positions))->getBorders()->getAllBorders()->applyFromArray($styleBorderThin);
//ВЫРАВНИВАНИЕ ТАБЛИЦЫ
$page->getStyle('D23:F'.(22+$num_positions))->applyFromArray($styleRight);
$page->getStyle('C23:C'.(22+$num_positions))->applyFromArray($styleCenter);
$page->getStyle('H23:K'.(23+$num_positions))->applyFromArray($styleRight);
$page->getStyle('A23:A'.(22+$num_positions))->applyFromArray($styleCenter);
$page->getStyle('B23:B'.(22+$num_positions))->applyFromArray($styleLeftVCenter);
$page->getStyle('D23:K'.(22+$num_positions))->applyFromArray($styleVCenter);
$page->getStyle('G23:G'.(22+$num_positions))->applyFromArray($styleCenter);
$page->getStyle('J23:J'.(22+$num_positions))->applyFromArray($styleCenter);
//ШРИФТ ТАБЛИЦЫ
$page->getStyle('A23:K'.(22+$num_positions))->applyFromArray($styleFont11);

//ВЫСОТА ЯЧЕЕК ТАБЛИЦЫ
for($r = 23; $r < (23+$num_positions); $r++){
	$page->getRowDimension($r)->setRowHeight(30);	
}

//ПОДПИСИ
$page->setCellValue('A'.(25+$num_positions), 'Руководитель организации: '.htmlspecialchars_decode(stripslashes($rUser["user_fio"])));
$page->mergeCells('A'.(25+$num_positions).':F'.(25+$num_positions));
$page->getStyle('A'.(25+$num_positions))->applyFromArray($styleFontBold12);

$page->mergeCells('A'.(26+$num_positions).':E'.(26+$num_positions));
$page->getStyle('A'.(26+$num_positions).':E'.(26+$num_positions))->applyFromArray($BorderBottomThick);

$page->mergeCells('A'.(27+$num_positions).':E'.(27+$num_positions));
$page->setCellValue('A'.(27+$num_positions), '(Ф.И.О., подпись)');
$page->getStyle('A'.(27+$num_positions))->applyFromArray($styleFontItalic8);

$page->setCellValue('H'.(25+$num_positions), 'ВЫДАЛ (ответственное лицо поставщика)');
$page->mergeCells('H'.(25+$num_positions).':K'.(25+$num_positions));
$page->getStyle('H'.(25+$num_positions))->applyFromArray($styleFontBold12);

$page->mergeCells('H'.(26+$num_positions).':K'.(26+$num_positions));
$page->setCellValue('H'.(26+$num_positions), $position_customer);
$page->getStyle('H'.(26+$num_positions).':K'.(26+$num_positions))->applyFromArray($BorderBottomThick);

$page->mergeCells('H'.(27+$num_positions).':K'.(27+$num_positions));
$page->setCellValue('H'.(27+$num_positions), '(должность)');
$page->getStyle('H'.(27+$num_positions))->applyFromArray($styleFontItalic8);

//МЕСТО ПЕЧАТИ
$page->mergeCells('F'.(26+$num_positions).':G'.(28+$num_positions));
$page->setCellValue('F'.(26+$num_positions), 'М.П.');
$page->getStyle('F'.(26+$num_positions))->applyFromArray($styleFont11);
$page->getStyle('F'.(26+$num_positions))->applyFromArray($styleCenter);
$page->getStyle('A'.(27+$num_positions))->applyFromArray($styleCenter);
$page->getStyle('H'.(27+$num_positions))->applyFromArray($styleCenter);

$page->setCellValue('A'.(29+$num_positions), 'Бухгалтер: '.$accounter_name);
$page->mergeCells('A'.(29+$num_positions).':E'.(29+$num_positions));
$page->getStyle('A'.(29+$num_positions))->applyFromArray($styleFontBold12);

$page->mergeCells('A'.(30+$num_positions).':E'.(30+$num_positions));
$page->getStyle('A'.(30+$num_positions).':E'.(30+$num_positions))->applyFromArray($styleCenter);
$page->getStyle('A'.(30+$num_positions).':E'.(30+$num_positions))->applyFromArray($BorderBottomThick);
$page->mergeCells('A'.(31+$num_positions).':E'.(31+$num_positions));
$page->setCellValue('A'.(31+$num_positions), '(Ф.И.О., подпись)');
$page->getStyle('A'.(31+$num_positions))->applyFromArray($styleFontItalic8);
$page->getStyle('A'.(31+$num_positions))->applyFromArray($styleCenter);

$page->mergeCells('H'.(30+$num_positions).':K'.(30+$num_positions));
$page->getStyle('H'.(30+$num_positions).':K'.(30+$num_positions))->applyFromArray($BorderBottomThick);

$page->mergeCells('H'.(31+$num_positions).':K'.(31+$num_positions));
$page->setCellValue('H'.(31+$num_positions), '(Ф.И.О., подпись)');
$page->getStyle('H'.(31+$num_positions))->applyFromArray($styleFontItalic8);
$page->getStyle('H'.(31+$num_positions))->applyFromArray($styleCenter);


//ПРИМЕЧАНИЕ
$page->mergeCells('A'.(32+$num_positions).':K'.(32+$num_positions));
$page->setCellValue('A'.(32+$num_positions), 'Примечание: Без печати не действительно. Оригинал (первый экземпляр) - покупателю. Копия (второй экземпляр) - поставщику.');
$page->getStyle('A'.(32+$num_positions))->applyFromArray($styleFont8);
$page->getStyle('A'.(32+$num_positions))->applyFromArray($styleLeft);


$writer = new Xls($objPHPExcel);
$writer->save($_SERVER['DOCUMENT_ROOT']."/upload/invoice/".$name_file);

?>