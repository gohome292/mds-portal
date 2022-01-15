<?php
App::import('Vendor', 'Iggy.pdf/japanese');

class PDF_Base extends PDF_Japanese
{
    var $title;
    var $print_form;
    var $report_details;
    var $cell_height = 5;
    
    // @param array $report
    // @param array $report_details
    // @return void
    function prepare($report, $report_details)
    {
        $this->title = mbo($report['Report']['name']);
        $this->print_form = $report['PrintForm']['identifier'];
        $this->report_details = mbo($report_details);
        // 改ページなし
        $this->SetAutoPageBreak(0);
        $this->AliasNbPages();
        $this->AddSJISFont();
        $this->Open();
        $this->AddPage($this->print_form);
    }
    
    function Header()
    {
        // 1ページ目のみタイトルを入れる
        if ($this->PageNo() == 1) {
            // Arial bold 15のフォントを指定する
            $this->SetFont('SJIS', 'B', 15);
            // タイトル
            $this->Cell(
                0,
                15,
                $this->title,
                0,
                0,
                'C'
            );
            //改行
            $this->Ln(15);
        }
    }
    
    // @return void
    function makeTitle()
    {
        foreach ($this->report_details as $report_detail) {
            $this->Cell(
                $report_detail['ReportDetail']['size'],
                $this->cell_height,
                $report_detail['ReportDetail']['name'],
                1,
                0,
                'C'
            );
        }
        $this->Ln();
    }
    
    // @param array $records
    // @return void
    function make($records)
    {
        $this->SetFont('SJIS', '', 8);
        $records = mbo($records);
        $this->makeTitle();
        
        foreach ($records as $record) {
            // 初期値
            $x0 = $x_now = $this->GetX();
            $y0 = $y_max = $this->GetY();
            // 文字の色を白にする
            $this->SetTextColor(255, 255, 255);
            
            $result_record = array();
            foreach ($this->report_details as $report_detail) {
                $fields = explode(
                    '|',
                    $report_detail['ReportDetail']['field']
                );
                $var = '';
                foreach ($fields as $field) {
                    $convert = '';
                    $options = array();
                    // カラムデータ
                    if(preg_match(
                        '/^(.+?)\.(.+?)(\[(.+?)\])?$/',
                        $field,
                        $matches
                    )) {
                        $modelname = $matches[1];
                        $fieldname = $matches[2];
                        if (isset($matches[4])) {
                            $convert = $matches[4];
                            if (strpos($convert, ',') !== false) {
                                $options = explode(',', $convert);
                                $convert = array_shift($options);
                            }
                        }
                        $var .= $this->convert(
                            $record[$modelname][$fieldname],
                            $convert,
                            $options
                        );
                    // 改行
                    } elseif ($field == 'ln') {
                        $var .= "\n";
                    // 自由入力
                    } elseif (preg_match(
                        '/^\[(.+?)\]$/',
                        $field,
                        $matches
                    )) {
                        $var .= $matches[1];
                    }
                }
                $result_record[$report_detail['ReportDetail']['id']] = $var;
                
                $this->MultiCell(
                    $report_detail['ReportDetail']['size'],
                    $this->cell_height,
                    $var,
                    0,
                    $report_detail['Align']['identifier']
                );
                // セル書き込み後のYを取得する
                $y1 = $this->GetY();
                // セル書き込み後の$y1が$y_maxより大きいかチェック
                if ($y1 > $y_max) {
                    //$y1が$y_maxより大きい場合は$y_maxを更新
                    $y_max = $y1;
                }
                // Yの位置を元に戻す
                $this->SetY($y0);
                // x_nowを進める
                $x_now = $x_now + $report_detail['ReportDetail']['size'];
                $this->SetX($x_now);
            }
            // セルの最大縦を取得
            $y_max_set = $y_max - $y0;
            // 文字の色を黒にする
            $this->SetTextColor(0, 0, 0);
            // ページの終わりに近づいたら
            if (200 < $y_max || $y0 > $y_max) {
                // ページを超えたら改ページ
                $this->AddPage($this->print_form);
                // タイトルを作る
                $this->makeTitle();
                // 初期値
                $x0 = $this->GetX();
                $y0 = $this->GetY();
            } else {
                // 超えない場合はそのまま
                $this->SetY($y0);
                $this->SetX($x0);
            }
            // $x_nowを初期値に戻す
            $x_now = $this->GetX($x0);
            // セルデータの書き込み
            foreach ($this->report_details as $report_detail) {
                $this->MultiCell(
                    $report_detail['ReportDetail']['size'],
                    $this->cell_height,
                    $result_record[$report_detail['ReportDetail']['id']],
                    0,
                    $report_detail['Align']['identifier']
                );
                $this->SetY($y0);
                $x_now = $x_now + $report_detail['ReportDetail']['size'];
                $this->SetX($x_now);
            }
            $this->SetY($y0);
            $this->SetX($x0);
            // $x_nowを初期値に戻す
            $x_now = $this->GetX($x0);
            // セル枠の書き込み
            foreach ($this->report_details as $report_detail) {
                $this->Cell(
                    $report_detail['ReportDetail']['size'],
                    $y_max_set,
                    '',
                    1,
                    0,
                    $report_detail['Align']['identifier']
                );
                $this->SetY($y0);
                $x_now = $x_now + $report_detail['ReportDetail']['size'];
                $this->SetX($x_now);
            }
            $this->Ln($y_max_set);
        }
    }
    
    function Footer()
    {
        // 文字色：黒
        $this->SetTextColor(0, 0, 0);
        // ページの下端から1.5 cm上に縦座標をセットする
        $this->SetY(-15);
        // Arial italic 8のフォントを指定する
        $this->SetFont('SJIS', '', 8);
        // ページ番号をセンタリングして出力
        $this->Cell(0, 10, $this->PageNo().' / {nb}', 0, 0, 'C');
    }
    
    // @param string $var
    // @param string $method
    // @param array $options
    // @return string
    function convert($var, $method, $options = array())
    {
        if (empty($var)) return '';
        if (empty($method)) return $var;
        $method = "convert_{$method}";
        if (method_exists($this, $method)) {
            return $this->$method($var, $options);
        }
    }
    
    // @param string $var
    // @param array $options
    // @return string
    function convert_numeric($var, $options = array())
    {
        return number_format($var, 0);
    }
    
    // @param string $var
    // @param array $options
    // @return string
    function convert_jpn_calendar($var, $options = array())
    {
        static $start;
        if (!isset($start)) {
            App::import('Vendor', 'Iggy.jpn_calendar');
            $start = true;
        }
        return jpn_calendar($var);
    }
    
    // @param string $var
    // @param array $options
    // @return string
    function convert_truncate($var, $options = array())
    {
        if (!isset($options[1])) {
            $options[1] = $options[0];
            $options[0] = 0;
        }
        return mb_substr($var, $options[0], $options[1], 'SJIS');
    }
}
