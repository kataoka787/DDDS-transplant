<?php defined('BASEPATH') or exit('No direct script access allowed');

class Printentry extends CI_model
{

    /**
     * Create pdf
     *
     * @param array $primaryKeysArray
     * @return pdf $pdf
     */
    public function createPdf($primaryKeysArray)
    {
        $settings = $this->init();
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
     * Init pdf file and global variables
     *
     * @return array $settings
     */
    private function init()
    {
        $settings = array(
            "style" => $this->pdf->style,
            "b" => config_item('b'),
            "b_first" => config_item('b_first_15'),
            "p_first" => config_item('p_first'),
            "h1" => config_item('h1'),
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
        extract($settings);
        $this->pdf->createPage();
        $currentTime = new DateTime();

        $this->printHeader($h1, $b, $style, $currentTime, $printData);

        /* Print recipient individual info (個人情報) */
        $this->printRecipientIndividualInfo($b_first, $b, $style, $currentTime, $printData);

        /* Print organ status (臓器の転帰) */
        $this->printOrganStatus($b_first, $style, $printData);

        /* Print patient status (患者の転帰) */
        $this->printPatientStatus($b_first, $b, $style, $printData);

        /* Print dead cause category (死因の分類) */
        $this->printDeadCauseCategory($b_first, $printData);

        /* 腎臓, 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_KIDNEY || $printData->ZOKI_CODE == ORGAN_PANCREAS) {
            /* Print dialysis (透析) */
            $this->printDialysis($b_first, $b, $style, $printData);
        }

        /* 膵臓 */
        if ($printData->ZOKI_CODE == ORGAN_PANCREAS) {
            /* Print insurin (インスリン治療) */
            $this->printInsurinTherapy($b_first, $b, $printData);
        }

        /* Print immunosuppressant drug*/
        $this->printImmunosuppressantDrug($b_first, $p_first, $style, $printData);

        /* 肺 */
        if ($printData->ZOKI_CODE == ORGAN_LUNG) {
            $this->pdf->addTextFlow("在宅酸素療法", 90, array_merge($b_first, $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow("離脱年月日", 50, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(date("Y/m/d"), 80);
            $this->pdf->addTextFlow("再導入年月日", 60, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(date("Y/m/d"), 80);
        }

        /* Print rejection therapeutic effect */
        $this->printRejectionTherapeuticEffect($b_first, $style, $printData);

        /* Print inspection result */
        $inspections = $this->Tkensa->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT);
        $tableCount = ceil((min(config_item("max_cycle_year"), $this->Tkensa->getMaxCycle($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT)) + 3) / config_item('inspection_result_max_column'));
        $tableCount == 1 && $this->printInspectionResult($b_first, $style, $tableCount, $inspections);

        /* Print complication */
        $this->printComplication($b_first, $style, $printData);

        /* Print hospital leaving date (退院年月日) */
        $this->pdf->addTextFlow("退院年月日", 50, array_merge($b_first, $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->TAIIN_DATE), 80);

        /* Print print rehabilitation (社会復帰) */
        $this->printRehabilitation($p_first, $b_first, $b, $style, $printData);

        /* Print footer */
        $this->printFooter($b_first, $b, $style);
        $this->pdf->endPage();

        /* Print inspection result (検査項目) in new page */
        if ($tableCount > 1) {
            $this->pdf->createPage();
            /* Print page header */
            $this->printHeader($h1, $b, $style, $currentTime, $printData);
            /* Print recipient individual info (個人情報) */
            $this->printRecipientIndividualInfo($b_first, $b, $style, $currentTime, $printData);
            /* Print inspection result (検査項目) */
            $this->printInspectionResult($b_first, $style, $tableCount, $inspections);
            /* Print page footer */
            $this->printFooter($b_first, $b, $style);
            $this->pdf->endPage();
        }

        /* Print living conditions (生活状況) in new page */
        $this->pdf->createPage();
        /* Print page header */
        $this->printHeader($h1, $b, $style, $currentTime, $printData);
        /* Print recipient individual info (個人情報) */
        $this->printRecipientIndividualInfo($b_first, $b, $style, $currentTime, $printData);
        /* Print living conditions (生活状況) */
        $livingConditions = $this->Tliving->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT);
        $this->printLivingCoditions($b_first, $style, $livingConditions);
        /* Print page footer */
        $this->printFooter($b_first, $b, $style);
        $this->pdf->endPage();
    }

    /**
     * Print page header
     *
     * @param array $b
     * @param array $style
     * @param datetime $currentTime
     * @param object $printData
     * @return void
     */
    private function printHeader($h1, $b, $style, $currentTime, $printData)
    {
        $this->pdf->addTextFlow("移植後経過情報記録記入用紙（" . (ORGAN[$printData->ZOKI_CODE] ?? null) . "）", 575, array_merge($h1, $style['center']));
        $this->pdf->addTextFlow("印刷日時", 460, array_merge($b, $style['right']));
        $this->pdf->addTextFlow(":", 10);
        $this->pdf->addTextFlow($currentTime->format(DATE_TIME_LONG), 100, $style['ml_0']);
    }

    /**
     * Print recipient individual info (個人情報)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param datetime $currentTime
     * @param object $printData
     * @return void
     */
    private function printRecipientIndividualInfo($b_first, $b, $style, $currentTime, $printData)
    {
        $this->pdf->beginBorder('full_width');

        $this->pdf->addTextFlow("移植者ID", 60, array_merge($b_first, $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(str_pad($printData->RECIPIENT_ID, 7, "0", STR_PAD_LEFT), 80);
        $this->pdf->addTextFlow("氏名", 50, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->KANJI_NAME, 200);

        $this->pdf->addTextFlow("性別", 60, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(SEX_SHORT[$printData->SEX] ?? "", 80);
        $this->pdf->addTextFlow("生年月日", 50, $b);
        $this->pdf->addTextFlow(":", 5);
        $birthday = new DateTime($printData->BIRTHDAY);
        $this->pdf->addTextFlow($birthday->format(DATE_TIME_DEFAULT), 80);
        $this->pdf->addTextFlow("現在年齢", 50, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($currentTime->diff($birthday)->y, 20);
        $this->pdf->addTextFlow("歳", 20, $b);

        $this->pdf->addTextFlow("移植年月日", 60, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->ISYOKU_DATE), 80);
        $this->pdf->addTextFlow("移植回数", 50, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->ISYOKU_CNT, 10);
        $this->pdf->addTextFlow("回", 65, $b);
        $this->pdf->addTextFlow("移植時年齢", 50, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->ISYOKU_AGE, 20);
        $this->pdf->addTextFlow("歳", 20, $b);

        $this->pdf->addTextFlow("移植実施施設", 110, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->TRANSPLANT_INSTITUTION_NAME, 250);

        $this->pdf->addTextFlow("移植後経過情報管理施設", 110, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->FOLLOW_UP_INSTITUTION_NAME, 250);

        $this->pdf->endBorder();
    }

    /**
     * Print underlying disease (原疾患)
     *
     * @param array $b_first
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printOrganStatus($b_first, $style, $printData)
    {
        $this->pdf->addTextFlow("臓器の転帰", 60, array_merge($b_first, $style['mt_10']));
        $this->pdf->addTextFlow(":", 5);
        $isFirst = true;
        foreach ($this->Mcd->getCodeValueArrayByCodeType("080") as $code => $value) {
            $shouldCheck = $printData->ZOKI_TENKI == $code ? "●" : "〇";
            if ($isFirst) {
                $isFirst = false;
                $this->pdf->addTextFlow($shouldCheck . " $value", 50);
                continue;
            }
            $this->pdf->addTextFlow($shouldCheck . " $value", 50);
        }

        $this->pdf->addTextFlow("機能廃絶日", 60, array_merge($b_first, ['m-left' => 90]));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->KINOHAIZETU_DATE), 80);

        $this->pdf->beginBorder('full_width');
        $this->pdf->addTextFlow("原因", 60, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $rowItemCount = 0;
        foreach ($this->Mcd->getCodeValueArrayByCodeType("086") as $code => $value) {
            $shouldCheck = $printData->ZOKI_TENKI_GENIN == $code ? "●" : "〇";
            /* New line when reach 4 items/row */
            if ($rowItemCount == 5) {
                $this->pdf->addTextFlow("", 70, $b_first);
                $this->pdf->addTextFlow("$shouldCheck $value", 90);
                $rowItemCount = 1;
                continue;
            }
            $this->pdf->addTextFlow("$shouldCheck $value", 90);
            $rowItemCount++;
        }
        $this->pdf->endBorder();
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
        $this->pdf->addTextFlow("患者の転帰", 60, array_merge($b_first, $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        foreach ($this->Mcd->getCodeValueArrayByCodeType("077") as $code => $value) {
            $shouldCheck = $printData->RECIPIENT_TENKI == $code ? "●" : "〇";
            $this->pdf->addTextFlow("$shouldCheck  $value", 50);
        }

        $this->pdf->addTextFlow("死亡日", 60, array_merge($b_first, ['m-left' => 90]));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->SIBO_DATE), 80);
        $this->pdf->addTextFlow("死因", 30, $b);
        $this->pdf->addTextFlow(":", 5);
        $mainReason = $this->Mcd->getCodeValueArrayByCodeType("078")[$printData->SIIN_H] ?? "";
        $additionReason = $this->Mcd->getCodeValueArrayByCodeType("079")[$printData->SIIN_L] ?? "";
        $this->pdf->addTextFlow($mainReason . "　　　" . $additionReason, 200);
    }

    /**
     * Print dead cause category (死因の分類)
     *
     * @param array $b_first
     * @param object $printData
     * @return void
     */
    private function printDeadCauseCategory($b_first, $printData)
    {
        $mainReasonsList = $this->Mcd->getByCodeType("078");
        $additionReasonsList = $this->Mcd->getByCodeType("079");

        $this->pdf->addTextFlow("死因の分類", 60, $b_first);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->beginBorder('full_width');
        foreach ($mainReasonsList as $mainReason) {
            $shouldCheck = $printData->SIIN_H == $mainReason->CODE;
            $this->pdf->addTextFlow($mainReason->VALUE, 80, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $additionReasons = array_filter($additionReasonsList, function ($additionReason) use ($mainReason) {
                return substr($additionReason->CODE, 0, 2) == $mainReason->CODE;
            });
            if (count($additionReasons) > 5) {
                $rowItemCount = 0;
                foreach ($additionReasons as $additionReason) {
                    $shouldCheck = $shouldCheck && $printData->SIIN_L == $additionReason->CODE;
                    $shouldCheck = $shouldCheck ? "●" : "〇";
                    /* New line when reach 5 items/row */
                    if ($rowItemCount == 4) {
                        $this->pdf->addTextFlow("", 90, $b_first);
                        $this->pdf->addTextFlow($shouldCheck . " " . $additionReason->VALUE, 105);
                        $rowItemCount = 1;
                        continue;
                    }
                    $this->pdf->addTextFlow($shouldCheck . " " . $additionReason->VALUE, 105);
                    $rowItemCount++;
                }
            } else {
                foreach ($additionReasons as $additionReason) {
                    $shouldCheck = $shouldCheck && $printData->SIIN_L == $additionReason->CODE;
                    $shouldCheck = $shouldCheck ? "●" : "〇";
                    $this->pdf->addTextFlow($shouldCheck . " " . $additionReason->VALUE, 85);
                }
            }

        }
        $this->pdf->endBorder();
    }

    /**
     * Print dialysis (透析)
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printDialysis($b_first, $b, $style, $printData)
    {
        /* 腎臓 */
        if ($printData->ZOKI_CODE == ORGAN_KIDNEY) {
            $this->pdf->addTextFlow("透析", 60, array_merge($b_first, $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            foreach ($this->Mcd->getCodeValueArrayByCodeType("087") as $code => $value) {
                $shouldCheck = $printData->TOSEKIRIDATU == $code ? "●" : "〇";
                $this->pdf->addTextFlow("$shouldCheck $value", 55);
            }
            $this->pdf->beginBorder('row');
            $this->pdf->addTextFlow("離脱不能原因", 60, $b);
            $this->pdf->addTextFlow(":", 5);
            foreach ($this->Mcd->getCodeValueArrayByCodeType("088") as $code => $value) {
                $shouldCheck = $printData->TOSEKIRIDATU_FUNOGENIN == $code ? "●" : "〇";
                switch (mb_strlen($value)) {
                    case 3:
                        $this->pdf->addTextFlow("$shouldCheck $value", 45);
                        break;
                    case 4:
                        $this->pdf->addTextFlow("$shouldCheck $value", 55);
                        break;
                    case 5:
                        $this->pdf->addTextFlow("$shouldCheck $value", 65);
                        break;
                    default:
                        $this->pdf->addTextFlow("$shouldCheck $value", mb_strlen($value) * 15);
                        break;
                }
            }
            $this->pdf->endBorder();

            $this->pdf->addTextFlow("最終透析日", 60, $b_first);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->TOSEKI_LAST_DATE), 80);
        } else { /* 膵臓 */
            $this->pdf->addTextFlow("透析", 70, array_merge($b_first, $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            foreach ($this->Mcd->getCodeValueArrayByCodeType("087") as $code => $value) {
                $shouldCheck = $printData->TOSEKIRIDATU == $code ? "●" : "〇";
                $this->pdf->addTextFlow("$shouldCheck $value", 55);
            }
            $this->pdf->addTextFlow("最終透析日", 60, $b);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow(datetimeToString($printData->TOSEKI_LAST_DATE), 80);
        }
    }

    /**
     * Print insulin therapy (インスリン治療)
     *
     * @param array $b_first
     * @param array $b
     * @param object $printData
     * @return void
     */
    private function printInsurinTherapy($b_first, $b, $printData)
    {
        $this->pdf->addTextFlow("インスリン治療", 70, $b_first);
        $this->pdf->addTextFlow(":", 5);
        foreach ($this->Mcd->getCodeValueArrayByCodeType('087') as $code => $value) {
            $shouldCheck = $printData->INSULIN_FLG == $code ? "●" : "〇";
            $this->pdf->addTextFlow("$shouldCheck $value", 55);
        }
        $this->pdf->addTextFlow("最終投与日", 60, $b);
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow(datetimeToString($printData->INSULIN_LAST_DATE), 80);
    }

    /**
     * Print immunosuppressant drugs (免疫抑制剤)
     *
     * @param array $b_first
     * @param array $p_first
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printImmunosuppressantDrug($b_first, $p_first, $style, $printData)
    {
        $this->pdf->addTextFlow("免疫抑制剤（導入）", 90, array_merge($b_first, $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        $drugCodeMSt = $this->Mcd->getCodeValueArrayByCodeType("189");

        foreach (config_item("introduction_immunosuppressant_drugs") as $drug => $textFlowLength) {
            $dataColumnName = "DONYU_" . strtoupper($drug);
            $shouldCheck = ($drugCodeMSt[$printData->$dataColumnName] ?? "") == M_CD_CODE_TYPE_189_VALUE_HAVE ? "■" : "□";
            $drug = in_array($drug, config_item("discontinued_drug")) ? "$drug" . "△" : $drug;
            $this->pdf->addTextFlow("$shouldCheck $drug", $textFlowLength);
        }

        $this->pdf->addTextFlow("その他", 35, array_merge($p_first, ['m-left' => 120]));
        $this->pdf->addTextFlow(":", 5);
        $this->pdf->addTextFlow($printData->DONYU_ETC, 340);
        $this->pdf->addTextFlow("△：製造中止", 60);

        if ($printData->ZOKI_CODE != ORGAN_HEART) {
            $this->pdf->beginBorder('full_width');
            $this->pdf->addTextFlow("免疫抑制剤（維持）", 90, array_merge($b_first, $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            foreach (config_item("maintain_immunosuppressant_drugs") as $drug => $textFlowLength) {
                $dataColumnName = "IJI_" . strtoupper($drug);
                $shouldCheck = ($drugCodeMSt[$printData->$dataColumnName] ?? "") == M_CD_CODE_TYPE_189_VALUE_HAVE ? "■" : "□";
                $this->pdf->addTextFlow("$shouldCheck $drug", $textFlowLength);
            }
            $this->pdf->addTextFlow("その他", 30);
            $this->pdf->addTextFlow(":", 5);
            $this->pdf->addTextFlow($printData->IJI_ETC, 140);
            $this->pdf->endBorder();
        }
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
            $rejections = $this->Trejection->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT, REJECTION_COMMON);
            $this->pdf->addTextFlow("拒否反応", 60, array_merge($b_first, $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
        } else { /* 肺 */
            $rejections = $this->Trejection->getAllByPrimaryKeys($printData->RECIPIENT_ID, $printData->ZOKI_CODE, $printData->ISYOKU_CNT, REJECTION_ACUTE);
            $this->pdf->addTextFlow("急性拒絶反応", 60, array_merge($b_first, $style['mt_5']));
            $this->pdf->addTextFlow(":", 5);
            if (!empty($rejections)) {
                $rejectionInfo = array(
                    array("No.", "診断日", "GradeA", "GradeB", "GradeC", "GradeD", "治療効果"),
                );
                $rejectionCount = 0;
                $gradeMst = $this->Mcd->getCodeValueArrayByCodeType("099");
                foreach ($rejections as $rejection) {
                    $rejectionCount++;
                    $gradeA = $gradeMst[$rejection->GRADEA] ?? "";
                    $gradeB = $gradeMst[$rejection->GRADEB] ?? "";
                    $gradeC = $gradeMst[$rejection->GRADEC] ?? "";
                    $gradeD = $gradeMst[$rejection->GRADED] ?? "";
                    $therapeuticEffect = $this->Mcd->getByCodeTypeCode("098", $rejection->TIRYOU_KOKA);
                    $therapeuticEffect = empty($therapeuticEffect) ? "" : $therapeuticEffect->VALUE;

                    array_push($rejectionInfo, array(
                        $rejectionCount,
                        datetimeToString($rejection->SINDAN_DATE),
                        $gradeA,
                        $gradeB,
                        $gradeC,
                        $gradeD,
                        $therapeuticEffect,
                    ));
                }
                $this->pdf->addTable(
                    array(
                        "colNum" => 7,
                        "colWidth" => array(
                            "header" => array("5%", "15%", "15%", "15%", "15%", "15%", "25%"),
                        ),
                        "marginLeft" => 50,
                    ),
                    $rejectionInfo,
                );
            }

            $this->pdf->addTextFlow("慢性拒否反応", 60, array_merge($b_first, $style['mt_5']));
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
                    $ab = empty($ab) ? "未検" : $ab->VALUE;

                    $therapeuticEffect = $this->Mcd->getByCodeTypeCode("098", $rejection->TIRYOU_KOKA);
                    $therapeuticEffect = empty($therapeuticEffect) ? "" : $therapeuticEffect->VALUE;

                    $therapeuticEffectString = "";
                    foreach ($this->Mcd->getByCodeType("098") as $therapeuticEffect) {
                        $shouldCheck = $rejection->TIRYOU_KOKA === $therapeuticEffect->CODE ? "●" : "〇";
                        $therapeuticEffectString .= "$shouldCheck $therapeuticEffect->VALUE ";
                    }
                    array_push($rejectionInfo, array(
                        $rejectionCount,
                        datetimeToString($rejection->SINDAN_DATE),
                        $stage,
                        $ab,
                        $therapeuticEffectString,
                    ));
                }
                $this->pdf->addTable(
                    array(
                        "colNum" => 5,
                        "colWidth" => array(
                            "header" => array("5%", "15%", "15%", "15%", "50%"),
                        ),
                        "marginLeft" => 50,
                    ),
                    $rejectionInfo,
                );
            }
            return;
        }

        /* 心臓 */
        if ($printData->ZOKI_CODE == ORGAN_HEART && !empty($rejections)) {
            $rejectionInfo = array(
                array("No.", "診断日", "治療手段", "治療効果"),
            );
            $rejectionCount = 0;
            foreach ($rejections as $rejection) {
                $rejectionCount++;
                $treatmentMethodString = "";
                $therapeuticEffectString = "";
                foreach ($this->Mcd->getByCodeType("097") as $treatmentMethod) {
                    $shouldCheck = $rejection->TIRYOU_SYUDAN == $treatmentMethod->CODE ? "●" : "〇";
                    $treatmentMethodString .= "$shouldCheck $treatmentMethod->VALUE ";
                }
                foreach ($this->Mcd->getByCodeType("216") as $therapeuticEffect) {
                    $shouldCheck = $rejection->TIRYOU_KOKA === $therapeuticEffect->CODE ? "●" : "〇";
                    $therapeuticEffectString .= "$shouldCheck $therapeuticEffect->VALUE ";
                }
                array_push($rejectionInfo, array(
                    $rejectionCount,
                    datetimeToString($rejection->SINDAN_DATE),
                    $treatmentMethodString,
                    $therapeuticEffectString,
                ));
            }
            $this->pdf->addTable(
                array(
                    "colNum" => 4,
                    "colWidth" => array(
                        "header" => array("3%", "11%", "75%", "14%"),
                    ),
                    "marginLeft" => 30,
                ),
                $rejectionInfo,
            );
            return;
        }

        /* 肝臓, 腎臓, 膵臓, 小腸*/
        if (!empty($rejections)) {
            $rejectionInfo = array(
                array("No.", "診断日", "治療効果"),
            );

            $rejectionCount = 0;
            foreach ($rejections as $rejection) {
                $rejectionCount++;
                $therapeuticEffectString = "";
                foreach ($this->Mcd->getByCodeType("098") as $therapeuticEffect) {
                    $shouldCheck = $rejection->TIRYOU_KOKA === $therapeuticEffect->CODE ? "●" : "〇";
                    $therapeuticEffectString .= "$shouldCheck $therapeuticEffect->VALUE ";
                }
                array_push($rejectionInfo, array(
                    $rejectionCount,
                    datetimeToString($rejection->SINDAN_DATE),
                    $therapeuticEffectString,
                ));
            }
            $this->pdf->addTable(
                array(
                    "colNum" => 3,
                    "colWidth" => array(
                        "header" => array("5%", "20%", "75%"),
                    ),
                    "marginLeft" => 30,
                    "width" => 400,
                ),
                $rejectionInfo,
            );
        }

    }

    /**
     * Print inspection result (検査項目)
     *
     * @param array $b_first
     * @param array $style
     * @param object $printData
     * @return void
     */
    private function printInspectionResult($b_first, $style, $tableCount, $inspections)
    {
        $inspectionCycleName = $this->Mcd->getCodeValueArrayByCodeType("105");

        $this->pdf->addTextFlow("検査項目", 50, array_merge($b_first, $style['mt_5']));
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
        $this->pdf->addTextFlow("入院を要する合併症", 100, array_merge($b_first, $style['mt_5']));
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
    private function printRehabilitation($p_first, $b_first, $b, $style, $printData)
    {
        $this->pdf->beginBorder('full_width');
        $this->pdf->addTextFlow("社会復帰", 50, array_merge($b_first, $style['mt_5']));
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

        $isFirst = true;
        foreach ($this->Mcd->getByCodeType($rehabilitationCode) as $rehabilitation) {
            $shouldCheck = $printData->SYAKAIFUKKI == $rehabilitation->CODE ? "●" : "〇";
            if ($isFirst) {
                $isFirst = false;
                $this->pdf->addTextFlow("$shouldCheck $rehabilitation->VALUE", 350);
                $this->pdf->addTextFlow("社会復帰日", 50, $b);
                $this->pdf->addTextFlow(":", 5);
                $this->pdf->addTextFlow(datetimeToString($printData->SYAKAIFUKKI_DATE), 80);
                continue;
            }
            $this->pdf->addTextFlow("$shouldCheck $rehabilitation->VALUE", 350, array_merge($p_first, ['m-left' => 80]));
        }
        $this->pdf->endBorder();
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
        $this->pdf->addTextFlow("生活状況", 45, array_merge($b_first, $style['mt_5']));
        $this->pdf->addTextFlow(":", 5);
        $lastTwoRecord = array();
        empty($livingConditions) || $lastTwoRecord = array_splice($livingConditions, 0, 2);
        $header = array("No.", "記録日", "確認者", "報告者", "報告形式", "コメント");
        $livingConditionPrintData = array(
            $header,
        );
        if (!empty($lastTwoRecord)) {
            foreach ($lastTwoRecord as $index => $livingCondition) {
                array_push($livingConditionPrintData, array(
                    $index + 1,
                    datetimeToString($livingCondition->INPUT_DATE),
                    $livingCondition->KAKUNIN_USER_NAME,
                    $livingCondition->REPORT_USER_NAME,
                    $reportFormMst[$livingCondition->REPORT_FORM] ?? "",
                    $livingCondition->LIVING_NAIYO,
                ));
            }
        }
        /* Leave 2 empty row */
        for ($i = count($lastTwoRecord); $i < count($lastTwoRecord) + 2; $i++) {
            array_push($livingConditionPrintData, array($i + 1, "", "", "", "", ""));
        }
        $this->pdf->addTable(
            array(
                "colNum" => count($header),
                "colWidth" => array(
                    "header" => array("5%", "10%", "10%", "10%", "10%", "55%"),
                ),
                "marginLeft" => 30,
            ),
            $livingConditionPrintData
        );
    }

    /**
     * Print page footer
     *
     * @param array $b_first
     * @param array $b
     * @param array $style
     * @return void
     */
    private function printFooter($b_first, $b, $style)
    {
        $this->pdf->addTextFlow("記載日", 30, array_merge($b_first, $style['mt_20']));
        $this->pdf->addTextFlow(":", 150);
        $this->pdf->addTextFlow("施設名", 30, $b);
        $this->pdf->addTextFlow(":", 150);
        $this->pdf->addTextFlow("記載者氏名", 40, $b);
        $this->pdf->addTextFlow(":", 100);
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
