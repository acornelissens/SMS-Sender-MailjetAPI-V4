<!DOCTYPE html>
<html>
<head>
  <title>SMS SEND</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  

  <div class="container">
      <div class="column left">            

        <div id="informations">
          <ul></ul>
        </div>
        <div id="progression"></div>
        <br/>

        <form>
          <label for="from"> From :<br/></label>
          <input id="from" type="text" name="from" placeholder="Nom du destinataire"/>
          <br/>
          <label for="from"> Message :<br/></label>
          <textarea id="message" rows="8" name="message" placeholder="Votre message"></textarea>
          <br/>
          <button id="send">Envoyer</button>
        </form>
      </div>
      <div class="column right">            

        <div id="results">
          <ul></ul>
        </div>

      </div>
    </div>
  </div>


  <script src="/node_modules/jquery/dist/jquery.min.js"></script>
  <script>

    var numArray = [];
    var incNum = 0;
    var totalNum = 0;

    //Recuperation de la liste pour boucler en ajax    
    var formdata = new FormData();    
    formdata.append('action', 'get_list');
    doAjax(formdata);

    //On SEND
    $("#send").click(function(e){

      e.preventDefault();
      if (confirm("Confirmation de l'envoi des SMS")) {

        //Inscription de l'heure d depart
        var d = new Date();
        $("#informations ul").append("<li>Lancement des envois à "+d.toLocaleTimeString()+"</li>");

        formdata = new FormData();
        formdata.append('action', 'send_SMS');
        formdata.append('from',  $("#from").val());
        formdata.append('message',  $("#message").val());

        var time = 0;
        numArray.forEach(function(item){

            setTimeout(function() {
                formdata.append("numero",item);
                doAjax(formdata);

            }, time);
            time += 200;
        });                 

      } else {
        alert("Operation annulée");
      }      
    })

    function doAjax(formData){

       $.ajax({
        type: "POST",
        data: formdata,
        url: "APISMSMailjetHandler.php",    
        contentType:false,
        processData:false,

        success: function(response) {

          if(response.status === 'success'){

            switch(response.action){
              case "get_list":

                totalNum = response.data.count;
                $("#informations ul").append("<li>La liste contient "+totalNum+" numéros</li>");
                $("#informations ul").append("<li>L'ID de la contact list est le N°"+response.data.contactListID+"</li>");
                numArray = response.data.file;               

                //Default Variables
                $("#from").val(response.data.defaultFrom);
                $("#message").val(response.data.defaultMessage);

              break;
              case "send_SMS":

                //Progression
                incNum++;
                var percent = Math.floor((incNum/totalNum)*100);
                $('#progression').html("SMS envoyés : "+incNum+"/"+totalNum+"   ( <strong>"+percent+"%</strong> )");

                //Results
                $('#results').prepend("<li>"+response.data.result+"</li>");

              break;
            }
              

           }else if(response.status == 'error'){

              $('#results').prepend("<li>"+response.data.result+"</li>");
          }          
        },
          error : function( error ){
            console.log( error );
        }    
      });    
    }

  </script>

</body>
</html>

