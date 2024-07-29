<?php
ini_set('memory_limit','-1');
ini_set( "display_errors", 1 );
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// require_once "../../../_inc.php";
require_once "../../../vendor/autoload.php";
require_once "../../../common/func.php";

$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Add header data
$sheet = $spreadsheet->getActiveSheet();

// 용지 방향
$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
// 용지 크기
$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
// 자동 맞춤
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

// 폰트사이즈
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

// 반복할 행
$sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);

$jno = $data["jno"];
$jobName = $data["jobName"];
$maxSeq = $data["maxSeq"];

// 헤더
$today = new DateTime();
$dateTime = $today->format('Y-m-d H:i');
$nowDate = $today->format('Y-m-d');
$sheet->setCellValue('A1', "JNO : " . $jno );
$sheet->setCellValue('C1', "PROJECT : " . $jobName);
$sheet->getStyle("A1:C1")->getFont()->setSize(12);
$sheet->setCellValue('G1', "※ Rslt# \"R\"일 때 회람횟수 미포함");
$sheet->getStyle("G1")->getFont()->getColor()->setRGB('974706');
$sheet->setCellValue('J1', "※ 차기접수일이 기준일시를 지난 경우 빨간색 표시");
$sheet->getStyle("J1")->getFont()->getColor()->setRGB('FF0000');
$sheet->setCellValue('N1', "기준일시 : " . $dateTime);

$sheet->setCellValue('A2', "공종");
$sheet->setCellValue('B2', "문서번호");
$sheet->setCellValue('C2', "Rev.");
$sheet->setCellValue('D2', "문서제목");
$sheet->setCellValue('E2', "Vendor");
$sheet->setCellValue('F2', "TR No.");
$sheet->setCellValue('G2', "Latest");
$sheet->mergeCells("G2:J2");
$sheet->setCellValue('G3', "Rslt#");
$sheet->setCellValue('H3', "회람일\n(From VD)");
$sheet->setCellValue('I3', "회신일\n(To VD)");
$sheet->setCellValue('J3', "차기 접수일\n(From VD)");
$sheet->setCellValue('K2', "회람\n횟수");
$sheet->setCellValue('L2', "RFQ. NO.");
$sheet->setCellValue('M2', "RFQ. Title");
$sheet->setCellValue('N2', "Item / Tag No.");

// 줄바꿈
$sheet->getStyle("H2:K3")->getAlignment()->setWrapText(true);

// 헤더 병합
$sheet->mergeCells("A2:A3");
$sheet->mergeCells("B2:B3");
$sheet->mergeCells("C2:C3");
$sheet->mergeCells("D2:D3");
$sheet->mergeCells("E2:E3");
$sheet->mergeCells("F2:F3");
// $sheet->mergeCells("G2:G3");
// $sheet->mergeCells("H2:H3");
// $sheet->mergeCells("I2:K2");
// $sheet->mergeCells("I2:I3");
// $sheet->mergeCells("J2:J3");
$sheet->mergeCells("K2:K3");
$sheet->mergeCells("L2:L3");
$sheet->mergeCells("M2:M3");
$sheet->mergeCells("N2:N3");

// 배포, 회신 history 헤더
function latestOrder($i) {
    if($i == 0) {
        return "Last";
    } else {
        return "L - " . $i;
    }
}
$lastCol = '';
for( $i=0, $col='O'; $i < $maxSeq; $i++,$col++) {
    $sheet->setCellValue($col."2", latestOrder($i));
    $nextCol = $col;
    $nextCol++;
    $sheet->setCellValue($col."3", "Rslt#");
    $sheet->setCellValue($nextCol."3", "회람일\n(From VD)");
    $sheet->getStyle("{$nextCol}3")->getAlignment()->setWrapText(true);
    $nextCol++;
    $sheet->setCellValue($nextCol."3", "회신일\n(To VD)");
    $sheet->mergeCells("{$col}2:{$nextCol}2");
    $sheet->getStyle("{$nextCol}3")->getAlignment()->setWrapText(true);
    $lastCol = $nextCol;
    $col++;
    $col++;
}

// 헤더 틀 고정
$spreadsheet->getActiveSheet()->freezePane("A4");

// 헤더 배경색 지정
$sheet->getStyle("A2:{$lastCol}3")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DCDCDC');

// 헤더 폰트 굵게
$sheet->getStyle("A1:{$lastCol}3")->getFont()->setBold(true);

$url = "http://vp.htenc.co.kr/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&jno={$jno}&mode=latest";

$curl = curl_init();

curl_setopt_array($curl, array(
        // CURLOPT_PORT => "80",
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: text/plain; charset=utf-8"
    ),
));

$response = curl_exec($curl);
    // $err = curl_error($curl);
curl_close($curl);

$responseResult = json_decode($response);

// 하이퍼 링크
$link_style_array = array(
    'font'  => array(
        'color' => array('rgb' => '0070C0'),
        // 'underline' => 'single'
    ),
);

// Final
$final_style_array = array(
    'font'  => array(
        'color' => array('rgb' => '0000FF'),
        // 'underline' => 'single'
    ),
);

if($responseResult->ResultType = "Success") {
    $rowCnt = 4;
    for($i=0; $i < count($responseResult->Value); $i++) {
    $latestData = $responseResult->Value;
        // 공종
        $sheet->setCellValue('A'.$rowCnt, $latestData[$i]->tr_func_cd);
        // 문서번호
        $sheet->setCellValue('B'.$rowCnt, $latestData[$i]->doc_num);
        // Rev.
        $sheet->setCellValue('C'.$rowCnt, $latestData[$i]->doc_rev_num);
        // 문서제목
        $sheet->setCellValue('D'.$rowCnt, $latestData[$i]->doc_title);
        // 제작사
        $sheet->setCellValue('E'.$rowCnt, $latestData[$i]->from_comp_name);
        // TR No.
        $sheet->setCellValue('F'.$rowCnt, $latestData[$i]->tr_doc_num);
        // Rslt#
        $sheet->setCellValue('G'.$rowCnt, $latestData[$i]->doc_status_nick);
        // 회람일
        $sheet->setCellValue('H'.$rowCnt, $latestData[$i]->doc_distribute_date_str);
        $sheet->getCell('H'.$rowCnt)->getHyperlink()->setUrl("https://vp.htenc.co.kr/pdfViewer.php?jno={$jno}&doc_no={$latestData[$i]->doc_no}&pdfPage=1&model=DOC_DE_DOWNLOAD");
        // 회신일
        $sheet->setCellValue('I'.$rowCnt, $latestData[$i]->doc_reply_date_str);
        if($latestData[$i]->doc_reply_date_str) {
            $sheet->getCell('I'.$rowCnt)->getHyperlink()->setUrl("https://vp.htenc.co.kr/pdfViewer.php?jno={$jno}&doc_no={$latestData[$i]->doc_no}&pdfPage=1&model=DOC_LE_DOWNLOAD");
        }
        // 차기 접수일
        $resultCode = $latestData[$i]->doc_status_nick;
        if($resultCode != "A" && $resultCode != "F") {
            $sheet->setCellValue('J'.$rowCnt, $latestData[$i]->doc_return_date_str);
            if($latestData[$i]->doc_return_date_str) {
                $returnDate = $latestData[$i]->doc_return_date_str;
    
                $nowDateStr = strtotime($nowDate);
                $returnDate = strtotime($returnDate);
                
                if($returnDate < $nowDateStr) {
                    $sheet->getStyle('J'.$rowCnt)->getFont()->getColor()->setARGB('FF0000');
                }
            }
        }
        // 회람 회수
        $sheet->setCellValue('K'.$rowCnt, $latestData[$i]->doc_cnt);
        // RFQ. No.
        $sheet->setCellValue('L'.$rowCnt, $latestData[$i]->doc_rfq_num);
        // RFQ. Title
        $sheet->setCellValue('M'.$rowCnt, $latestData[$i]->doc_rfq_title);
        // 아이템/태그
        $sheet->setCellValue('N'.$rowCnt, $latestData[$i]->doc_tag_item);

        $ms_no = $latestData[$i]->ms_no;

        // 회람일/회신일 history 목록
        $col='O';
        foreach($data["historyDateList"][$ms_no] as $value) {
            // Rslt#
            $sheet->setCellValue("{$col}{$rowCnt}", $value["doc_status_nick"]);
            $col++;
            // 회람일
            $sheet->setCellValue("{$col}{$rowCnt}", $value["hist_distribute_date_str"]);
            $sheet->getCell("{$col}{$rowCnt}")->getHyperlink()->setUrl("https://vp.htenc.co.kr/pdfViewer.php?jno={$jno}&doc_no={$value['doc_no']}&pdfPage=1&model=DOC_DE_DOWNLOAD");
            if($col == "P") {
                $sheet->getStyle("{$col}{$rowCnt}")->applyFromArray($link_style_array);
            }
            if($value["doc_status_nick"] == "R") {
                $sheet->getStyle("{$col}{$rowCnt}")->getFont()->setStrikethrough(true);
            }
            $col++;
            // 회신일
            $sheet->setCellValue("{$col}{$rowCnt}", $value["hist_reply_date_str"]);
            if($value["hist_reply_date_str"]) {
                $sheet->getCell("{$col}{$rowCnt}")->getHyperlink()->setUrl("https://vp.htenc.co.kr/pdfViewer.php?jno={$jno}&doc_no={$value['doc_no']}&pdfPage=1&model=DOC_LE_DOWNLOAD");
                if($col == "Q") {
                    $sheet->getStyle("{$col}{$rowCnt}")->applyFromArray($link_style_array);
                }
            }
            if($value["doc_status_nick"] == "R") {
                $sheet->getStyle("{$col}{$rowCnt}")->getFont()->setStrikethrough(true);
            }
            $col++;
        }

        // final 서식
        if($latestData[$i]->doc_status_nick == "F") {
            $sheet->getStyle("A{$rowCnt}:N{$rowCnt}")->applyFromArray($final_style_array);
        }
        // 하이퍼 링크
        $sheet->getStyle("G{$rowCnt}")->applyFromArray($link_style_array);
        $sheet->getStyle("H{$rowCnt}")->applyFromArray($link_style_array);
        $sheet->getStyle("I{$rowCnt}")->applyFromArray($link_style_array);
        $sheet->getStyle("O{$rowCnt}")->applyFromArray($link_style_array);
        $sheet->getStyle("P{$rowCnt}")->applyFromArray($link_style_array);
        $sheet->getStyle("Q{$rowCnt}")->applyFromArray($link_style_array);

        flush();
        $rowCnt++;
    }
}

// 10차수 이상일 경우 숨기기
// if($maxSeq > 10) {
//     for($col='AV'; true; $col++) {
//         // $sheet->getRowDimension($col)->setOutlineLevel(1);
//         $sheet->getColumnDimension($col)->setVisible(false);
//         if($col == $lastCol) {
//             break;
//         }
//     }
// }

// 들여쓰기
$sheet->getStyle('B3:B'.$rowCnt)->getAlignment()->setIndent(1);
$sheet->getStyle('D3:D'.$rowCnt)->getAlignment()->setIndent(1);
$sheet->getStyle('F3:F'.$rowCnt)->getAlignment()->setIndent(1);
$sheet->getStyle('M3:N'.$rowCnt)->getAlignment()->setIndent(1);
$sheet->getStyle('N1')->getAlignment()->setIndent(1);

// 자동 필터
$spreadsheet->getActiveSheet()->setAutoFilter("A3:{$lastCol}{$rowCnt}");

// 표 그리기
$rowCnt--;
$sheet->getStyle("A2:{$lastCol}{$rowCnt}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

// 헤더 칼럼 가운데 정렬
$sheet->getStyle("A2:{$lastCol}3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// 행 가운데 정렬
$sheet->getStyle('A4:A'.$rowCnt)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('C4:C'.$rowCnt)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('E4:E'.$rowCnt)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('G4:K'.$rowCnt)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("O4:{$lastCol}{$rowCnt}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// 셀 높이
$sheet->getRowDimension(1)->setRowHeight(15);
for($i = 2; $i <= $rowCnt; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(-1);
}

// 텍스트 맞춤
$sheet->getStyle("A1:{$lastCol}{$rowCnt}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

//자동 줄바꿈
$sheet->getStyle('A3:M'.$rowCnt)->getAlignment()->setWrapText(true);

// 칼럼 사이즈 자동 조정
$sheet->getColumnDimension('A')->setWidth(6);
$sheet->getColumnDimension('B')->setAutoSize(true);
// $sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(5);
$sheet->getColumnDimension('D')->setAutoSize(true);
// $sheet->getColumnDimension('D')->setWidth(50);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(28);
$sheet->getColumnDimension('G')->setWidth(9);
$sheet->getColumnDimension('H')->setWidth(12);
$sheet->getColumnDimension('I')->setWidth(12);
$sheet->getColumnDimension('J')->setWidth(12);
$sheet->getColumnDimension('K')->setWidth(9);
$sheet->getColumnDimension('L')->setWidth(20);
// $sheet->getColumnDimension('K')->setAutoSize(true);
$sheet->getColumnDimension('M')->setAutoSize(true);
$sheet->getColumnDimension('N')->setWidth(60);

// 회람일/회신일 history 너비
for($col='O'; true; $col++) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
    if($col == $lastCol) {
        break;
    }
}

// 확대/축소
$sheet->getSheetView()->setZoomScale(90);

// 파일명
$title = $jno . "_VDCS_Latest_List";

// Rename worksheet
$sheet->setTitle($title);
// Redirect output to a client’s web browser (Excel2007)
@header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//IE EDGE
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') !== FALSE)) {
    $title = rawurlencode($title);
    @header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
    @header('Cache-Control: private, no-transform, no-store, must-revalidate');
    @header('Pragma: no-cache');
}
//IE
else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) {
    $title = iconv("UTF-8","EUC-KR", $title);
    @header('Content-Disposition: attachment;filename=' . $title . '.xlsx');
    @header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    @header('Pragma: public'); // HTTP/1.0
}
else {
    @header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
    @header('Cache-Control: private, no-transform, no-store, must-revalidate');
    @header('Pragma: no-cache');
}
@header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
@header('Cache-Control: max-age=1');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
?>
