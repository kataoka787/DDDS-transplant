<?php defined('BASEPATH') or exit('No direct script access allowed');

class Pdf
{
    private $pdf = null;
    private $landscape = false;
    private $newLine = true;
    private $maxWidth = 0;
    private $maxHeight = 0;
    private $minX = 0;
    private $minY = 0;
    private $maxX = 0;
    private $maxY = 0;
    private $cursorX = 0;
    private $cursorY = 0;
    private $margin = array(
        'top' => 5,
        'right' => 5,
        'bottom' => 5,
        'left' => 5,
    );
    private $initBorder = false;
    private $borderContent = array();
    private $nextPage = false;
    private $borderOpts = null;
    private $border = array(
        'beginX' => 0,
        'beginY' => 0,
        'endX' => 0,
        'endY' => 0,
    );
    private $currentPage = 0;
    private $currentIndex = 0;
    private $shouldInsertPageIndex = false;

    private $createPageTrigger = null;
    private $createPageTriggerArgs = array();

    public $style = null;

    public function __construct()
    {
        $this->pdf = new PDFlib();
        $this->style = config_item("style");
    }

    /**
     * Get current position
     *
     * @return array $currentPositonXY
     */
    public function getCurrentPosition()
    {
        return array('currentX' => $this->cursorX, 'currentY' => $this->cursorY);
    }

    /**
     * Set initial settings
     *
     * @return void
     */
    public function initPdf()
    {
        /* Set resources path */
        $rePath = APPPATH . "data";

        /* This means we must check return values of load_font() etc. */
        $this->pdf->set_option("errorpolicy=return");

        /* All strings are expected as utf8 */
        $this->pdf->set_option("stringformat=utf8");

        /* Set the search path for font files */
        $this->pdf->set_option("SearchPath={{$rePath}}");

    }

    public function startIndexPage()
    {
        $this->shouldInsertPageIndex = true;
        $this->currentIndex = 0;
    }

    public function stopIndexPage()
    {
        for ($i = 0; $i < $this->currentIndex; $i++) {
            $pageIndex = $this->currentIndex - $i;
            $suspendingPage = $this->currentPage - $i;
            $this->pdf->resume_page("pagenumber $suspendingPage");
            $font = config_item('p')['font'];
            $crOpts = $font . " leading=120% alignment=center";
            $textFlow = $this->pdf->create_textflow($pageIndex . "/" . $this->currentIndex, $crOpts);
            $this->pdf->fit_textflow($textFlow, 0, 15, 595, 25, "verticalalign=center firstlinedist=capheight lastlinedist=descender");
            $this->pdf->end_page_ext("");
        }
        $this->shouldInsertPageIndex = false;
        $this->currentIndex = 0;
    }

    /**
     * Create new document
     *
     * @param boolean $landscape
     * @return void
     */
    public function createDocument($landscape = false, $options = array())
    {
        /* Create document */
        $this->pdf->begin_document("", "");

        /* Set metadata */
        $this->pdf->set_info("Creator", $options["creator"] ?? "ドナーデータ伝送システム");
        $this->pdf->set_info("Author", $options["author"] ?? "ドナーデータ伝送システム");
        $this->pdf->set_info("Title", $options["author"] ?? "ドナーデータ伝送システム");

        /* Set landscape orientation */
        $this->landscape = $landscape;
    }

    /**
     * Close current document
     *
     * @return void
     */
    public function endDocument()
    {
        $this->pdf->end_document("");
    }

    /**
     * Create new page in current document
     *
     * @return void
     */
    public function createPage()
    {
        $this->currentPage++;
        $this->currentIndex++;
        $this->resetProps();
        /* Create page */
        if ($this->landscape) {
            // A4 format in Landscape orientation
            $this->pdf->begin_page_ext(0, 0, "width=a4.height height=a4.width");
        } else {
            // A4 format in Portrait orientation3.
            $this->pdf->begin_page_ext(0, 0, "width=a4.width height=a4.height");
        }
        empty($this->createPageTrigger) || call_user_func_array($this->createPageTrigger, $this->createPageTriggerArgs);
    }

    public function setCreatePageTrigger($triggerFunctionName = null, $triggerFunctionArgs = array())
    {
        $this->createPageTrigger = $triggerFunctionName;
        $this->createPageTriggerArgs = $triggerFunctionArgs;
    }

    public function unsetCreatePageTrigger()
    {
        $this->createPageTrigger = null;
        $this->createPageTriggerArgs = array();
    }

    public function resetProps()
    {
        if ($this->landscape) {
            // Reset all properties in Landscape orientation
            $this->maxWidth = 820;
            $this->maxHeight = 555;
            $this->minX = 20;
            $this->minY = 20;
            $this->maxX = 840;
            $this->maxY = 575;
            $this->cursorX = 20;
            $this->cursorY = 575;
        } else {
            // Reset all properties in Portrait orientation
            $this->maxWidth = 575;
            $this->maxHeight = 790;
            $this->minX = 10;
            $this->minY = 30;
            $this->maxX = 585;
            $this->maxY = 820;
            $this->cursorX = 20;
            $this->cursorY = 820;
        }
    }

    /**
     * Close current page
     *
     * @return void
     */
    public function endPage()
    {
        /* End page */
        if ($this->shouldInsertPageIndex) {
            $this->pdf->suspend_page("");
        } else {
            $this->pdf->end_page_ext("");
        }
    }

    /**
     * Add text to current page and return it's coordinates
     * Create new page if needed
     *
     * @param string $text
     * @param int $x
     * @param int $y
     * @param string $opt
     * @param mixed $currentPosition
     * @return array $currentPosition
     */
    public function addText($text = "", $x, $y, $opt = FONT_REGULAR_10, &$curPos = null)
    {
        /* Close current page and create new one if current position reach end of page */
        if ($curPos && $curPos["y"] <= 10) {
            $this->endPage();
            $this->createPage();
            /* Add text to top of new page */
            $this->pdf->fit_textline($text, $x, 800, $opt);
        } else {
            $this->pdf->fit_textline($text, $x, $y, $opt);
        }

        $curPos = array(
            "x" => $this->pdf->get_option("textx", ""),
            "y" => $this->pdf->get_option("texty", ""),
        );
        return $curPos;
    }

    /**
     * Add line to current page
     *
     * @param array $startPoint
     * @param array $endPoint
     * @return void
     */
    public function addLine($startPoint = ADD_LINE_POINT_DEFAULT, $endPoint = ADD_LINE_POINT_DEFAULT)
    {
        $this->pdf->setlinewidth(1);
        $this->pdf->setcolor("stroke", "rgb", 0, 0, 0, 0);

        if ($startPoint && $endPoint) {
            $this->pdf->moveto($startPoint["x"], $startPoint["y"]);
            $this->pdf->lineto($endPoint["x"], $endPoint["y"]);
        } else {
            $this->cursorX = $this->minX;
            $this->cursorY -= $this->margin['top'];
            $this->pdf->moveto($this->cursorX, $this->cursorY);
            $this->cursorX = $this->maxX;
            $this->pdf->lineto($this->cursorX, $this->cursorY);
        }
        $this->pdf->stroke();
        $this->pdf->fit_textline("", 0, 0, "fillcolor={black}");
    }

    /**
     * Add table to current page and return it's last row coordinates.
     * Auto create page if table overflowing on current page.
     *
     * @param array $opts
     * @param array $data
     * @param mixed $currentPosition
     * @return array $currentPosition
     */
    public function addTable($opts = ADD_TBL_OPTS_DEFAULT, $data = ADD_TBL_DATA_DEFAULT)
    {
        /* Init table */
        $tbl = 0;
        $headerHeight = $opts["headerHeight"] ?? LINE_HEIGHT;
        $rowHeight = LINE_HEIGHT;

        /* Load font */
        $headerFont = $this->pdf->load_font("NotoSansCJKjp-Bold", "unicode", "");
        $bodyFont = $this->pdf->load_font("NotoSansCJKjp-Regular", "unicode", "");

        /* Get data row number */
        $rowNum = $opts["rowNum"] ?? count($data);

        /* Add table header */
        $col = 1;
        $row = 1;
        while ($col <= $opts["colNum"]) {
            $colWidth = $opts["colWidth"]["header"][$col - 1] ?? "10%";
            $colSpanSettings = $opts["colSpan"]["header"][$col - 1] ?? 1;
            $rowSpanSettings = $opts["rowSpan"]["header"][$col - 1] ?? $row;
            if (is_array($colSpanSettings)) {
                $maxColSpan = 0;
                foreach ($colSpanSettings as $rowIndex => $rowValue) {
                    foreach ($rowValue as $colIndex => $colValue) {
                        $tf = $this->pdf->add_textflow(0, $data[0][$col - 1][$rowIndex][$colIndex], FONT_BOLD_10 . " leading=120% alignment=center");
                        $colSpan = $colValue;
                        $rowSpan = $opts["rowSpan"]["header"][$col - 1][$rowIndex][$colIndex] ?? 1;
                        $colOpt = "fittextline={font=$headerFont fontsize=10 position=center} colspan=$colSpan rowspan=$rowSpan colwidth=$colWidth rowheight=$headerHeight textflow=$tf";
                        $maxColSpan = $colSpan > $maxColSpan ? $colSpan : $maxColSpan;
                        $row = $rowSpan > $row ? $rowSpan : $row;
                        $tbl = $this->pdf->add_table_cell($tbl, $col + $colIndex, $rowIndex + 1, "", $colOpt);
                    }
                }
                $col += $maxColSpan;
            } else {
                $tf = $this->pdf->add_textflow(0, $data[0][$col - 1], FONT_BOLD_10 . " leading=120% alignment=center");
                $colOpt = "fittextline={font=$headerFont fontsize=10 position=center} colspan=$colSpanSettings rowspan=$rowSpanSettings colwidth=$colWidth rowheight=$headerHeight textflow=$tf";
                $tbl = $this->pdf->add_table_cell($tbl, $col, 1, "", $colOpt);
                $col += $colSpanSettings;
                $row = $rowSpanSettings > $row ? $rowSpanSettings : $row;
            }
        }

        /* Add table body */
        for ($row = $row + 1; $row <= $rowNum; $row++) {
            for ($col = 1; $col <= $opts["colNum"]; $col++) {
                $colWidth = $opts["colWidth"]["body"][$col - 1] ?? $opts["colWidth"]["header"][$col - 1] ?? "10%";
                $tf = $this->pdf->add_textflow(0, $data[$row - 1][$col - 1], FONT_REGULAR_10 . " leading=100% alignment=center");
                $colOpt = "fittextline={font=$bodyFont fontsize=10} textflow=$tf";
                $tbl = $this->pdf->add_table_cell($tbl, $col, $row, "", $colOpt);
            }
        }

        $marginLeft = $opts["marginLeft"] ?? $this->minX + $this->margin['left'];
        $marginLeft = $marginLeft > $this->minX ? $marginLeft : $this->minX;

        $marginTop = $opts["marginTop"] ?? $this->margin['top'];
        $marginTop = $this->cursorY - $marginTop < $this->maxY ? $marginTop : $this->margin['top'];

        $width = $opts["width"] ?? $this->maxWidth;
        $width = $width < $this->maxX ? $width : $this->maxX;

        /* Fit table to page */
        $tblOpt = "header=0 rowheightdefault=$rowHeight " .
            "fill={{area=rowodd fillcolor={white}}} " .
            "stroke={{line=other}} ";
        /* Try fitting table to current page */
        if ($this->cursorY - $this->margin['top'] - $this->minY < $rowHeight) {
            $this->endPage();
            $this->createPage();
            /* Fit table to top of new page  */
            $result = $this->pdf->fit_table($tbl, $marginLeft, $this->minY, $width, $this->maxY - $marginTop, $tblOpt);
        } else {
            $result = $this->pdf->fit_table($tbl, $marginLeft, $this->minY, $width, $this->cursorY - $marginTop, $tblOpt);
        }
        /* Close current page and create new one if table overflowing */
        while ($result == "_boxfull") {
            $this->endPage();
            $this->createPage();
            /* Fit table to top of new page  */
            $result = $this->pdf->fit_table($tbl, $marginLeft, $this->minY, $width, $this->cursorY - $this->margin['top'], $tblOpt);
        }

        $this->cursorX = $this->pdf->info_table($tbl, "xvertline{$opts['colNum']}");
        $this->cursorY = $this->pdf->info_table($tbl, "yhorline$rowNum");

        /* This will also delete Textflow handler used in the table */
        $this->pdf->delete_table($tbl, "");
    }

    /**
     * Add text flow to current page
     * Create new page if needed
     */
    public function addTextFlow($text = '', $width, $opts = array())
    {
        $width = $width ?? $this->maxWidth;
        $opts = $opts ?? config_item('p');
        $height = $opts['height'] ?? config_item('p')['height'];
        $font = $opts['font'] ?? config_item('p')['font'];
        $opts['row'] = $opts['row'] ?? config_item('p')['row'];

        $this->autoMargin($height, $opts);
        [$width, $height] = $this->autoResize($width, $height);

        if (array_key_exists('alignment', $opts)) {
            $crOpts = $font . " leading=120% alignment=" . $opts['alignment'];
        } else {
            $crOpts = $font . " leading=120% alignment=left";
        }
        $tf = $this->pdf->create_textflow($text, $crOpts);
        if (array_key_exists('showborder', $opts)) {
            $fitOpts = $opts['showborder'] ? "showborder verticalalign=center firstlinedist=capheight lastlinedist=descender"
            : "verticalalign=center firstlinedist=capheight lastlinedist=descender";
        } else {
            $fitOpts = "verticalalign=center firstlinedist=capheight lastlinedist=descender";
        }

        if ($this->initBorder) {
            array_push($this->borderContent, array(
                'text' => $text,
                'opts' => $opts,
                'width' => $width,
                'height' => $height,
                'tf' => $tf,
                'fitOpts' => $fitOpts,
                'x' => $this->cursorX,
                'y' => $this->cursorY,
            ));
        } else {
            $this->pdf->fit_textflow($tf, $this->cursorX, $this->cursorY, ($this->cursorX + $width), ($this->cursorY + $height), $fitOpts);
        }

        $this->cursorX += $width;
    }

    public function addTextFlowTabs($text = '', $width, $opts = array())
    {
        $width = $width ?? $this->maxWidth;
        $opts = $opts ?? config_item('p');
        $height = $opts['height'] ?? config_item('p')['height'];
        $font = $opts['font'] ?? config_item('p')['font'];
        $opts['row'] = $opts['row'] ?? config_item('p')['row'];

        $this->autoMargin($height, $opts);
        [$width, $height] = $this->autoResize($width, $height);

        $colX = 0;
        $ruler = '';
        $colWidth = $width / $opts['col'];
        $tabAlign = $colAlign = "left";
        for ($i = 2; $i <= $opts['col']; $i++) {
            $colX += $colWidth;
            $ruler .= " $colX";
            $tabAlign .= " $colAlign";
        };
        $ruler = $opts['ruler'] ?? $ruler;
        $tabAlign = $opts['tabAlign'] ?? $tabAlign;

        $crOpts = "$font leading=120% ruler={{$ruler}} tabalignment={{$tabAlign}} hortabmethod=ruler";

        $tf = $this->pdf->create_textflow($text, $crOpts);
        if (array_key_exists('showborder', $opts)) {
            $fitOpts = $opts['showborder'] ? "showborder verticalalign=center firstlinedist=capheight lastlinedist=descender"
            : "verticalalign=center firstlinedist=capheight lastlinedist=descender";
        } else {
            $fitOpts = "verticalalign=center firstlinedist=capheight lastlinedist=descender";
        }

        $this->pdf->fit_textflow($tf, $this->cursorX, $this->cursorY, ($this->cursorX + $width), $this->cursorY + count(explode("\n", $text)) * LINE_HEIGHT, $fitOpts);

        $this->cursorX += $width;
    }

    public function autoMargin($height, $opts = array())
    {
        if (array_key_exists('row', $opts) && $opts['row']) {
            $this->cursorX += array_key_exists('m-left', $opts) ? $opts['m-left'] : $this->margin['left'];
            $this->newLine && $this->newLine = false;
        } else {
            $this->newLine = true;
            $this->cursorX = array_key_exists('m-left', $opts) ? $opts['m-left'] : $this->minX;
            $this->cursorY -= array_key_exists('m-top', $opts) ? $opts['m-top'] + $height :
            $this->margin['top'] + $height;
        }
    }

    public function autoResize($width, $height)
    {
        if ($this->cursorX + $width > $this->maxX) {
            $this->autoMargin($height);
            if ($width > $this->maxWidth) {
                $width = $this->maxWidth;
            }
        }

        if ($this->cursorY + $height > $this->maxY) {
            $this->cursorY = $this->maxY - $height;
        }

        if ($this->cursorY < $this->minY) {
            $currenX = $this->cursorX;
            $this->endPage();
            $this->createPage();
            $this->initBorder && $this->nextPage = true;
            if ($height > $this->maxHeight) {
                $height = $this->maxHeight;
            }
            $this->cursorX = $currenX;
            $this->cursorY = $this->maxY - $height;
        }
        return array($width, $height);
    }

    public function beginBorder($opts = null)
    {
        if (!$this->initBorder) {
            $this->borderOpts = $opts;
            $this->border['beginX'] = $this->cursorX;
            $this->border['beginY'] = $this->cursorY;

            if ($this->borderOpts === 'full_width') {
                $this->border['beginX'] = $this->minX;
            } else if ($this->borderOpts === 'row') {
                $this->border['beginY'] = $this->cursorY + LINE_HEIGHT;
            };
            $this->border['beginY'] = $this->border['beginY'] < $this->maxY ? $this->border['beginY'] : $this->maxY;
            $this->initBorder = true;
        }
    }

    public function endBorder()
    {
        if ($this->initBorder) {
            $this->initBorder = false;

            if ($this->nextPage) {
                $this->resetProps();
                $this->border['beginX'] = $this->cursorX;
                $this->border['beginY'] = $this->cursorY;
                if ($this->borderOpts === 'full_width') {
                    $this->border['beginX'] = $this->minX;
                } else if ($this->borderOpts === 'row') {
                    $this->border['beginY'] = $this->cursorY + LINE_HEIGHT;
                };
                foreach ($this->borderContent as $value) {
                    $this->addTextFlow($value['text'], $value['width'], $value['opts']);
                }
                $this->nextPage = false;
            } else {
                foreach ($this->borderContent as $value) {
                    $this->pdf->fit_textflow($value['tf'], $value['x'], $value['y'], $value['x'] + $value['width'], $value['y'] + $value['height'], $value['fitOpts']);
                }
            }
            $this->borderContent = array();

            if ($this->borderOpts === 'full_width') {
                $this->border['endX'] = $this->maxX;
            } else {
                $this->border['endX'] = $this->cursorX;
            }
            $this->border['endY'] = $this->cursorY;

            $tf = $this->pdf->create_textflow('', FONT_REGULAR_10);
            $fitOpts = "showborder";
            $this->pdf->fit_textflow($tf, $this->border['beginX'], $this->border['beginY'], $this->border['endX'], $this->border['endY'], $fitOpts);
        }
    }

    public function getBuffer()
    {
        return $this->pdf->get_buffer();
    }
}
