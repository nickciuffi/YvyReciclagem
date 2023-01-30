<!-- Layout -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
 
 <?php
  
 /* Valores recebidos do formulário  */
 $arquivo = $_FILES['arquivo'];
 $nome = $_POST['nome'];
 $replyto = $_POST['email']; // Email que será respondido
 $mensagem_form = $_POST['mensagem'];
 $assunto = $_POST['assunto'];
 
 /* Destinatário e remetente - EDITAR SOMENTE ESTE BLOCO DO CÓDIGO */
 $to = $replyto;
 $to2 = "micael@fluencydesign.com.br";
 $remetente = "micael@fluencydesign.com.br"; // Deve ser um email válido do domínio
  
 /* Cabeçalho da mensagem  */
 $boundary = "XYZ-" . date("dmYis") . "-ZYX";
 $headers = "MIME-Version: 1.0\n";
 $headers.= "From: $remetente\n";
 $headers.= "Reply-To: $replyto\n";
 $headers.= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";  
 $headers.= "$boundary\n"; 
  
 /* Layout da mensagem  */
 $corpo_mensagem = " 
 <br>Formulário via site
 <br>--------------------------------------------<br>
 <br><strong>Nome:</strong> $nome
 <br><strong>Email:</strong> $replyto
 <br><strong>Assunto:</strong> $assunto
 <br><strong>Mensagem:</strong> $mensagem_form
 <br><br>--------------------------------------------
 <br> $arquivo
 ";
 $corpo_mensagem2 = " 
 <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'><html xmlns='http://www.w3.org/1999/xhtml'><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><meta name='viewport' content='width=device-width'></head><body style='-moz-box-sizing:border-box;-ms-text-size-adjust:100%;-webkit-box-sizing:border-box;-webkit-text-size-adjust:100%;Margin:0;box-sizing:border-box;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;min-width:100%;padding:0;text-align:left;width:100%!important'><style>@media only screen{html{min-height:100%;background:#f3f3f3}}@media only screen and (max-width:596px){.small-float-center{margin:0 auto!important;float:none!important;text-align:center!important}.small-text-center{text-align:center!important}.small-text-left{text-align:left!important}.small-text-right{text-align:right!important}}@media only screen and (max-width:596px){.hide-for-large{display:block!important;width:auto!important;overflow:visible!important;max-height:none!important;font-size:inherit!important;line-height:inherit!important}}@media only screen and (max-width:596px){table.body table.container .hide-for-large,table.body table.container .row.hide-for-large{display:table!important;width:100%!important}}@media only screen and (max-width:596px){table.body table.container .callout-inner.hide-for-large{display:table-cell!important;width:100%!important}}@media only screen and (max-width:596px){table.body table.container .show-for-large{display:none!important;width:0;mso-hide:all;overflow:hidden}}@media only screen and (max-width:596px){table.body img{width:auto;height:auto}table.body center{min-width:0!important}table.body .container{width:95%!important}table.body .column,table.body .columns{height:auto!important;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;padding-left:16px!important;padding-right:16px!important}table.body .column .column,table.body .column .columns,table.body .columns .column,table.body .columns .columns{padding-left:0!important;padding-right:0!important}table.body .collapse .column,table.body .collapse .columns{padding-left:0!important;padding-right:0!important}td.small-1,th.small-1{display:inline-block!important;width:8.33333%!important}td.small-2,th.small-2{display:inline-block!important;width:16.66667%!important}td.small-3,th.small-3{display:inline-block!important;width:25%!important}td.small-4,th.small-4{display:inline-block!important;width:33.33333%!important}td.small-5,th.small-5{display:inline-block!important;width:41.66667%!important}td.small-6,th.small-6{display:inline-block!important;width:50%!important}td.small-7,th.small-7{display:inline-block!important;width:58.33333%!important}td.small-8,th.small-8{display:inline-block!important;width:66.66667%!important}td.small-9,th.small-9{display:inline-block!important;width:75%!important}td.small-10,th.small-10{display:inline-block!important;width:83.33333%!important}td.small-11,th.small-11{display:inline-block!important;width:91.66667%!important}td.small-12,th.small-12{display:inline-block!important;width:100%!important}.column td.small-12,.column th.small-12,.columns td.small-12,.columns th.small-12{display:block!important;width:100%!important}table.body td.small-offset-1,table.body th.small-offset-1{margin-left:8.33333%!important;Margin-left:8.33333%!important}table.body td.small-offset-2,table.body th.small-offset-2{margin-left:16.66667%!important;Margin-left:16.66667%!important}table.body td.small-offset-3,table.body th.small-offset-3{margin-left:25%!important;Margin-left:25%!important}table.body td.small-offset-4,table.body th.small-offset-4{margin-left:33.33333%!important;Margin-left:33.33333%!important}table.body td.small-offset-5,table.body th.small-offset-5{margin-left:41.66667%!important;Margin-left:41.66667%!important}table.body td.small-offset-6,table.body th.small-offset-6{margin-left:50%!important;Margin-left:50%!important}table.body td.small-offset-7,table.body th.small-offset-7{margin-left:58.33333%!important;Margin-left:58.33333%!important}table.body td.small-offset-8,table.body th.small-offset-8{margin-left:66.66667%!important;Margin-left:66.66667%!important}table.body td.small-offset-9,table.body th.small-offset-9{margin-left:75%!important;Margin-left:75%!important}table.body td.small-offset-10,table.body th.small-offset-10{margin-left:83.33333%!important;Margin-left:83.33333%!important}table.body td.small-offset-11,table.body th.small-offset-11{margin-left:91.66667%!important;Margin-left:91.66667%!important}table.body table.columns td.expander,table.body table.columns th.expander{display:none!important}table.body .right-text-pad,table.body .text-pad-right{padding-left:10px!important}table.body .left-text-pad,table.body .text-pad-left{padding-right:10px!important}table.menu{width:100%!important}table.menu td,table.menu th{width:auto!important;display:inline-block!important}table.menu.small-vertical td,table.menu.small-vertical th,table.menu.vertical td,table.menu.vertical th{display:block!important}table.menu[align=center]{width:auto!important}table.button.small-expand,table.button.small-expanded{width:100%!important}table.button.small-expand table,table.button.small-expanded table{width:100%}table.button.small-expand table a,table.button.small-expanded table a{text-align:center!important;width:100%!important;padding-left:0!important;padding-right:0!important}table.button.small-expand center,table.button.small-expanded center{min-width:0}} .container{background-image: linear-gradient(green, blue)} .row{color:white} tr{color:white}</style><table class='body' data-made-with-foundation='' style='Margin:0;background:#f3f3f3;border-collapse:collapse;border-spacing:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%'><tbody><tr style='padding:0;text-align:left;vertical-align:top'><td class='float-center' style='-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0 auto;border-collapse:collapse!important;color:#0a0a0a;float:none;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0 auto;padding:0;text-align:center;vertical-align:top;word-wrap:break-word' valign='top' align='center'><center style='min-width:580px;width:100%'><table class='container' style='Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px'></table>Vimos que você fez um cadastro no site da Yvy<table class='row' style='border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%'><tbody><tr style='padding:0;text-align:left;vertical-align:top'><th class='expander' style='Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:1.3;margin:10;padding:0!important;text-align:left;visibility:hidden;width:0'></th></tr></tbody></table>Entraremos em contato assim que possível<table class='row' style='border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%'><tbody><tr style='padding:0;text-align:left;vertical-align:top'><th class='expander' style='Margin:0;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0'></th></tr></tbody></table></center></td></tr></tbody></table></body></html>
 ";
  
 /* Função que codifica o anexo para poder ser enviado na mensagem  */
 if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
  
     $fp = fopen($_FILES["arquivo"]["tmp_name"],"rb"); // Abri o arquivo enviado.
  $anexo = fread($fp,filesize($_FILES["arquivo"]["tmp_name"])); // Le o arquivo aberto na linha anterior
  $anexo = base64_encode($anexo); // Codifica os dados com MIME para o e-mail 

  

  fclose($fp); // Fecha o arquivo aberto anteriormente
     $anexo = chunk_split($anexo); // Divide a variável do arquivo em pequenos pedaços para poder enviar
     $mensagem = "--$boundary\n"; // Nas linhas abaixo possuem os parâmetros de formatação e codificação, juntamente com a inclusão do arquivo anexado no corpo da mensagem
     $mensagem.= "Content-Transfer-Encoding: 8bits\n"; 
     $mensagem.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
     $mensagem.= "$corpo_mensagem\n"; 
     $mensagem.= "--$boundary\n"; 
     $mensagem.= "Content-Type: ".$arquivo["type"]."\n";  
     $mensagem.= "Content-Disposition: attachment; filename=\"".$arquivo["name"]."\"\n";  
     $mensagem.= "Content-Transfer-Encoding: base64\n\n";  
     $mensagem.= "$anexo\n";  
     $mensagem.= "--$boundary--\r\n"; 
     $mensagem2 = "--$boundary\n"; 
     $mensagem2.= "Content-Transfer-Encoding: 8bits\n"; 
     $mensagem2.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
     $mensagem2.= "$corpo_mensagem2\n"; 
    
 }
  else // Caso não tenha anexo
  {
  $mensagem = "--$boundary\n"; 
  $mensagem.= "Content-Transfer-Encoding: 8bits\n"; 
  $mensagem.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
  $mensagem.= "$corpo_mensagem\n";
  $mensagem2.= "--$boundary\n";
  $mensagem2.= "Content-Transfer-Encoding: 8bits\n"; 
  $mensagem2.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
  $mensagem2.= "$corpo_mensagem2\n"; 
 }
  
 /* Função que envia a mensagem  */
 if(mail($to2, $assunto, $mensagem, $headers) and mail($to, 'Retorno de cadastro', $mensagem2, $headers))
 {
    echo "<br><br><center><b><font color='green'>Mensagem enviada com sucesso";
 } 
  else
  {
  
  echo "<br><br><center><b><font color='red'>Ocorreu um erro ao enviar a mensagem!";
 }
 ?>