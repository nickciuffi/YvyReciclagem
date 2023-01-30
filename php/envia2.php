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
 $to2 = "cv@yvyreciclagem.com.br";
 $remetente = "cv@yvyreciclagem.com.br"; // Deve ser um email válido do domínio
  
 /* Cabeçalho da mensagem  */
 $boundary = "XYZ-" . date("dmYis") . "-ZYX";
 $headers = "MIME-Version: 1.0\n";
 $headers.= "From: $remetente\n";
 $headers.= "Reply-To: $replyto\n";
 $headers.= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";  
 $headers.= "$boundary\n"; 
  
 /* Layout da mensagem  */
 $corpo_mensagem = " 
 <br>Formulário - contato comercial - site Yvy Reciclagem
 <br>--------------------------------------------<br>
 <br><strong>Nome:</strong> $nome
 <br><strong>Email:</strong> $replyto
 <br><strong>Assunto:</strong> $assunto
 <br><strong>Mensagem:</strong> $mensagem_form
 <br><br>--------------------------------------------
 <br> $arquivo
 ";

 $corpo_mensagem2 = '
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <style>
        html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        }
        body {
            max-width: 100%;
            margin: 0 auto;
        }
        h2 {
            font-weight: 300;
            font-size: 16px;
            line-height: 200%;
            margin-left: 25px;
        }
        img {
            margin-left: 25px;
        }


    </style>

</head>

<body>
    <h2>Olá, recebemos sua mensagem.</h2>
    <h2>Entraremos em contato o mais breve possível. <br>
        Obrigado! <br>
        Equipe Yvy Reciclagem <br>
    </h2>
    <br>
    <img src="https://fluency.be/img/logo-yvy.png" alt="logo-yvy">
</body>
</html>
';
  
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
 if(mail($to2, $assunto, $mensagem, $headers) and mail($to, 'Recebemos seu formulário - Yvy Reciclagem', $mensagem2, $headers))
 {
    echo '<!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../img/favicon.png">
        <title>Email Recebido</title>
    
        <style>
            body {
                margin: 0;
                padding: 0;
            }
            .linha {
                min-width: 100%;
                height: 5px;
                background-color: #007D3E;
                margin: 0;
                padding: 0;
            }
            section {
                max-width: 350px;
                margin: 0 auto;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
                margin-top: 0px;
            }
            h2,h3 {
                font-weight: 400;
                font-size: 18px;
            }
            img {
                max-width: 100%;
            }
            h1,h2,h3,.simbolo {
                margin-left: 25px;
            }
            h2,h3 {
                line-height: 175%;
            }
            a {
                color: black;
                font-size: 16px;
                padding: 12px 18px 12px 0;
                opacity: .9;
                transition: .5s ease;
                text-decoration: underline;
            }
            a:hover {
                opacity: 1;
            }
        </style>
    
    </head>
    <body>
        <div class="linha"></div>
        <section>
        <div style="height: 50px;"></div>
        <img class="simbolo" src="../img/check.png" alt="Confirmado">
        <h1>Tudo certo!</h1>
        <h2>Formulário enviado com sucesso.</h2>
        <h3>Em breve entraremos em contato. <br>
            Obrigado! <br>
            Equipe Yvy Reciclagem</h3>
        <img class="simbolo" src="../img/logo-yvy.png" alt="Logo Yvy">
        <h3><a href="../">VOLTAR PARA A HOME</a></h3>
        </section>
    </body>
    </html>';
 } 
  else
  {
  
  echo '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
  <img style=" margin-top: 40px;" src="../img/logo-yvy.png" alt="Logo Yvy">
  <h2 style="font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif; color: rgb(0,129,71); text-align: center;">Ocorreu um erro ao enviar o formulário, tente novamente mais tarde.</h2>
  <img style="max-width: 100%;" src="../img/erro.png" alt="Email Não Enviado">
  <a href="../contato.html"><img src="../img/back.png" alt="Voltar"></a>
</div>';
 }
 ?>