<?php defined('BASEPATH') or exit('No direct script access allowed');

class Printlist extends CI_Model
{
    /**
     * Print pdf
     *
     * @param array $data
     * @return buffer $pdf
     */
    public function createPdf($data, $shouldPrintDonorInfo = true)
    {
        $style = $this->pdf->style;
        $b = config_item('b');
        $h1 = config_item('h1');
        $this->pdf->initPdf();
        $this->pdf->createDocument(true);
        $this->pdf->createPage();
        $this->pdf->addTextFlow("移植後経過情一覧", null, array_merge($h1, $style['center']));
        $this->pdf->addTextFlow("作成日：", 740, array_merge($b, $style['right']));
        $this->pdf->addTextFlow(date("Y/m/d"), 80, $style['ml_0']);

        $headerArray = array("登録者ID", "カナ氏名漢字氏名", "入力対象経過期間", "報告期限日", array(array("入力状況"), array("生活状況", "検査項目")), "", "生年月日", "年齢", "性別", "移植臓器", "同時移植", "移植実施日", "移植回数", "移植施設", "移植後経過情報管理施設", "臓器転帰", "患者転帰");
        if ($shouldPrintDonorInfo) {
            array_push($headerArray, "ドナーID");
        }
        $this->session->userdata("account")->account_type_mst_id == ACC_TYPE_TP && array_pop($headerArray);
        $dataArray = array(
            $headerArray,
            array(),
        );
        foreach ($data as $info) {
            array_push($dataArray, array(
                sprintf('%07d', $info->RECIPIENT_ID),
                $info->KANA_NAME . $info->KANJI_NAME,
                $info->elapsedPeriod,
                datetimeToString($info->REPORT_DEADLINE),
                $info->living_conditions,
                $info->inspection_item,
                datetimeToString($info->BIRTHDAY),
                empty($info->BIRTHDAY) ? "" : date_diff(date_create($info->BIRTHDAY), date_create('now'))->y,
                $info->sex,
                $info->organ,
                $info->DOUJI_ISHOKU,
                datetimeToString($info->ISYOKU_DATE),
                $info->ISYOKU_CNT,
                $info->transplant_name,
                $info->transfer_destination_name,
                $info->organ_outcome,
                $info->patient_outcome,
                sprintf('%07d', $info->DONOR_ID),
            ));
        }

        $this->pdf->addTable(
            array(
                "colNum" => count($headerArray),
                "colWidth" => array(
                    "header" => array("9%", "9%", "9%", "12%", "9%", null, "12%", "5%", "5%", null, null, "12%", "5%", "15%", "15%", "8%", "8%", "9%"),
                ),
                "colSpan" => array(
                    "header" => array(1, 1, 1, 1,
                        array(
                            array(2),
                            array(1, 1),
                        ),
                    ),
                ),
                "rowSpan" => array(
                    "header" => array(2, 2, 2, 2,
                        array(
                            array(1),
                            array(1, 1),
                        ),
                    ),
                ),
                "marginTop" => 10,
                "headerHeight" => LINE_HEIGHT * 1.2,
            ),
            $dataArray
        );

        $this->pdf->endPage();
        $this->pdf->endDocument();
        return $this->pdf->getBuffer();
    }
}
