<?php
  $container = <<<EOT
<html>
<head>
<meta charset="windows-1252">
 <script src="../js/jquery-1.7.1.min.js"></script>
 <script src="../js/jquery.isotope.js"></script>
 <script src="../js/filtrify.js"></script>
 <script src="../js/main.js"></script>
<script src="../js/highlight.pack.js"></script>
<script src="../js/script.js"></script>
<script src="../js/filtrify.min.js"></script>
<script src="../js/jquery.isotope.min.js"></script>

<!-- Add fancyBox -->
<link rel="stylesheet" href="../../../_ferramentas/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="../../../_ferramentas/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<link rel="stylesheet" href="../css/style_f.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/sunburst.css">
<link rel="stylesheet" href="../css/filtrify.css">
</head>
<body>
<div id="dlg_frame" style="display:none;">
<div id="tools" style="height:22px;padding:0;margin:0px;width:640px;" align="right"><img id="close" style="cursor:pointer;" src="../img/close_btn.gif" /></div>
<div id="cols" style="width:640px;">
<div id="col1" style="width:310px;float:left;">
<div id="head" style="font-size:24px;font-weight:bold;padding-left:20px;padding-top:20px;">Titulo do Evento</div>
<div id="dta_evento" style="font-size:13px;font-weight:bold;padding-left:20px;">Data do Evento</div>
<div id="local" style="height:100px;padding:20px; overflow:auto;">DESCRI&Ccedil;&Atilde;O DO LOCAL DO EVENTO</div>
<div id="id_evento" style="font-size:10px;float:left;color:#e7e7e7;padding-left:20px;">id_evento</div>
<div id="response" style="margin-top:200px;background-color:red;color:white;"></div>
</div>
<form id="frm_programas" method="post">
<div id="col2" style="width:260px;height:350px;float:left;padding-top:20px;padding-left:50px;border:0px; border-left:1px dashed #A0A0A0;">
<div style="font-size:14px;font-weight:bold;line-height:105%;margin-bottom:10px;">Determine quais ser&atilde;o as Aulas do GROUNDWORKS, selecione abaixo:</div>

<label class="checkbox"><input type="checkbox" name="programa[]" id="BA" value="BA"/>&nbsp;BODYATTACK&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="BB" value="BB" />&nbsp;BODYBALANCE&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="BC" value="BC" />&nbsp;BODYCOMBAT&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="BJ" value="BJ" />&nbsp;BODYJAM&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="BP" value="BP" />&nbsp;BODYPUMP&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="BS" value="BS" />&nbsp;BODYSTEP&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="BV" value="BV" />&nbsp;BODYVIVE&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="CX" value="CX" />&nbsp;CXWORX&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="RPM" value="RPM" />&nbsp;RPM&trade;</label><br/>
<label class="checkbox"><input type="checkbox" name="programa[]" id="SB" value="SB" />&nbsp;SHBAM&trade;</label><br/>
</div>
</div>
<div id="footer" style="height:22px;padding:0;margin-right:10px;" align="right">
    <input id="apply" type="button" value="Aplicar"/>&nbsp;<input type="button" value="Cancelar" id="cancel" />
</div>
<input type="hidden" id="eventoid" value="" name="eventoid" />
<input type="hidden" id="descricao" value="" name="descricao" />
<input type="hidden" id="data_evento" value="" name="data_evento" />
</form>
</div>
<div id="wrap" style="width:90%;margin-left:10%;">
<div id="placeHolder"></div>
<ul id="container">
    {ITEMS}
</ul>
</div>
</body
></html>
EOT;


?>
