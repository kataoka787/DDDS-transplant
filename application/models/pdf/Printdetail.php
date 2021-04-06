<?php defined('BASEPATH') or exit('No direct script access allowed');

class Printdetail extends CI_Model
{
    /**
     * Create pdf
     *
     * @param array $primaryKeysArray
     * @param boolean $shouldPrintDonorInfo
     * @return pdf $pdf
     */
    public function createPdf($primaryKeysArray, $shouldPrintDonorInfo = true)
    {
        $settings = $this->init($shouldPrintDonorInfo);
        foreach ($primaryKeysArray as $primaryKeys) {
            $primaryKeys = explode(",", $primaryKeys);
            if (count($primaryKeys) != 3) {
                continue;
            }
            $printData = $this->Tishokugokeika->getForPdfPrint($primaryKeys[0], $primaryKeys[1], $primaryKeys[2]);
            if (empty($printData)) {
                continue;
            }
            $this->printPdf($settings, $printData);
        }
        return $this->finalize();
    }

    /**
     * Init pdf print function
     *
     * @param boolean $shouldPrintDonorInfo
     * @return array $settings
     */
    private function init($shouldPrintDonorInfo = true)
    {
        $settings = array(
            "style" => $this->pdf->style,
            "b" => config_item('b'),
            "b_first" => config_item('b_first_30'),
            "h1" => config_item('h1'),
            "h3" => config_item('h3'),
            "inspectionColMax" => 13,
            "shouldPrintDonorInfo" => $shouldPrintDonorInfo,
        );

        $this->pdf->initPdf();
        $this->pdf->createDocument();
        return $settings;
    }

    /**
     * Print pdf
     *
     * @param array $settings
     * @param object $printData
     * @return void
     */
    private function printPdf($settings, $printData)
    {
        $currentTime = new DateTime();
        extract($settings);
        $this->pdf->createPage();

        /* Inspection table count */
        $inspections = $this->Tkensa->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT);
        $tableCount = ceil((min(config_item("max_cycle_year"), $this->Tkensa->getMaxCycle($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT)) + 3) / config_item('inspection_result_max_column'));

        /* Print report name and created date */
        $this->printHeader($h1, $b, $style, $currentTime, $printData);

        /* Print recipient individual info 個人情報 */
        $this->printRecipientIndividualInfo($h3, $b_first, $b, $style, $currentTime, $printData);

        /* Print underlying disease (原疾患)*/
        $this->printUnderlyingDisease($b_first, $printData);

        /* 膵臓 */
        $printData->ZOKI_CODE == ORGAN_PANCREAS && $this->printTransplantContent($b_first, $b, $style, $printData);

        $this->pdf->addTextFlow("コメント", 60, array_merge($b_first, $style['ml_50']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->GENSIKKAN_CMNT, 200);

        /* 肺 */
        $printData->ZOKI_CODE == ORGAN_LUNG && $this->printTransplantContent($b_first, $b, $style, $printData);

        /* Print follow up institution name */
        $this->pdf->addTextFlow("移植後経過情報管理施設", 110, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->FOLLOW_UP_INSTITUTION_NAME, 200);

        $this->pdf->addLine(false);

        $this->pdf->addTextFlow("◆情報１", 80, $h3);

        /* Print donor info (ドナー情報) */
        $this->printDonorInfo($b_first, $b, $style, $currentTime, $shouldPrintDonorInfo, $printData);

        /* Print hospital leaving date */
        $this->pdf->addTextFlow("退院年月日", 60, array_merge($b_first, $style['ml_20']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->TAIIN_DATE), 60);

        /* Print immunosuppressant drugs (免疫抑制剤) */
        $this->printImmunosuppressantDrug($b_first, $b, $style, $printData);

        /* Print rejection (拒否反応) */
        $this->printRejectionTherapeuticEffect($b_first, $style, $printData);

        $this->pdf->addLine(false);

        $this->pdf->addTextFlow("◆情報２", 80, $h3);

        /* 心臓, 肝臓, 腎臓, 膵臓, 小腸 */
        if ($printData->ZOKI_CODE != ORGAN_LUNG) {
            /* Print inspection result (検査項目) */
            $tableCount == 1 && $this->printInspectionResult($b_first, $style, $tableCount, $inspections);
        }

        /* Print complication (合併症) */
        $this->printComplication($b_first, $style, $printData);

        /* 肺 */
        if ($printData->ZOKI_CODE == ORGAN_LUNG) {
            $this->pdf->addTextFlow("在宅酸素療法", 60, array_merge($b_first, $style['ml_20'], $style['mt_5']));
            $this->pdf->addTextFlow(":", 10);
            $this->pdf->addTextFlow("導入年月日", 50, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->ZAITAKUSANSORYOHO_START_DATE), 80);
            $this->pdf->addTextFlow("離脱年月日", 50, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->ZAITAKUSANSORYOHO_END_DATE), 80);
        }

        /* Print organ status (臓器の転帰) */
        $this->printOrganStatus($b_first, $b, $style, $printData);

        /* Print patient status (患者の転帰) */
        $this->printPatientStatus($b_first, $b, $style, $printData);

        /* 肺 */
        if ($printData->ZOKI_CODE == ORGAN_LUNG) {
            /* Print inspection result (検査項目) */
            $tableCount == 1 && $this->printInspectionResult($b_first, $style, $tableCount, $inspections);
        }
        $this->pdf->endPage();

        /* Print inspection result (検査項目) in new page */
        if ($tableCount > 1) {
            $this->pdf->createPage();
            /* Print report name and created date */
            $this->printHeader($h1, $b, $style, $currentTime, $printData);
            /* Print recipient individual info (個人情報) */
            $this->printRecipientIndividualInfo($h3, $b_first, $b, $style, $currentTime, $printData);
            /* Print underlying disease (原疾患) */
            $this->printUnderlyingDisease($b_first, $printData);
            /* 膵臓 */
            $printData->ZOKI_CODE == ORGAN_PANCREAS && $this->printTransplantContent($b_first, $b, $style, $printData);
            $this->pdf->addTextFlow("コメント", 60, array_merge($b_first, $style['ml_50']));
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->GENSIKKAN_CMNT, 200);
            /* 肺 */
            $printData->ZOKI_CODE == ORGAN_LUNG && $this->printTransplantContent($b_first, $b, $style, $printData);
            /* Print follow up institution name */
            $this->pdf->addTextFlow("移植後経過情報管理施設", 110, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->FOLLOW_UP_INSTITUTION_NAME, 200);
            $this->pdf->addLine(false);
            $this->pdf->addTextFlow("◆情報２", 80, $h3);
            $this->printInspectionResult($b_first, $style, $tableCount, $inspections);
            $this->pdf->endPage();

        }

        /* Print living conditions (生活状況) in new page */
        $livingConditions = $this->Tliving->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT);
        if (!empty($livingConditions)) {
            $this->pdf->startIndexPage();
            $this->pdf->createPage();
            /* Print page header */
            $this->printHeader($h1, $b, $style, $currentTime, $printData);
            /* Print recipient individual info (個人情報) */
            $this->printRecipientIndividualInfo($h3, $b_first, $b, $style, $currentTime, $printData);
            /* Print underlying disease (原疾患) */
            $this->printUnderlyingDisease($b_first, $printData);
            /* 膵臓 */
            $printData->ZOKI_CODE == ORGAN_PANCREAS && $this->printTransplantContent($b_first, $b, $style, $printData);
            $this->pdf->addTextFlow("コメント", 60, array_merge($b_first, $style['ml_50']));
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->GENSIKKAN_CMNT, 200);
            /* 肺 */
            $printData->ZOKI_CODE == ORGAN_LUNG && $this->printTransplantContent($b_first, $b, $style, $printData);
            /* Print follow up institution name */
            $this->pdf->addTextFlow("移植後経過情報管理施設", 110, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->FOLLOW_UP_INSTITUTION_NAME, 200);
            $this->pdf->addLine(false);
            $this->pdf->addTextFlow("◆情報２", 80, $h3);
            /* Print rehabiliation (社会復帰) */
            $this->printRehabilitation($b_first, $b, $style, $printData);
            /* Set create page trigger */
            $this->pdf->setCreatePageTrigger(function ($h1, $b_first, $b, $style, $currentTime, $printData) {
                /* Print page header */
                $this->printHeader($h1, $b, $style, $currentTime, $printData);
                $this->pdf->addTextFlow("生活状況", 45, array_merge($b_first, $style['ml_20'], $style['mt_5']));
                $this->pdf->addTextFlow(":", 5);
            }, array($h1, $b_first, $b, $style, $currentTime, $printData));
            /* Print living conditions (生活状況) */
            $this->printLivingCoditions($b_first, $style, $livingConditions);
            $this->pdf->endPage();
            $this->pdf->stopIndexPage();
            $this->pdf->unsetCreatePageTrigger();
        }
    }

    /**
     * Print page header
     *
     * @param array $h1
     * @param array $b
     * @param array $style
     * @param datetime $currentTime
     * @param object $printData
     * @return void
     */
    public function printHeader($h1, $b, $style, $currentTime, $printData)
    {
        $this->pdf->addTextFlow("移植後経過情報（" . (ORGAN[$printData->ZOKI_CODE] ?? null) . "）", null, array_merge($h1, $style['center']));
        $this->pdf->addTextFlow("作成日：", 525, array_merge($b, $style['right']));
        $this->pdf->addTextFlow($currentTime->format(DATE_TIME_DEFAULT), 50, $style['ml_0']);
    }

    /**
     * Print recipient individual info (個人情報)
     *
     * @param array $h3
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param datetime $currentTime
     * @param object $printData
     * @return void
     */
    private function printRecipientIndividualInfo($h3, $b_first, $b, $style, $currentTime, $printData)
    {
        $this->pdf->addTextFlow("◆個人情報", 80, $h3);

        $this->pdf->addTextFlow("登録者ID", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(str_pad($printData->RECIPIENT_ID, 7, "0", STR_PAD_LEFT), 100);

        $this->pdf->addTextFlow("氏名", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->KANJI_NAME, 100);

        $this->pdf->addTextFlow("性別", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(SEX_SHORT[$printData->SEX] ?? "", 80);
        $this->pdf->addTextFlow("生年月日", 50, array_merge($b_first, $style['row']));
        $this->pdf->addTextFlow(":", 5);
        $birthday = new DateTime($printData->BIRTHDAY);
        $this->pdf->addTextFlow($birthday->format(DATE_TIME_DEFAULT), 85);
        $this->pdf->addTextFlow($currentTime->diff($birthday)->y, 15);
        $this->pdf->addTextFlow("歳", 20, $b);

        $this->pdf->addTextFlow("移植年月日", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->ISYOKU_DATE), 80);
        $this->pdf->addTextFlow("移植回数", 50, array_merge($b_first, $style['row']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->ISYOKU_CNT, 10);
        $this->pdf->addTextFlow("回", 70, $b);
        $this->pdf->addTextFlow("移植時年齢", 60, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->ISYOKU_AGE ?? "", 15);
        $this->pdf->addTextFlow("歳", 20, $b);

        $this->pdf->addTextFlow("移植実施施設", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->TRANSPLANT_INSTITUTION_NAME, 200);
    }

    /**
     * Print underlying disease (原疾患)
     *
     * @param array $b_first
     * @param object $printData
     * @return void
     */
    private function printUnderlyingDisease($b_first, $printData)
    {
        /* 肺 */
        if ($printData->ZOKI_CODE == ORGAN_LUNG) {
            $this->pdf->addTextFlow("原疾患（大分類）", 80, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->GENSIKKAN_H, 200);

            $this->pdf->addTextFlow("原疾患（小分類）", 80, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->GENSIKKAN_L, 200);
        } else {
            $this->pdf->addTextFlow("原疾患", 80, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $gensikkan = $this->Mcd->getByCodeTypeCode("017", sprintf("%02d", $printData->GENSIKKAN));
            $this->pdf->addTextFlow(empty($gensikkan) ? null : $gensikkan->VALUE, 200);
        }
    }

    /**
     * Print transplant content (移植内容)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printTransplantContent($b_first, $b, $style, $printData)
    {
        /* 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_PANCREAS) {
            $this->pdf->addTextFlow("移植内容", 40, $b);
            $ishokunaiyou = $this->Mcd->getByCodeTypeCode("076", $printData->ISYOKU_NAIYO);
        } else { /* 肺 */
            $this->pdf->addTextFlow("移植内容", 80, array_merge($b_first, $style['ml_50']));
            $ishokunaiyou = $this->Mcd->getByCodeTypeCode("072", $printData->ISYOKU_NAIYO);
        }
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(empty($ishokunaiyou) ? null : $ishokunaiyou->VALUE, 200);
    }

    /**
     * Print donor info (ドナー情報)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param Datetime $currentTime
     * @param object $printData
     * @return void
     */
    private function printDonorInfo($b_first, $b, $style, $currentTime, $shouldPrintDonorInfo, $printData)
    {
        $this->pdf->addTextFlow("ドナー情報", 80, array_merge($b_first, $style['ml_20'], $style['mt_0']));
        if ($shouldPrintDonorInfo) {
            $this->pdf->addTextFlow("ドナーID", 45, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(str_pad($printData->DONOR_ID, 7, "0", STR_PAD_LEFT), 120);
            $this->pdf->addTextFlow("ドナー発生地", 80, array_merge($b, $style['ml_0']));
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($this->Prefmst->getPrefNameById($printData->DONOR_TODOFUKEN), 100);

            $this->pdf->addTextFlow("氏名", 45, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->DONOR_KANJI_NAME, 250);

            $this->pdf->addTextFlow("性別", 45, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(SEX_SHORT[$printData->DONOR_SEX] ?? "", 115);
            $this->pdf->addTextFlow("生年月日", 80, $b);
            $this->pdf->addTextFlow(":", 5);
            $donorBirthday = new DateTime($printData->DONOR_BIRTHDAY);
            $this->pdf->addTextFlow($donorBirthday->format("Y/m/d"), 80);
            $this->pdf->addTextFlow($currentTime->diff($donorBirthday)->y, 15);
            $this->pdf->addTextFlow("歳", 20, array_merge($b, $style['ml_0']));
        }

        $this->pdf->addTextFlow("血液型", 45, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $ABO = $this->Mcd->getByCodeTypeCode("013", $printData->DONOR_BLOOD_ABO);
        $this->pdf->addTextFlow(empty($ABO) ? null : $ABO->VALUE . "型", 115);
        $this->pdf->addTextFlow("RH型", 80, $b);
        $this->pdf->addTextFlow(":", 5);
        $RH = $this->Mcd->getByCodeTypeCode("014", $printData->DONOR_BLOOD_RH);
        $this->pdf->addTextFlow(empty($RH) ? null : $RH->VALUE, 80);

        $this->pdf->addTextFlow("サイズ", 45, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow("身長", 25, $b);
        $this->pdf->addTextFlow($printData->HEIGHT, 30, $style['right']);
        $this->pdf->addTextFlow("cm", 50, $b);
        $this->pdf->addTextFlow("体重", 80, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->WEIGHT, 30);
        $this->pdf->addTextFlow("kg", 25, $b);

        /* 腎臓, 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_KIDNEY || $printData->ZOKI_CODE == ORGAN_PANCREAS) {
            $this->pdf->addTextFlow("摘出条件", 80, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $exportCondition = $this->Mcd->getByCodeTypeCode("082", $printData->TEKISYUTU_JOKEN);
            $this->pdf->addTextFlow(empty($exportCondition) ? null : $exportCondition->VALUE, 60);
        }

        $this->pdf->addTextFlow("手術開始日時", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->SYUJUTU_START_DATETIME, DATE_TIME_LONG_HIJP), 120);

        $this->pdf->addTextFlow("摘出開始日時", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->REMOVE_DATETIME, DATE_TIME_LONG_HIJP), 120);

        $this->pdf->addTextFlow("大動脈遮断日時", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->AORTICBLOCK_DATETIME, DATE_TIME_LONG_HIJP), 120);

        $this->pdf->addTextFlow("灌流開始日時", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->PERFUSION_START_DATETIME, DATE_TIME_LONG_HIJP), 120);

        $this->pdf->addTextFlow("血流再開日時", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->KETURYUSAIKAI_DATETIME, DATE_TIME_LONG_HIJP), 120);

        $this->pdf->addTextFlow("手術終了日時", 80, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->SYUJUTU_END_DATETIME, DATE_TIME_LONG_HIJP), 120);

        /* TODO Confirm specification */
        /* 心臓, 肺 */
        if ($printData->ZOKI_CODE == ORGAN_HEART || $printData->ZOKI_CODE == ORGAN_LUNG) {
            $this->pdf->addTextFlow("", 215, $b_first);
        } else { /* 肝臓, 腎臓, 膵臓, 小腸 */
            $this->pdf->addTextFlow("温阻血時間", 80, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->ONSOKETU_MINUTE, 10);
            $this->pdf->addTextFlow("分", 110, $b);
        }
        $this->pdf->addTextFlow("全阻血時間", 60, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->ZENSOKETU_HOUR, 10);
        $this->pdf->addTextFlow("時間", 20, $b);
        $this->pdf->addTextFlow($printData->ZENSOKETU_MINUTE, 10);
        $this->pdf->addTextFlow("分", 20, $b);

        $this->pdf->addTextFlow("死因", 40, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $deathCause = $this->Mcd->getByCodeTypeCode("165", $printData->DONOR_SHIIN_NAI);
        $this->pdf->addTextFlow(empty($deathCause) ? null : $deathCause->VALUE, 240);

        $this->pdf->addTextFlow("コメント", 40, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $comment = $this->Mcd->getByCodeTypeCode("166", $printData->DONOR_SHIIN_GAI);
        $this->pdf->addTextFlow($comment->VALUE ?? "", 250);

        /* 腎臓, 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_KIDNEY || $printData->ZOKI_CODE == ORGAN_PANCREAS) {
            $this->pdf->addTextFlow("透析", 60, array_merge($b_first, $style['ml_20']));
            $this->pdf->addTextFlow(":", 5);
            $dialysis​ = $this->Mcd->getByCodeTypeCode("087", $printData->TOSEKIRIDATU);
            $this->pdf->addTextFlow($dialysis​->VALUE ?? "", 80);
            $this->pdf->addTextFlow("最終透析日", 50, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->TOSEKI_LAST_DATE), 80);
            $this->pdf->addTextFlow("原因", 20, $b);
            $this->pdf->addTextFlow(":", 5);
            $reason = $this->Mcd->getByCodeTypeCode("088", $printData->TOSEKIRIDATU_FUNOGENIN);
            $this->pdf->addTextFlow($reason->VALUE ?? "", 80);
        }

        /* 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_PANCREAS) {
            $this->pdf->addTextFlow("インスリン治療", 60, array_merge($b_first, $style['ml_20']));
            $this->pdf->addTextFlow(":", 5);
            $insurin = $this->Mcd->getByCodeTypeCode('087', $printData->INSULIN_FLG);
            $this->pdf->addTextFlow($insurin->VALUE ?? "", 80);
            $this->pdf->addTextFlow("最終投与日", 50, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->INSULIN_LAST_DATE), 80);
            $this->pdf->addTextFlow("原因", 20, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow("技術的要因", 80);
        }
    }

    /**
     * Print immunosuppressant drugs (免疫抑制剤)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printImmunosuppressantDrug($b_first, $b, $style, $printData)
    {
        $this->pdf->addTextFlow("免疫抑制剤（導入）", 90, array_merge($b_first, $style['ml_20']));
        $this->pdf->addTextFlow(":", 5);

        $drugCodeMSt = $this->Mcd->getCodeValueArrayByCodeType("189");
        $rowItemCount = 0;
        foreach (array_keys(config_item("introduction_immunosuppressant_drugs")) as $drug) {
            $dataColumnName = "DONYU_" . strtoupper($drug);
            $shouldCheck = ($drugCodeMSt[$printData->$dataColumnName] ?? "") == M_CD_CODE_TYPE_189_VALUE_HAVE ? "＊" : "　";
            $drug = in_array($drug, config_item("discontinued_drug")) ? "$drug" . "△" : $drug;
            $rowItemCount++;
            if ($rowItemCount % 8 == 1) {
                $this->pdf->addTextFlow("$shouldCheck $drug", 60, array_merge($b_first, $style['ml_50']));
                continue;
            }
            $this->pdf->addTextFlow("$shouldCheck $drug", 60, $b);
        }
        $this->pdf->addTextFlow("　△：製造中止", 80, $b);
        $this->pdf->addTextFlow("　その他", 40, array_merge($b_first, $style['ml_50']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->DONYU_ETC, 250);

        $this->pdf->addTextFlow("免疫抑制剤（維持）", 90, array_merge($b_first, $style['ml_20']));
        $this->pdf->addTextFlow(":", 5);

        $isFirst = true;
        foreach (array_keys(config_item("maintain_immunosuppressant_drugs")) as $drug) {
            $dataColumnName = "IJI_" . strtoupper($drug);
            $shouldCheck = ($drugCodeMSt[$printData->$dataColumnName] ?? "") == M_CD_CODE_TYPE_189_VALUE_HAVE ? "＊" : "　";
            $rowItemCount++;
            if ($isFirst) {
                $isFirst = false;
                $this->pdf->addTextFlow("$shouldCheck $drug", 60, array_merge($b_first, $style['ml_50']));
                continue;
            }
            $this->pdf->addTextFlow("$shouldCheck $drug", 60, $b);
        }
        $this->pdf->addTextFlow("　その他", 40, array_merge($b_first, $style['ml_50']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->IJI_ETC, 250);
    }

    /**
     * Print rejection therapeutic effect (拒否反応)
     *
     * @param array $b_first
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printRejectionTherapeuticEffect($b_first, $style, $printData)
    {
        /* 心臓, 肝臓, 腎臓, 膵臓, 小腸 */
        if ($printData->ZOKI_CODE != ORGAN_LUNG) {
            $this->pdf->addTextFlow("拒否反応", 50, array_merge($b_first, $style['ml_20'], $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
        }

        /* 心臓, 腎臓, 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_HEART || $printData->ZOKI_CODE == ORGAN_KIDNEY || $printData->ZOKI_CODE == ORGAN_PANCREAS) {
            $rejections = $this->Trejection->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT, REJECTION_COMMON);
            if (!empty($rejections)) {
                $rejectionInfo = array(
                    array("No.", "診断日", "治療手段", "治療効果"),
                );

                $rejectionCount = 0;
                foreach ($rejections as $rejection) {
                    $rejectionCount++;
                    $tiryousyudan = $this->Mcd->getByCodeTypeCode("097", $rejection->TIRYOU_SYUDAN);
                    $tiryousyudan = empty($tiryousyudan) ? "" : $tiryousyudan->VALUE;
                    if ($rejection->ZOKI_CODE == ORGAN_HEART) {
                        $tiryoukoka = $this->Mcd->getByCodeTypeCode("216", $rejection->TIRYOU_KOKA);
                    } else {
                        $tiryoukoka = $this->Mcd->getByCodeTypeCode("098", $rejection->TIRYOU_KOKA);
                    }
                    $tiryoukoka = empty($tiryoukoka) ? "" : $tiryoukoka->VALUE;
                    array_push($rejectionInfo, array(
                        $rejectionCount,
                        datetimeToString($rejection->SINDAN_DATE),
                        $tiryousyudan,
                        $tiryoukoka,
                    ));
                }
                $this->pdf->addTable(
                    array(
                        "colNum" => 4,
                        "colWidth" => array(
                            "header" => array("10%", "20%", "50%", "20%"),
                        ),
                        "marginLeft" => 80,
                        "width" => 400,
                    ),
                    $rejectionInfo,
                );
            }
        }

        /* 肝臓, 小腸 */
        if ($printData->ZOKI_CODE == ORGAN_LIVER || $printData->ZOKI_CODE == ORGAN_SMALL_INTENSTINE) {
            $rejections = $this->Trejection->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT, REJECTION_COMMON);
            if (!empty($rejections)) {
                $rejectionInfo = array(
                    array("No.", "診断日", "治療効果"),
                );

                $rejectionCount = 0;
                foreach ($rejections as $rejection) {
                    $rejectionCount++;
                    $tiryousyudan = $this->Mcd->getByCodeTypeCode("097", $rejection->TIRYOU_SYUDAN);
                    $tiryousyudan = empty($tiryousyudan) ? "" : $tiryousyudan->VALUE;
                    $tiryoukoka = $this->Mcd->getByCodeTypeCode("098", $rejection->TIRYOU_KOKA);
                    $tiryoukoka = empty($tiryoukoka) ? "" : $tiryoukoka->VALUE;
                    array_push($rejectionInfo, array(
                        $rejectionCount,
                        datetimeToString($rejection->SINDAN_DATE),
                        $tiryousyudan,
                        $tiryoukoka,
                    ));
                }
                $this->pdf->addTable(
                    array(
                        "colNum" => 3,
                        "colWidth" => array(
                            "header" => array("10%", "45%", "45%"),
                        ),
                        "marginLeft" => 80,
                        "width" => 300,
                    ),
                    $rejectionInfo,
                );
            }
        }

        /* 肺 */
        if ($printData->ZOKI_CODE == ORGAN_LUNG) {
            $this->pdf->addTextFlow("急性拒否反応", 50, array_merge($b_first, $style['ml_20'], $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            $rejections = $this->Trejection->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT, REJECTION_ACUTE);
            if (!empty($rejections)) {
                $rejectionInfo = array(
                    array("No.", "診断日", "GradeA", "GradeB", "GradeC", "GradeD", "治療効果"),
                );

                $rejectionCount = 0;
                foreach ($rejections as $rejection) {
                    $rejectionCount++;
                    $gradeA = $this->Mcd->getByCodeTypeCode("099", $rejection->GRADEA);
                    $gradeA = empty($gradeA) ? "" : $gradeA->VALUE;
                    $gradeB = $this->Mcd->getByCodeTypeCode("099", $rejection->GRADEB);
                    $gradeB = empty($gradeB) ? "" : $gradeB->VALUE;
                    $gradeC = $this->Mcd->getByCodeTypeCode("099", $rejection->GRADEC);
                    $gradeC = empty($gradeC) ? "" : $gradeC->VALUE;
                    $gradeD = $this->Mcd->getByCodeTypeCode("099", $rejection->GRADED);
                    $gradeD = empty($gradeD) ? "" : $gradeD->VALUE;
                    $tiryoukoka = $this->Mcd->getByCodeTypeCode("098", $rejection->TIRYOU_KOKA);
                    $tiryoukoka = empty($tiryoukoka) ? "" : $tiryoukoka->VALUE;

                    array_push($rejectionInfo, array(
                        $rejectionCount,
                        datetimeToString($rejection->SINDAN_DATE),
                        $gradeA,
                        $gradeB,
                        $gradeC,
                        $gradeD,
                        $tiryoukoka,
                    ));
                }
                $this->pdf->addTable(
                    array(
                        "colNum" => 7,
                        "colWidth" => array(
                            "header" => array("5%", "15%", "10%", "10%", "10%", "10%", "30%"),
                        ),
                        "marginLeft" => 80,
                        "width" => 500,
                    ),
                    $rejectionInfo,
                );
            }

            $this->pdf->addTextFlow("慢性拒否反応", 60, array_merge($b_first, $style['ml_20'], $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            $rejections = $this->Trejection->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT, REJECTION_CHRONIC);
            if (!empty($rejections)) {
                $rejectionInfo = array(
                    array("No.", "診断日", "Stage", "a/b", "治療効果"),
                );

                $rejectionCount = 0;
                foreach ($rejections as $rejection) {
                    $rejectionCount++;
                    $stage = $this->Mcd->getByCodeTypeCode("103", $rejection->STAGE);
                    $stage = empty($stage) ? "" : $stage->VALUE;
                    $ab = $this->Mcd->getByCodeTypeCode("104", $rejection->A_B);
                    $ab = empty($ab) ? "" : $ab->VALUE;

                    $tiryoukoka = $this->Mcd->getByCodeTypeCode("098", $rejection->TIRYOU_KOKA);
                    $tiryoukoka = empty($tiryoukoka) ? "" : $tiryoukoka->VALUE;
                    array_push($rejectionInfo, array(
                        $rejectionCount,
                        datetimeToString($rejection->SINDAN_DATE),
                        $stage,
                        $ab,
                        $tiryoukoka,
                    ));
                }
                $this->pdf->addTable(
                    array(
                        "colNum" => 5,
                        "colWidth" => array(
                            "header" => array("7%", "22%", "17%", "17%", "50%"),
                        ),
                        "marginLeft" => 80,
                        "width" => 400,
                    ),
                    $rejectionInfo,
                );
            }
        }
    }

    /**
     * Print inspection result (検査項目)
     *
     * @param array $b_first
     * @param array $style
     * @param int $tableCount
     * @param object $inspections
     * @return void
     */
    private function printInspectionResult($b_first, $style, $tableCount, $inspections)
    {
        $inspectionCycleName = $this->Mcd->getCodeValueArrayByCodeType("105");
        $this->pdf->addTextFlow("検査項目", 50, array_merge($b_first, $style['ml_20'], $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        if (!empty($inspections)) {
            $header = array(
                "",
                "検査項目",
                "単位",
                $inspectionCycleName["M1"],
                $inspectionCycleName["M3"],
                $inspectionCycleName["M6"],
            );
            for ($i = 1; $i <= config_item('inspection_result_max_column') - 3; $i++) {
                array_push($header, $inspectionCycleName[sprintf('%02d', $i)]);
            }
            $inspectionPrintData = array(
                $header,
            );
            foreach ($inspections as $index => $inspection) {
                $rowData = array(
                    $index + 1,
                    $inspection->KENSA_NAME,
                    $inspection->KENSA_UNIT,
                    $inspection->KENSA_VALUE_M1,
                    $inspection->KENSA_VALUE_M3,
                    $inspection->KENSA_VALUE_M6,
                );
                for ($i = 1; $i <= config_item('inspection_result_max_column') - 3; $i++) {
                    $inspectionValueCycle = "KENSA_VALUE_" . sprintf('%02d', $i);
                    array_push($rowData, $inspection->$inspectionValueCycle);
                }
                array_push($inspectionPrintData, $rowData);
            }
            $this->pdf->addTable(
                array(
                    "colNum" => count($header),
                    "colWidth" => array(
                        "header" => array("5%", "13%"),
                    ),
                    "marginLeft" => 30,
                ),
                $inspectionPrintData
            );
            if ($tableCount > 1) {
                $currentCycle = config_item('inspection_result_max_column') - 3;
                for ($i = 2; $i <= $tableCount; $i++) {
                    $currentMaxYear = $currentCycle + config_item('inspection_result_max_column');
                    $header = array("", "検査項目", "単位");
                    for ($j = $currentCycle; $j < $currentMaxYear; $j++) {
                        array_push($header, ($j + 1) <= config_item('max_cycle_year') ? $inspectionCycleName[sprintf('%02d', $j + 1)] : "");
                    }
                    $inspectionPrintData = array(
                        $header,
                    );
                    foreach ($inspections as $index => $inspection) {
                        $row = array($index + 1, $inspection->KENSA_NAME, $inspection->KENSA_UNIT);
                        for ($j = $currentCycle; $j < $currentMaxYear; $j++) {
                            $inspectionColumn = 'KENSA_VALUE_' . ($j + 1);
                            array_push($row, property_exists($inspection, $inspectionColumn) ? $inspection->$inspectionColumn : "");
                        }
                        array_push($inspectionPrintData, $row);
                    }
                    $currentCycle = $currentMaxYear;
                    $this->pdf->addTable(
                        array(
                            "colNum" => count($header),
                            "colWidth" => array(
                                "header" => array("5%", "13%"),
                            ),
                            "marginLeft" => 30,
                        ),
                        $inspectionPrintData
                    );
                }
            }
        }
    }

    /**
     * Print complication (合併症)
     *
     * @param array $b_first
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printComplication($b_first, $style, $printData)
    {
        $this->pdf->addTextFlow("入院を要する合併症", 100, array_merge($b_first, $style['ml_20'], $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);

        $complications = $this->Tgappei->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT);
        if (!empty($complications)) {
            $complicationCodes = array(
                ORGAN_HEART => "090",
                ORGAN_LUNG => "091",
                ORGAN_LIVER => "092",
                ORGAN_KIDNEY => "093",
                ORGAN_PANCREAS => "094",
                ORGAN_SMALL_INTENSTINE => "095",
            );
            $complicationInfo = array(
                array("No.", "合併症", "入院日", "退院日", "コメント"),
            );
            $complicationCount = 0;
            foreach ($complications as $complication) {
                $complicationCount++;
                $complicationName = $this->Mcd->getByCodeTypeCode($complicationCodes[$printData->ZOKI_CODE], $complication->GAPPEI);
                $complicationName = empty($complicationName) ? "" : $complicationName->VALUE;
                array_push($complicationInfo, array(
                    $complicationCount,
                    $complicationName,
                    datetimeToString($complication->NYUIN_DATE),
                    datetimeToString($complication->TAIIN_DATE),
                    $complication->CMNT,
                ));
            }
            $this->pdf->addTable(
                array(
                    "colNum" => 5,
                    "colWidth" => array(
                        "header" => array("5%", "15%", "15%", "15%", "50%"),
                    ),
                    "marginLeft" => 30,
                ),
                $complicationInfo
            );
        }
    }

    /**
     * Print rehabiliation (社会復帰)
     *
     * @param array $p_first
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printRehabilitation($b_first, $b, $style, $printData)
    {
        $this->pdf->addTextFlow("退院年月日", 55, array_merge($b_first, $style['ml_20'], $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->TAIIN_DATE), 120);
        $this->pdf->addTextFlow("社会復帰", 45, array_merge($b_first, $style['ml_20'], $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        switch ($printData->ZOKI_CODE) {
            case ORGAN_HEART:
                $rehabilitationCode = "212";
                break;
            case ORGAN_LUNG:
                $rehabilitationCode = "213";
                break;
            case ORGAN_LIVER:
                $rehabilitationCode = "214";
                break;
            default:
                $rehabilitationCode = "215";
                break;
        }
        $this->pdf->addTextFlow($this->Mcd->getCodeValueArrayByCodeType($rehabilitationCode)[$printData->SYAKAIFUKKI] ?? "", 250);
        $this->pdf->addTextFlow("", 50, $b_first);
        $this->pdf->addTextFlow("社会復帰日", 55, $b);
        $this->pdf->addTextFlow(":", 5, $b);
        $this->pdf->addTextFlow(datetimeToString($printData->SYAKAIFUKKI_DATE), 250);
        $this->pdf->addTextFlow("", 50, $b_first);
        $this->pdf->addTextFlow("コメント", 55, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->SYAKAIFUKKI_NAIYO, 250);
    }

    /**
     * Print living conditions (生活状況)
     *
     * @param array $b_first
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printLivingCoditions($b_first, $style, $livingConditions)
    {
        $reportFormMst = $this->Mcd->getCodeValueArrayByCodeType("217");
        $this->pdf->addTextFlow("生活状況", 45, array_merge($b_first, $style['ml_20'], $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        if (!empty($livingConditions)) {
            $livingConditionPrintData = array(
                array("No.", "記録日", "確認者", "報告者", "報告形式", "生活状況"),
            );
            foreach ($livingConditions as $index => $livingCondition) {
                array_push($livingConditionPrintData, array(
                    $index + 1,
                    datetimeToString($livingCondition->INPUT_DATE),
                    $livingCondition->KAKUNIN_USER_NAME,
                    $livingCondition->REPORT_USER_NAME,
                    $reportFormMst[$livingCondition->REPORT_FORM] ?? "",
                    $livingCondition->LIVING_NAIYO,
                ));
            }
            $this->pdf->addTable(
                array(
                    "colNum" => 6,
                    "colWidth" => array(
                        "header" => array("5%", "13%", "10%", "10%", "10%", "50%"),
                    ),
                    "marginLeft" => 30,
                ),
                $livingConditionPrintData
            );
        }
    }

    /**
     * Print organ status (臓器の転帰)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printOrganStatus($b_first, $b, $style, $printData)
    {
        $this->pdf->addTextFlow("臓器の転帰", 50, array_merge($b_first, $style['ml_20'], $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);

        $statusList = $this->Mcd->getByCodeType("080");
        $isFirst = true;
        foreach ($statusList as $status) {
            $shouldCheck = $printData->ZOKI_TENKI === $status->CODE ? "＊" : "　";
            if ($isFirst) {
                $isFirst = false;
                $this->pdf->addTextFlow($shouldCheck . "　$status->VALUE", 60, array_merge($b_first, $style['ml_50'], $style['mt_0']));
                continue;
            }
            $this->pdf->addTextFlow($shouldCheck . "　$status->VALUE", 60, $b);
        }

        $this->pdf->addTextFlow("機能廃絶日", 60, array_merge($b_first, $style['ml_50'], $style['mt_0']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->KINOHAIZETU_DATE), 60);

        $this->pdf->addTextFlow("原因", 40, array_merge($b_first, $style['ml_50'], $style['mt_0']));
        $this->pdf->addTextFlow(":", 15);

        $reasonList = $this->Mcd->getByCodeType("086");
        $rowItemCount = 0;
        foreach ($reasonList as $reason) {
            $shouldCheck = $printData->ZOKI_TENKI_GENIN == $reason->CODE ? "＊" : "　";
            /* New line when reach 4 items/row */
            if ($rowItemCount == 4) {
                $this->pdf->addTextFlow($shouldCheck . "　" . $reason->VALUE, 110, array_merge($b_first, ['m-left' => 115], $style['mt_0']));
                $rowItemCount = 1;
                continue;
            }
            $this->pdf->addTextFlow($shouldCheck . "　" . $reason->VALUE, 110, $b);
            $rowItemCount++;
        }

        $this->pdf->addTextFlow("コメント", 40, array_merge($b_first, $style['ml_50'], $style['mt_0']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->ZOKI_TENKI_CMNT, 250);
    }

    /**
     * Print patient status (患者の転帰)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printPatientStatus($b_first, $b, $style, $printData)
    {
        $this->pdf->addTextFlow("患者の転帰", 50, array_merge($b_first, $style['ml_20'], $style['mt_0']));
        $this->pdf->addTextFlow(":", 5);

        $statusList = $this->Mcd->getByCodeType("077");
        $isFirst = true;
        /* 心臓 */
        if ($printData->ZOKI_CODE == ORGAN_HEART) {
            foreach ($statusList as $status) {
                $printStyle = $b;
                $shouldCheck = $printData->RECIPIENT_TENKI == $status->CODE ? "＊" : "　";
                if ($isFirst) {
                    $isFirst = false;
                    $printStyle = array_merge($b_first, $style['ml_50'], $style['mt_0']);
                }
                if ($status->VALUE == "生存") {
                    $this->pdf->addTextFlow($shouldCheck . "　生存（", 50, $printStyle);
                    $detailStatusList = $this->Mcd->getByCodeType("081");
                    foreach ($detailStatusList as $detailStatus) {
                        $shouldCheck = $printData->RECIPIENT_TENKI_DETAIL == $detailStatus->CODE ? "＊" : "　";
                        $this->pdf->addTextFlow($shouldCheck . "　" . $detailStatus->VALUE, mb_strlen($detailStatus->VALUE) > 3 ? 80 : 50, $b);
                    }
                    $this->pdf->addTextFlow(")", 10, $b);
                    continue;
                }
                $this->pdf->addTextFlow($shouldCheck . "　" . $status->VALUE, 60, $b);
            }
        } else { /* 肺, 肝臓, 腎臓, 膵臓, 小腸 */
            foreach ($statusList as $status) {
                $shouldCheck = $printData->RECIPIENT_TENKI == $status->CODE ? "＊" : "　";
                if ($isFirst) {
                    $this->pdf->addTextFlow($shouldCheck . "　" . $status->VALUE, 60, array_merge($b_first, $style['ml_50'], $style['mt_0']));
                    $isFirst = false;
                    continue;
                }
                $this->pdf->addTextFlow($shouldCheck . "　" . $status->VALUE, 60, $b);
            }
        }

        $this->pdf->addTextFlow("死亡日", 40, array_merge($b_first, $style['ml_50'], $style['mt_0']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->SIBO_DATE), 60);
        $this->pdf->addTextFlow("死因", 25, array_merge($b, $style['ml_10']));
        $this->pdf->addTextFlow(":", 5);
        $mainReason = $this->Mcd->getByCodeTypeCode("078", $printData->SIIN_H);
        $mainReason = empty($mainReason) ? "" : $mainReason->VALUE;
        $additionReason = $this->Mcd->getByCodeTypeCode("079", $printData->SIIN_L);
        $additionReason = empty($additionReason) ? "" : $additionReason->VALUE;
        $this->pdf->addTextFlow($mainReason . "　　　" . $additionReason, 200, $style['ml_10']);

        $this->pdf->addTextFlow("コメント", 40, array_merge($b_first, $style['ml_50'], $style['mt_0']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->RECIPENT_TENKI_CMNT, 300);

        /* 肝臓 */
        if ($printData->ZOKI_CODE == ORGAN_LIVER) {
            $this->pdf->addTextFlow("最終生存確認日", 70, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->FINAL_LIV_DATE), 80);
        }
    }

    /**
     * Finalize and return pdf buffer
     *
     * @return buffer $pdf
     */
    private function finalize()
    {
        $this->pdf->endDocument();
        return $this->pdf->getBuffer();
    }
}
