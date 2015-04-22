<?php require_once( '../admin/cms.php' ); require('mysql.php');?>
<cms:template title='Survey' order='10' />
<!doctype html>

<html>

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">


<title>Survey | High Desert Homes</title>

<script type="text/javascript" src="../js/jquery.min.js"></script>

<script type="text/javascript" src="../js/bootstrap.min.js"></script>



<link href="../styles/survey.css" rel="stylesheet" type="text/css">

<link href="../styles/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("button").click(function () {
            var frame = $('iframe', window.parent.document);
            var height = jQuery(".container").height();
            frame.height(height + 15);
        });
    });
</script>

<script>
function resize(){
  var frame = $('iframe', window.parent.document);
  var height = jQuery(".container").height();
  frame.height(height + 15);
}
</script>

<script>

$(document).ready(function(){

  $('#stepOne form').submit(function (e) {
	  e.preventDefault();
	  $('#stepOne #submit').button('loading');
      $.post('stepSubmit.php?step=1', $('#stepOne form').serialize(), function(data){
        $('head').append(data);
        $('#stepOne').hide('slow');
        $('#stepTwo').slideDown('slow', function(){
          resize();
        });
	    });
    });

  

   $('#stepTwo #submit').click(function (e) {

	  e.preventDefault();

	  $('#stepTwo #submit').button('loading');

    $.post('stepSubmit.php?step=2&session_token='+session_token, $('#stepTwo form').serialize(), function(data){

        $('#stepTwo').hide('slow', function(){

        $('#stepThree').slideDown('slow');

      });

    });

  });

});

</script>

</head>



<body>

	<div class="container">

    	<div id="stepOne">

        <?php $result=mysqli_query($con, "SELECT ip FROM takers WHERE ip='$_SERVER[REMOTE_ADDR]'");
        $count=0;
        while($row=mysqli_fetch_array($result)){
          $count++;
        }
        if ($count >= 5){
          echo "<h2>You have taken the survey too many times.<h2>";
          exit;
        }
        ?>

            <form id="userInformation">
            
            <cms:editable name='welcome_text' desc="First text the visitor sees" type='richtext'>
              <h1>Your Information</h1>
            </cms:editable>
              <div class="form-group col-sm-6">

                <label for="firstName">First Name</label>

                <input type="text" required name="firstName" class="form-control" id="firstName" placeholder="First Name"/>

              </div>

              

              <div class="form-group col-sm-6">

                <label for="lastName">Last Name</label>

                <input type="text" class="form-control" required id="lastName" name="lastName" placeholder="Last Name"/>

              </div>

              

              <div class="form-group col-sm-6">

                <label for="houseNumber">House Number</label>

                <input type="text" class="form-control" id="houseNumber"  name="houseNumber" required placeholder="ex. 2357" />

              </div>

              

              <div class="form-group col-sm-6">

                <label for="houseStreet">Street</label>

                <input type="text" class="form-control" id="houseStreet" name="houseStreet" required placeholder="ex. 2350 East or Bumblebee Lane" />

              </div>

              

              <div class="form-group col-sm-6">

                <label for="houseCity">City</label>

                <input type="text" class="form-control" id="houseCity" name="houseCity" required placeholder="ex. Saint George" />

              </div>

              

              <div class="form-group col-sm-6">

                <label for="houseZip">ZIP Code</label>

                <input type="text" class="form-control" id="houseZip" name="houseZip" required placeholder="ex. 84790" />

              </div>

             

             <div class="form-group col-sm-12">

                  <button type="submit" id="submit" data-loading-text="Checking..." class="btn btn-default" autocomplete="off">

                  Continue >>

                </button>

             </div>

            </form>

        </div>

        

        <div id="stepTwo" style="display:none">
          <cms:editable name='question_text' desc="Text visitor sees when viewing questions" type='richtext'>
          	<h1>Questions</h1>
          </cms:editable>

        	<form>

            
            <?php 
              //Connect to DB and get questions
              
              $result = mysqli_query($con, "SELECT * FROM questions");
              while($row = mysqli_fetch_array($result)){
                $choices = json_decode($row['choices']);
                //print_r($choices);
                switch($row['type']){
                  case 'text':
                    echo "<div class='form-group col-sm-12'>

                              <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                              <input type='text' class='form-control' name='{$row[id]}' required placeholder='Text response' />

                          </div>";
                          break;
                  case 'yn':
                    echo "<div class='col-sm-12'>

                        <h4>{$row[question]}<small> {$row[description]}</small></h4><br>

                        <div class='btn-group' data-toggle='buttons'>

                          <label class='btn btn-primary btn-lg'>

                            <input type='radio' name='{$row[id]}' id='option2' autocomplete='off' value='Yes'> Yes

                          </label>

                          <label class='btn btn-primary btn-lg'>

                            <input type='radio' name='{$row[id]}' id='option3' autocomplete='off' value='No'> No

                          </label>

                        </div>

                     </div>";
                     break;
                  case 'option':
                  case 'expanded_option':
                    echo "<div class='form-group col-sm-12' style='margin-top:13px'>

                         <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                        <select name='{$row[id]}' "; if($row['type']=='expanded_option'){echo "multiple";} echo" class='form-control'>";

                          foreach($choices as $choice){
                            echo "<option>{$choice}</option>";
                          }

                        echo "</select>

                    </div>";
                    break;
                  case 'response':
                    echo "<div class='form-group col-sm-12'>

                        <label for='{$row[id]}'><h4>{$row[question]}<small> {$row[description]}</small></h4></label>

                        <textarea rows='5' class='form-control' name='{$row[id]}'></textarea>

                    </div>";
                    break;
                  default:
                    echo "<h5>unknown question type</h5>";
                }
              }
            ?>
            
            <div class="form-group col-sm-12">

                  <button type="button" id="submit" data-loading-text="Submitting..." class="btn btn-default" autocomplete="off">

                  Finish >>

                </button>

             </div>

            </form>

        </div>

        

        <div id="stepThree" style="display:none">
          <cms:editable name='thanks_text' desc="Text visitor sees after taking the survey." type='richtext'>
          	<h1>Thank you!</h1>

            <p>Thank you for taking the survey. Your responses have been recorded.</p>
          </cms:editable>

            <hr>

        </div>

    </div>

</body>

</html>
<?php COUCH::invoke(); ?>