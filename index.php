<!DOCTYPE html>
<html lang="en">
<head>
  <title>Validador XML-TISS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

 <body>
<div class='container'>

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Validar XML do lote </h1>
            <p class="lead">*Obs: Baixe o xml do lote específico em Administrar Lotes</p>
        </div>
    </div>
    <form enctype="multipart/form-data" method="post">
        <div class="form-row">
            <div class="form-group col-4">
                versao: 
                <select class='form-control' name="versao" >
                    <option value="tissV3_05_00">3.05.00</opition>
                    <option value="tissV3_04_01">3.04.01</opition>
                    <option value="tissV3_04_00">3.04.00</opition>
                    <option value="tissV3_03_03">3.03.03</opition>
                    <option value="tissV3_03_02">3.03.02</opition>
                    <option value="tissV3_03_01">3.03.01</opition>
                    <option value="tissV3_02_02">3.02.02</opition>
                    <option value="tissV3_02_01">3.02.01</opition>
                    <option value="tissV3_02_00">3.02.00</opition>
                    <option value="tissV2_02_03">2.02.03</opition>
                    <option value="tissV2_02_02">2.02.02</opition>
                    <option value="tissV2_02_01">2.02.01</opition>
                    <option value="tissV2_01_03">2.01.03</opition>
                </select>
            </div>
            <div class="form-group col-6">
                xml: 
                <input id='file' class="form-control-file" type="file" name="userfile" required />
            </div>
            <div class="form-group col-2">
                <input class="btn btn-info" type="submit" />
            </div>
        </div>
    </form>

    <div id="hash"  class="alert alert-success" hidden role="alert">
        <div id="hashMsg"></div>
    </div>

    <div id="alerta" class="alert alert-success" hidden role="alert">
        <h4 id="alertatitulo" class="alert-heading"></h4>
        <hr>
        <div id="alertaMsg"></div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$('form').submit(function(event){
    let form = $('form')[0]; // You need to use standard javascript object here
    let formData = new FormData(form);
    
    $.ajax({
    url: 'validaxml.php',
    data: formData,
    type: 'POST',
    contentType: false, 
    processData: false, 
    success: function(response,status){
       if(response){
        let data = JSON.parse(response);
        let alerta = document.getElementById('alerta');
        let alertatitulo = document.getElementById('alertatitulo');
        let alertaMsg = document.getElementById('alertaMsg');
        let file = document.getElementById("file");

        let hash = document.getElementById('hash');
        let hashMsg = document.getElementById('hashMsg');

        alertaMsg.innerHTML = '';
        hashMsg.innerHTML = '';
        alerta.hidden = false;
        hash.hidden = false;
        file.value = "";

            
            hash.setAttribute("class",'alert alert-'+(data.hash.status==true?'success':'warning'))
            let p = document.createElement('p');
            p.innerText = (data.hash.status==true?"Hash válido: ":'Hash inválido: ')+data.hash.value
            hashMsg.appendChild(p);


            if(data.status=="0"){
                alerta.setAttribute("class",'alert alert-success');
                alertatitulo.innerText  = data.msg;
            }else{
                alerta.setAttribute("class",'alert alert-warning');
                alertatitulo.innerText  = 'Arquivo inválido';
                 p = document.createElement('p');
                    
                for (const item of data.Errors) {
                    let ul = document.createElement('ul');
                    ul.innerHTML += '<li><strong>Linha:</strong> '+item.linha+'</li>';
                    ul.innerHTML += '<li><strong>Mensagen:</strong>'+item.msg+'</li>';
                    p.appendChild(ul);
                }
                alertaMsg.appendChild(p);

            }
       }
    }
});


    return false;
}) ;   
</script>
 </body>
</html>