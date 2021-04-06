<?php
$config["style"] = array(
    "center" => array(
        "alignment" => "center",
    ),
    "right" => array(
        "alignment" => "right",
    ),
    "row" => array(
        "row" => true,
    ),
    "ml_0" => array(
        "m-left" => 0,
    ),
    "ml_10" => array(
        "m-left" => 10,
    ),
    "ml_20" => array(
        "m-left" => 20,
    ),
    "ml_30" => array(
        "m-left" => 30,
    ),
    "ml_40" => array(
        "m-left" => 40,
    ),
    "ml_50" => array(
        "m-left" => 50,
    ),
    "mt_0" => array(
        "m-top" => 0,
    ),
    "mt_5" => array(
        "m-top" => 5,
    ),
    "mt_10" => array(
        "m-top" => 10,
    ),
    "mt_20" => array(
        "m-top" => 20,
    ),
    "bordered" => array(
        "showborder" => true,
    ),
);

// Normal text
$config['p'] = array(
    'height' => LINE_HEIGHT,
    'font' => FONT_REGULAR_10,
    'row' => true,
);

// Normal text, first item in line
$config['p_first'] = array(
    'height' => LINE_HEIGHT,
    'font' => FONT_REGULAR_10,
    'row' => false,
    'm-left' => 30,
    'm-top' => 0,
);

// Bold text
$config['b'] = array(
    'height' => LINE_HEIGHT,
    'font' => FONT_BOLD_10,
    'row' => true,
);

// Bold text, first item in line, margin left = 15
$config['b_first_15'] = array(
    'height' => LINE_HEIGHT,
    'font' => FONT_BOLD_10,
    'row' => false,
    'm-left' => 15,
    'm-top' => 0,
);

// Bold text, first item in line, margin left = 30
$config['b_first_30'] = array(
    'height' => LINE_HEIGHT,
    'font' => FONT_BOLD_10,
    'row' => false,
    'm-left' => 30,
    'm-top' => 0,
);

// Large header
$config['h1'] = array(
    'height' => LINE_HEIGHT * 2,
    'font' => FONT_BOLD_20,
    'row' => false,
);

// Medium header
$config['h2'] = array(
    'height' => LINE_HEIGHT * 1.5,
    'font' => FONT_BOLD_15,
    'row' => false,
);

// Small header
$config['h3'] = array(
    'height' => LINE_HEIGHT * 1.2,
    'font' => FONT_BOLD_12,
    'row' => false,
);

/* Pdf printing settings */
$config["inspection_result_max_column"] = 13;

/* Immunosuppressant drug list */
$config["discontinued_drug"] = array("ALG", "OKT3");
$config["introduction_immunosuppressant_drugs"] = array(
    "CsA" => 30,
    "TAC" => 35,
    "PS" => 25,
    "MMF" => 35,
    "Bas" => 30,
    "ATG" => 35,
    "AZ" => 25,
    "MZ" => 27,
    "EVL" => 35,
    "DSG" => 35,
    "ALG" => 45,
    "OKT3" => 50,
);
$config["maintain_immunosuppressant_drugs"] = array(
    "CsA" => 30,
    "TAC" => 35,
    "PS" => 25,
    "MMF" => 35,
    "AZ" => 25,
    "MZ" => 27,
    "EVL" => 35,
);
