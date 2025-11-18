<?php

define('FPDF_VERSION','1.86');

class FPDF
{
protected $page;
protected $n;
protected $offsets;
protected $buffer;
protected $pages;
protected $state;
protected $compress;
protected $k;
protected $DefOrientation;
protected $CurOrientation;
protected $StdPageSizes;
protected $DefPageSize;
protected $CurPageSize;
protected $CurRotation;
protected $PageInfo;
protected $wPt, $hPt;
protected $w, $h;
protected $lMargin;
protected $tMargin;
protected $rMargin;
protected $bMargin;
protected $cMargin;
protected $x, $y;
protected $lasth;
protected $LineWidth;
protected $fontpath;
protected $FontFiles;
protected $fonts;
protected $FontFamily;
protected $FontStyle;
protected $underline;
protected $CurrentFont;
protected $FontSizePt;
protected $FontSize;
protected $DrawColor;
protected $FillColor;
protected $TextColor;
protected $ColorFlag;
protected $WithAlpha;
protected $AutoPageBreak;
protected $PageBreakTrigger;
protected $InHeader;
protected $InFooter;
protected $AliasNbPages;
protected $ZoomMode;
protected $LayoutMode;
protected $metadata;
protected $PDFVersion;

function __construct($orientation='P', $unit='mm', $size='A4')
{
    $this->page = 0;
    $this->n = 2;
    $this->buffer = '';
    $this->pages = array();
    $this->PageInfo = array();
    $this->fonts = array();
    $this->FontFiles = array();
    $this->offsets = array();
    $this->state = 0;
    $this->compress = true;
    $this->k = 1;
    $this->DefOrientation = 'P';
    $this->CurOrientation = 'P';
    $this->StdPageSizes = array('a3'=>array(841.89,1190.55), 'a4'=>array(595.28,841.89), 'a5'=>array(420.94,595.28),
        'letter'=>array(612,792), 'legal'=>array(612,1008));
    $size = $this->_getpagesize($size);
    $this->DefPageSize = $size;
    $this->CurPageSize = $size;
    $this->CurRotation = 0;
    $this->wPt = $size[0];
    $this->hPt = $size[1];
    $this->w = $size[0] / $this->k;
    $this->h = $size[1] / $this->k;
    $margin = 28.35 / $this->k;
    $this->lMargin = $margin;
    $this->tMargin = $margin;
    $this->rMargin = $margin;
    $this->bMargin = $margin * 2;
    $this->cMargin = $margin / 10;
    $this->LineWidth = .567 / $this->k;
    $this->fontpath = dirname(__FILE__).'/font/';
    $this->FontFamily = '';
    $this->FontStyle = '';
    $this->FontSizePt = 12;
    $this->underline = false;
    $this->DrawColor = '0 G';
    $this->FillColor = '0 g';
    $this->TextColor = '0 g';
    $this->ColorFlag = false;
    $this->WithAlpha = false;
    $this->AutoPageBreak = true;
    $this->PageBreakTrigger = $this->h - $this->bMargin;
    $this->InHeader = false;
    $this->InFooter = false;
    $this->ZoomMode = 'default';
    $this->LayoutMode = 'default';
    $this->metadata = array();
    $this->PDFVersion = '1.3';
}

function SetMargins($left, $top, $right=null)
{
    $this->lMargin = $left;
    $this->tMargin = $top;
    if($right===null)
        $right = $left;
    $this->rMargin = $right;
}

function SetLeftMargin($margin)
{
    $this->lMargin = $margin;
    if($this->page>0 && $this->x<$margin)
        $this->x = $margin;
}

function SetTopMargin($margin)
{
    $this->tMargin = $margin;
}

function SetRightMargin($margin)
{
    $this->rMargin = $margin;
}

function SetAutoPageBreak($auto, $margin=0)
{
    $this->AutoPageBreak = $auto;
    $this->bMargin = $margin;
    $this->PageBreakTrigger = $this->h - $margin;
}

function SetFont($family, $style='', $size=0)
{
    if($family=='')
        $family = $this->FontFamily;
    else
        $family = strtolower($family);
    $style = strtoupper($style);
    if(strpos($style,'U')!==false)
    {
        $this->underline = true;
        $style = str_replace('U','',$style);
    }
    else
        $this->underline = false;
    if($style=='IB')
        $style = 'BI';
    if($size==0)
        $size = $this->FontSizePt;
    if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
        return;
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey]))
    {
        if($family=='arial')
            $family = 'helvetica';
        if(in_array($family,array('courier','helvetica','times')))
        {
            $this->fonts[$fontkey] = array('i'=>$this->n+1,'type'=>'core','name'=>$this->_getcorefont($family.$style),'up'=>-100,'ut'=>50,'cw'=>$this->_getcorefontwidths($family.$style));
            $this->n++;
        }
        else
            $this->Error('Undefined font: '.$family.' '.$style);
    }
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size / $this->k;
    $this->CurrentFont = &$this->fonts[$fontkey];
}

function SetFontSize($size)
{
    if($this->FontSizePt==$size)
        return;
    $this->FontSizePt = $size;
    $this->FontSize = $size / $this->k;
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function AddPage($orientation='', $size='', $rotation=0)
{
    if($this->state==3)
        $this->Error('The document is closed');
    $family = $this->FontFamily;
    $style = $this->FontStyle.($this->underline ? 'U' : '');
    $fontsize = $this->FontSizePt;
    $lw = $this->LineWidth;
    $dc = $this->DrawColor;
    $fc = $this->FillColor;
    $tc = $this->TextColor;
    $cf = $this->ColorFlag;
    if($this->page>0)
    {
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        $this->_endpage();
    }
    $this->_beginpage($orientation,$size,$rotation);
    $this->_out('2 J');
    $this->LineWidth = $lw;
    $this->_out(sprintf('%.2F w',$lw*$this->k));
    if($family)
        $this->SetFont($family,$style,$fontsize);
    $this->DrawColor = $dc;
    if($dc!='0 G')
        $this->_out($dc);
    $this->FillColor = $fc;
    if($fc!='0 g')
        $this->_out($fc);
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
    $this->InHeader = true;
    $this->Header();
    $this->InHeader = false;
    if($this->LineWidth!=$lw)
    {
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w',$lw*$this->k));
    }
    if($family)
        $this->SetFont($family,$style,$fontsize);
    if($this->DrawColor!=$dc)
    {
        $this->DrawColor = $dc;
        $this->_out($dc);
    }
    if($this->FillColor!=$fc)
    {
        $this->FillColor = $fc;
        $this->_out($fc);
    }
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
}

function Header()
{
}

function Footer()
{
}

function PageNo()
{
    return $this->page;
}

function SetDrawColor($r, $g=null, $b=null)
{
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->DrawColor = sprintf('%.3F G',$r/255);
    else
        $this->DrawColor = sprintf('%.3F %.3F %.3F RG',$r/255,$g/255,$b/255);
    if($this->page>0)
        $this->_out($this->DrawColor);
}

function SetFillColor($r, $g=null, $b=null)
{
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->FillColor = sprintf('%.3F g',$r/255);
    else
        $this->FillColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    if($this->page>0)
        $this->_out($this->FillColor);
}

function SetTextColor($r, $g=null, $b=null)
{
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->TextColor = sprintf('%.3F g',$r/255);
    else
        $this->TextColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
}

function GetStringWidth($s)
{
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++)
        $w += $cw[$s[$i]];
    return $w * $this->FontSize / 1000;
}

function SetLineWidth($width)
{
    $this->LineWidth = $width;
    if($this->page>0)
        $this->_out(sprintf('%.2F w',$width*$this->k));
}

function Line($x1, $y1, $x2, $y2)
{
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Rect($x, $y, $w, $h, $style='')
{
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AutoPageBreak)
    {
        $x = $this->x;
        $ws = $this->ws;
        if($ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
        $this->x = $x;
        if($ws>0)
        {
            $this->ws = $ws;
            $this->_out(sprintf('%.3F Tw',$ws*$k));
        }
    }
    if($w==0)
        $w = $this->w - $this->rMargin - $this->x;
    $s = '';
    if($fill || $border==1)
    {
        if($fill)
            $op = ($border==1) ? 'B' : 'f';
        else
            $op = 'S';
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border,'L')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        if(strpos($border,'T')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        if(strpos($border,'R')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        if(strpos($border,'B')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if($txt!=='')
    {
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        if($align=='R')
            $dx = $w - $this->cMargin - $this->GetStringWidth($txt);
        elseif($align=='C')
            $dx = ($w - $this->GetStringWidth($txt)) / 2;
        else
            $dx = $this->cMargin;
        if($this->ColorFlag)
            $s .= 'q '.$this->TextColor.' ';
        $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$this->_escape($txt));
        if($this->underline)
            $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
        if($this->ColorFlag)
            $s .= ' Q';
        if($link)
            $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
    }
    if($s)
        $this->_out($s);
    $this->lasth = $h;
    if($ln>0)
    {
        $this->y += $h;
        if($ln==1)
            $this->x = $this->lMargin;
    }
    else
        $this->x += $w;
}

function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    if(!isset($this->CurrentFont))
        $this->Error('No font has been set');
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r",'',(string)$txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $b = 0;
    if($border)
    {
        if($border==1)
        {
            $border = 'LTRB';
            $b = 'LRT';
            $b2 = 'LR';
        }
        else
        {
            $b2 = '';
            if(strpos($border,'L')!==false)
                $b2 .= 'L';
            if(strpos($border,'R')!==false)
                $b2 .= 'R';
            $b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
        }
    }
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $ns = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            if($this->ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
                $b = $b2;
            continue;
        }
        if($c==' ')
        {
            $sep = $i;
            $ls = $l;
            $ns++;
        }
        $l += $cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            else
            {
                if($align=='J')
                {
                    $this->ws = ($ns>1) ? ($wmax-$ls) / 1000 * $this->FontSize / ($ns-1) : 0;
                    $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
                $b = $b2;
        }
        else
            $i++;
    }
    if($this->ws>0)
    {
        $this->ws = 0;
        $this->_out('0 Tw');
    }
    if($border && strpos($border,'B')!==false)
        $b .= 'B';
    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    $this->x = $this->lMargin;
}

function Ln($h=null)
{
    $this->x = $this->lMargin;
    if($h===null)
        $this->y += $this->lasth;
    else
        $this->y += $h;
}

function Output($dest='', $name='', $isUTF8=false)
{
    if($this->state<3)
        $this->Close();
    if($dest=='')
    {
        if($name=='')
        {
            $name = 'doc.pdf';
            $dest = 'I';
        }
        else
            $dest = 'F';
    }
    if($isUTF8)
        $name = $this->_UTF8toUTF16($name);
    switch(strtoupper($dest))
    {
        case 'I':
            $this->_checkoutput();
            if(PHP_SAPI!='cli')
            {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="'.$name.'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
            }
            echo $this->buffer;
            break;
        case 'D':
            $this->_checkoutput();
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="'.$name.'"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $this->buffer;
            break;
        case 'F':
            if(!file_put_contents($name,$this->buffer))
                $this->Error('Unable to create output file: '.$name);
            break;
        case 'S':
            return $this->buffer;
        default:
            $this->Error('Incorrect output destination: '.$dest);
    }
    return '';
}

protected function _getpagesize($size)
{
    if(is_string($size))
    {
        $size = strtolower($size);
        if(!isset($this->StdPageSizes[$size]))
            $this->Error('Unknown page size: '.$size);
        $a = $this->StdPageSizes[$size];
        return array($a[0]/$this->k, $a[1]/$this->k);
    }
    else
    {
        if($size[0]>$size[1])
            return array($size[1], $size[0]);
        else
            return $size;
    }
}

protected function _beginpage($orientation, $size, $rotation)
{
    $this->page++;
    $this->pages[$this->page] = '';
    $this->state = 2;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->FontFamily = '';
    if(!$orientation)
        $orientation = $this->DefOrientation;
    else
        $orientation = strtoupper($orientation[0]);
    if(!$size)
        $size = $this->DefPageSize;
    else
        $size = $this->_getpagesize($size);
    if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1])
    {
        if($orientation=='P')
        {
            $this->w = $size[0];
            $this->h = $size[1];
        }
        else
        {
            $this->w = $size[1];
            $this->h = $size[0];
        }
        $this->wPt = $this->w * $this->k;
        $this->hPt = $this->h * $this->k;
        $this->PageBreakTrigger = $this->h - $this->bMargin;
        $this->CurOrientation = $orientation;
        $this->CurPageSize = $size;
    }
    if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
        $this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);
    if($rotation!=0)
    {
        if($rotation%90!=0)
            $this->Error('Incorrect rotation value: '.$rotation);
        $this->CurRotation = $rotation;
        $this->PageInfo[$this->page]['rotation'] = $rotation;
    }
}

protected function _endpage()
{
    $this->state = 1;
}

protected function _escape($s)
{
    $s = str_replace('\\','\\\\',$s);
    $s = str_replace('(','\\(',$s);
    $s = str_replace(')','\\)',$s);
    $s = str_replace("\r",'\\r',$s);
    return $s;
}

protected function _dounderline($x, $y, $txt)
{
    $up = $this->CurrentFont['up'];
    $ut = $this->CurrentFont['ut'];
    $w = $this->GetStringWidth($txt) + $this->ws * substr_count($txt,' ');
    return sprintf('%.2F %.2F %.2F %.2F re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
}

protected function _getcorefont($key)
{
    static $fontnames = array(
        'courier'=>'Courier', 'courierB'=>'Courier-Bold', 'courierI'=>'Courier-Oblique', 'courierBI'=>'Courier-BoldOblique',
        'helvetica'=>'Helvetica', 'helveticaB'=>'Helvetica-Bold', 'helveticaI'=>'Helvetica-Oblique', 'helveticaBI'=>'Helvetica-BoldOblique',
        'times'=>'Times-Roman', 'timesB'=>'Times-Bold', 'timesI'=>'Times-Italic', 'timesBI'=>'Times-BoldItalic',
        'symbol'=>'Symbol', 'zapfdingbats'=>'ZapfDingbats'
    );
    return $fontnames[$key];
}

protected function _getcorefontwidths($key)
{
    static $widths = array(
        'courier'=>array(0=>600,1=>600,2=>600,3=>600,4=>600,5=>600,6=>600,7=>600,8=>600,9=>600,10=>600,11=>600,12=>600,13=>600,14=>600,15=>600,16=>600,17=>600,18=>600,19=>600,20=>600,21=>600,22=>600,23=>600,24=>600,25=>600,26=>600,27=>600,28=>600,29=>600,30=>600,31=>600,' '=>600,'!'=>600,'"'=>600,'#'=>600,'$'=>600,'%'=>600,'&'=>600,'\''=>600,'('=>600,')'=>600,'*'=>600,'+'=>600,','=>600,'-'=>600,'.'=>600,'/'=>600,'0'=>600,'1'=>600,'2'=>600,'3'=>600,'4'=>600,'5'=>600,'6'=>600,'7'=>600,'8'=>600,'9'=>600,':'=>600,';'=>600,'<'=>600,'='=>600,'>'=>600,'?'=>600,'@'=>600,'A'=>600,'B'=>600,'C'=>600,'D'=>600,'E'=>600,'F'=>600,'G'=>600,'H'=>600,'I'=>600,'J'=>600,'K'=>600,'L'=>600,'M'=>600,'N'=>600,'O'=>600,'P'=>600,'Q'=>600,'R'=>600,'S'=>600,'T'=>600,'U'=>600,'V'=>600,'W'=>600,'X'=>600,'Y'=>600,'Z'=>600,'['=>600,'\\'=>600,']'=>600,'^'=>600,'_'=>600,'`'=>600,'a'=>600,'b'=>600,'c'=>600,'d'=>600,'e'=>600,'f'=>600,'g'=>600,'h'=>600,'i'=>600,'j'=>600,'k'=>600,'l'=>600,'m'=>600,'n'=>600,'o'=>600,'p'=>600,'q'=>600,'r'=>600,'s'=>600,'t'=>600,'u'=>600,'v'=>600,'w'=>600,'x'=>600,'y'=>600,'z'=>600,'{'=>600,'|'=>600,'}'=>600,'~'=>600,127=>600,128=>600,129=>600,130=>600,131=>600,132=>600,133=>600,134=>600,135=>600,136=>600,137=>600,138=>600,139=>600,140=>600,141=>600,142=>600,143=>600,144=>600,145=>600,146=>600,147=>600,148=>600,149=>600,150=>600,151=>600,152=>600,153=>600,154=>600,155=>600,156=>600,157=>600,158=>600,159=>600,160=>600,161=>600,162=>600,163=>600,164=>600,165=>600,166=>600,167=>600,168=>600,169=>600,170=>600,171=>600,172=>600,173=>600,174=>600,175=>600,176=>600,177=>600,178=>600,179=>600,180=>600,181=>600,182=>600,183=>600,184=>600,185=>600,186=>600,187=>600,188=>600,189=>600,190=>600,191=>600,192=>600,193=>600,194=>600,195=>600,196=>600,197=>600,198=>600,199=>600,200=>600,201=>600,202=>600,203=>600,204=>600,205=>600,206=>600,207=>600,208=>600,209=>600,210=>600,211=>600,212=>600,213=>600,214=>600,215=>600,216=>600,217=>600,218=>600,219=>600,220=>600,221=>600,222=>600,223=>600,224=>600,225=>600,226=>600,227=>600,228=>600,229=>600,230=>600,231=>600,232=>600,233=>600,234=>600,235=>600,236=>600,237=>600,238=>600,239=>600,240=>600,241=>600,242=>600,243=>600,244=>600,245=>600,246=>600,247=>600,248=>600,249=>600,250=>600,251=>600,252=>600,253=>600,254=>600,255=>600),
        'helvetica'=>array(0=>278,1=>278,2=>278,3=>278,4=>278,5=>278,6=>278,7=>278,8=>278,9=>278,10=>278,11=>278,12=>278,13=>278,14=>278,15=>278,16=>278,17=>278,18=>278,19=>278,20=>278,21=>278,22=>278,23=>278,24=>278,25=>278,26=>278,27=>278,28=>278,29=>278,30=>278,31=>278,' '=>278,'!'=>278,'"'=>355,'#'=>556,'$'=>556,'%'=>889,'&'=>667,'\''=>191,'('=>333,')'=>333,'*'=>389,'+'=>584,','=>278,'-'=>333,'.'=>278,'/'=>278,'0'=>556,'1'=>556,'2'=>556,'3'=>556,'4'=>556,'5'=>556,'6'=>556,'7'=>556,'8'=>556,'9'=>556,':'=>278,';'=>278,'<'=>584,'='=>584,'>'=>584,'?'=>556,'@'=>1015,'A'=>667,'B'=>667,'C'=>722,'D'=>722,'E'=>667,'F'=>611,'G'=>778,'H'=>722,'I'=>278,'J'=>500,'K'=>667,'L'=>556,'M'=>833,'N'=>722,'O'=>778,'P'=>667,'Q'=>778,'R'=>722,'S'=>667,'T'=>611,'U'=>722,'V'=>667,'W'=>944,'X'=>667,'Y'=>667,'Z'=>611,'['=>278,'\\'=>278,']'=>278,'^'=>469,'_'=>556,'`'=>333,'a'=>556,'b'=>556,'c'=>500,'d'=>556,'e'=>556,'f'=>278,'g'=>556,'h'=>556,'i'=>222,'j'=>222,'k'=>500,'l'=>222,'m'=>833,'n'=>556,'o'=>556,'p'=>556,'q'=>556,'r'=>333,'s'=>500,'t'=>278,'u'=>556,'v'=>500,'w'=>722,'x'=>500,'y'=>500,'z'=>500,'{'=>334,'|'=>260,'}'=>334,'~'=>584,127=>350,128=>556,129=>350,130=>222,131=>556,132=>333,133=>1000,134=>556,135=>556,136=>333,137=>1000,138=>667,139=>333,140=>1000,141=>350,142=>611,143=>350,144=>350,145=>222,146=>222,147=>333,148=>333,149=>350,150=>556,151=>1000,152=>333,153=>1000,154=>500,155=>333,156=>944,157=>350,158=>500,159=>667,160=>278,161=>333,162=>556,163=>556,164=>556,165=>556,166=>260,167=>556,168=>333,169=>737,170=>370,171=>556,172=>584,173=>333,174=>737,175=>333,176=>400,177=>584,178=>333,179=>333,180=>333,181=>556,182=>537,183=>278,184=>333,185=>333,186=>365,187=>556,188=>834,189=>834,190=>834,191=>611,192=>667,193=>667,194=>667,195=>667,196=>667,197=>667,198=>1000,199=>722,200=>667,201=>667,202=>667,203=>667,204=>278,205=>278,206=>278,207=>278,208=>722,209=>722,210=>778,211=>778,212=>778,213=>778,214=>778,215=>584,216=>778,217=>722,218=>722,219=>722,220=>722,221=>667,222=>667,223=>611,224=>556,225=>556,226=>556,227=>556,228=>556,229=>556,230=>889,231=>500,232=>556,233=>556,234=>556,235=>556,236=>278,237=>278,238=>278,239=>278,240=>556,241=>556,242=>556,243=>556,244=>556,245=>556,246=>556,247=>584,248=>611,249=>556,250=>556,251=>556,252=>556,253=>500,254=>556,255=>500),
        'times'=>array(0=>250,1=>250,2=>250,3=>250,4=>250,5=>250,6=>250,7=>250,8=>250,9=>250,10=>250,11=>250,12=>250,13=>250,14=>250,15=>250,16=>250,17=>250,18=>250,19=>250,20=>250,21=>250,22=>250,23=>250,24=>250,25=>250,26=>250,27=>250,28=>250,29=>250,30=>250,31=>250,' '=>250,'!'=>333,'"'=>408,'#'=>500,'$'=>500,'%'=>833,'&'=>778,'\''=>180,'('=>333,')'=>333,'*'=>500,'+'=>564,','=>250,'-'=>333,'.'=>250,'/'=>278,'0'=>500,'1'=>500,'2'=>500,'3'=>500,'4'=>500,'5'=>500,'6'=>500,'7'=>500,'8'=>500,'9'=>500,':'=>278,';'=>278,'<'=>564,'='=>564,'>'=>564,'?'=>444,'@'=>921,'A'=>722,'B'=>667,'C'=>667,'D'=>722,'E'=>611,'F'=>556,'G'=>722,'H'=>722,'I'=>333,'J'=>389,'K'=>722,'L'=>611,'M'=>889,'N'=>722,'O'=>722,'P'=>556,'Q'=>722,'R'=>667,'S'=>556,'T'=>611,'U'=>722,'V'=>722,'W'=>944,'X'=>722,'Y'=>722,'Z'=>611,'['=>333,'\\'=>278,']'=>333,'^'=>469,'_'=>500,'`'=>333,'a'=>444,'b'=>500,'c'=>444,'d'=>500,'e'=>444,'f'=>278,'g'=>500,'h'=>500,'i'=>278,'j'=>278,'k'=>500,'l'=>278,'m'=>778,'n'=>500,'o'=>500,'p'=>500,'q'=>500,'r'=>333,'s'=>389,'t'=>278,'u'=>500,'v'=>500,'w'=>722,'x'=>500,'y'=>500,'z'=>444,'{'=>480,'|'=>200,'}'=>480,'~'=>541,127=>350,128=>500,129=>350,130=>333,131=>500,132=>444,133=>1000,134=>500,135=>500,136=>333,137=>1000,138=>556,139=>333,140=>889,141=>350,142=>611,143=>350,144=>350,145=>333,146=>333,147=>444,148=>444,149=>350,150=>500,151=>1000,152=>333,153=>980,154=>389,155=>333,156=>722,157=>350,158=>444,159=>722,160=>250,161=>333,162=>500,163=>500,164=>500,165=>500,166=>200,167=>500,168=>333,169=>760,170=>276,171=>500,172=>564,173=>333,174=>760,175=>333,176=>400,177=>564,178=>300,179=>300,180=>333,181=>500,182=>453,183=>250,184=>333,185=>300,186=>310,187=>500,188=>750,189=>750,190=>750,191=>444,192=>722,193=>722,194=>722,195=>722,196=>722,197=>722,198=>889,199=>667,200=>611,201=>611,202=>611,203=>611,204=>333,205=>333,206=>333,207=>333,208=>722,209=>722,210=>722,211=>722,212=>722,213=>722,214=>722,215=>564,216=>722,217=>722,218=>722,219=>722,220=>722,221=>722,222=>556,223=>500,224=>444,225=>444,226=>444,227=>444,228=>444,229=>444,230=>667,231=>444,232=>444,233=>444,234=>444,235=>444,236=>278,237=>278,238=>278,239=>278,240=>500,241=>500,242=>500,243=>500,244=>500,245=>500,246=>500,247=>564,248=>500,249=>500,250=>500,251=>500,252=>500,253=>500,254=>500,255=>500)
    );
    return $widths[$key];
}

protected function _out($s)
{
    if($this->state==2)
        $this->pages[$this->page] .= $s."\n";
    elseif($this->state<2)
        $this->buffer .= $s."\n";
    else
        $this->Error('Content cannot be added to a closed document');
}

protected function _checkoutput()
{
    if(PHP_SAPI!='cli')
    {
        if(headers_sent($file,$line))
            $this->Error("Some data has already been output, can't send PDF file (output started at $file:$line)");
    }
    if(ob_get_length())
    {
        if(preg_match('/^(\xEF\xBB\xBF)?\s*$/',ob_get_contents()))
        {
            ob_end_clean();
        }
        else
            $this->Error("Some data has already been output, can't send PDF file");
    }
}

protected function _putpages()
{
    $nb = $this->page;
    for($n=1;$n<=$nb;$n++)
        $this->PageInfo[$n]['n'] = $this->n + 1 + 2 * ($n - 1);
    for($n=1;$n<=$nb;$n++)
        $this->_putpage($n);
}

protected function _putpage($n)
{
    $this->_newobj();
    $this->_out('<</Type /Page');
    $this->_out('/Parent 1 0 R');
    if(isset($this->PageInfo[$n]['size']))
        $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$this->PageInfo[$n]['size'][0],$this->PageInfo[$n]['size'][1]));
    if(isset($this->PageInfo[$n]['rotation']))
        $this->_out('/Rotate '.$this->PageInfo[$n]['rotation']);
    $this->_out('/Resources 2 0 R');
    $this->_out('/Contents '.($this->n+1).' 0 R>>');
    $this->_out('endobj');
    $p = $this->pages[$n];
    if($this->compress)
    {
        $p = gzcompress($p);
    }
    $this->_newobj();
    $this->_out('<<'.(($this->compress) ? '/Filter /FlateDecode ' : '').'/Length '.strlen($p).'>>');
    $this->_putstream($p);
    $this->_out('endobj');
}

protected function _putfonts()
{
    foreach($this->fonts as $k=>$font)
    {
        if(isset($font['type']) && $font['type']=='core')
        {
            $this->fonts[$k]['n'] = $this->n + 1;
            $this->_newobj();
            $this->_out('<</Type /Font');
            $this->_out('/BaseFont /'.$font['name']);
            $this->_out('/Subtype /Type1');
            if($font['name']!='Symbol' && $font['name']!='ZapfDingbats')
                $this->_out('/Encoding /WinAnsiEncoding');
            $this->_out('>>');
            $this->_out('endobj');
        }
    }
}

protected function _putresources()
{
    $this->_putfonts();
    $this->offsets[2] = strlen($this->buffer);
    $this->_out('2 0 obj');
    $this->_out('<</ProcSet [/PDF /Text]');
    $this->_out('/Font <<');
    foreach($this->fonts as $font)
        $this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
    $this->_out('>>');
    $this->_out('>>');
    $this->_out('endobj');
}

protected function _putinfo()
{
    $this->metadata['Producer'] = 'FPDF '.FPDF_VERSION;
    $this->metadata['CreationDate'] = 'D:'.@date('YmdHis');
    foreach($this->metadata as $key=>$value)
        $this->_out('/'.$key.' '.$this->_textstring($value));
}

protected function _putcatalog()
{
    $n = $this->PageInfo[1]['n'];
    $this->_out('/Type /Catalog');
    $this->_out('/Pages 1 0 R');
    if($this->ZoomMode=='fullpage')
        $this->_out('/OpenAction ['.$n.' 0 R /Fit]');
    elseif($this->ZoomMode=='fullwidth')
        $this->_out('/OpenAction ['.$n.' 0 R /FitH null]');
    elseif($this->ZoomMode=='real')
        $this->_out('/OpenAction ['.$n.' 0 R /XYZ null null 1]');
    elseif(!is_string($this->ZoomMode))
        $this->_out('/OpenAction ['.$n.' 0 R /XYZ null null '.sprintf('%.2F',$this->ZoomMode/100).']');
    if($this->LayoutMode=='single')
        $this->_out('/PageLayout /SinglePage');
    elseif($this->LayoutMode=='continuous')
        $this->_out('/PageLayout /OneColumn');
    elseif($this->LayoutMode=='two')
        $this->_out('/PageLayout /TwoColumnLeft');
}

protected function _putheader()
{
    $this->_out('%PDF-'.$this->PDFVersion);
}

protected function _puttrailer()
{
    $this->_out('/Size '.($this->n+1));
    $this->_out('/Root '.$this->n.' 0 R');
    $this->_out('/Info '.($this->n-1).' 0 R');
}

protected function _enddoc()
{
    $this->_putheader();
    $this->_putpages();
    $this->_putresources();
    $this->_newobj();
    $this->_out('<<');
    $this->_putinfo();
    $this->_out('>>');
    $this->_out('endobj');
    $this->_newobj();
    $this->_out('<<');
    $this->_putcatalog();
    $this->_out('>>');
    $this->_out('endobj');
    $o = strlen($this->buffer);
    $this->_out('xref');
    $this->_out('0 '.($this->n+1));
    $this->_out('0000000000 65535 f ');
    for($i=1;$i<=$this->n;$i++)
        $this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
    $this->_out('trailer');
    $this->_out('<<');
    $this->_puttrailer();
    $this->_out('>>');
    $this->_out('startxref');
    $this->_out($o);
    $this->_out('%%EOF');
    $this->state = 3;
}

protected function _newobj()
{
    $this->n++;
    $this->offsets[$this->n] = strlen($this->buffer);
    $this->_out($this->n.' 0 obj');
}

protected function _putstream($s)
{
    $this->_out('stream');
    $this->_out($s);
    $this->_out('endstream');
}

protected function _textstring($s)
{
    if(!$this->_isascii($s))
        $s = $this->_UTF8toUTF16($s);
    return '('.$this->_escape($s).')';
}

protected function _isascii($s)
{
    $nb = strlen($s);
    for($i=0;$i<$nb;$i++)
    {
        if(ord($s[$i])>127)
            return false;
    }
    return true;
}

protected function _UTF8toUTF16($s)
{
    $res = "\xFE\xFF";
    $nb = strlen($s);
    $i = 0;
    while($i<$nb)
    {
        $c1 = ord($s[$i++]);
        if($c1>=224)
        {
            $c2 = ord($s[$i++]);
            $c3 = ord($s[$i++]);
            $res .= chr((($c1 & 0x0F)<<4) + (($c2 & 0x3C)>>2));
            $res .= chr((($c2 & 0x03)<<6) + ($c3 & 0x3F));
        }
        elseif($c1>=192)
        {
            $c2 = ord($s[$i++]);
            $res .= chr(($c1 & 0x1C)>>2);
            $res .= chr((($c1 & 0x03)<<6) + ($c2 & 0x3F));
        }
        else
        {
            $res .= "\0".chr($c1);
        }
    }
    return $res;
}

function Close()
{
    if($this->state==3)
        return;
    if($this->page==0)
        $this->AddPage();
    $this->InFooter = true;
    $this->Footer();
    $this->InFooter = false;
    $this->_endpage();
    $this->_enddoc();
}

function Error($msg)
{
    throw new Exception('FPDF error: '.$msg);
}
}

?>
