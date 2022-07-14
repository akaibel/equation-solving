<?php
$z_require_erlaubt = true;
require_once('header_ohne_session_start.php');
require_once('../include_math/equation_input.php');

if(!isset($_GET['token'])){
    echo("<h1><font color='red'>Token fehlt!</font></h1>");
}
else{
    $token_task = z_db_text_erzeugen($_GET['token']);
    //echo $token_task;
    
    $select_statement = 
        " SELECT ta.id AS task_id, ta.text AS task_text, ta.timestamp AS task_timestamp, COUNT(eq.id) AS number_of_equations, te.nickname as teacher_nickname ".
        " FROM (equ_teacher te RIGHT JOIN equ_task ta ".
        " ON te.id = ta.equ_teacher_id), equ_equation eq".
        " WHERE ta.id = eq.equ_task_id ".
        " AND ta.token = '$token_task' ".
        " GROUP BY ta.id ";
    //z_konsole($select_statement);
    $ergebnis = z_db_sql_statement_ausfuehren($select_statement);
    if(z_db_zeilen_zahl($ergebnis) == 0){
        z_fehlermeldung("Ung&uuml;ltiger Code!");
        z_html_button_link("zurueck", "task_new_teacher.php");
        
    }
    else{
        $zeile = z_db_zeile_auslesen($ergebnis);
        $task_id = $zeile['task_id'];
        $task_text = $zeile['task_text'];
        $task_timestamp = $zeile['task_timestamp'];
        $task_date = z_datum_aus_db($task_timestamp);
        $teacher_nickname = $zeile['teacher_nickname'];
        $number_of_equations = $zeile['number_of_equations'];
        
        echo "<h1> Aufgabe von $teacher_nickname ($task_date):</h1>";    
        
        $token_task_work = z_token_finden('equ_task_work',10);
        
        
        // create solution for this equation
        $insert_statement =
        " INSERT into equ_task_work(equ_task_id,token) ".
        " VALUES('$task_id','$token_task_work')";
        // echo $insert_statement."<br/><br/>";
        z_db_sql_statement_ausfuehren($insert_statement);
        
        $select_statement =
        " SELECT MAX(id) FROM equ_task_work WHERE equ_task_id = '$task_id'";
        // echo $select_statement."<br/><br/>";
        $result = z_db_sql_statement_ausfuehren($select_statement);
        $task_work_id = z_db_zeile_auslesen($result)[0];
        //echo "task_work_id: ".$task_work_id."<br/>";
        //echo "task_id: ".$task_id."<br/>";
        //echo "token_task_work: ".$token_task_work."<br/>";
         
        // TODO delete task_work entries older than one hour without solution
         
    ?>
    
    	<script>

            function insertLetter(letter){
                  let nicknameInput = document.getElementById('nickname');
              	  let text = nicknameInput.value + letter;
              	  nicknameInput.value = text.charAt(0).toUpperCase() + text.slice(1);              	  
            }
            
            function deleteLastLetter(){
              let nicknameInput = document.getElementById('nickname');
        	  let text = nicknameInput.value;
        	  text = text.substring(0, text.length-1);
          	  nicknameInput.value = text.charAt(0).toUpperCase() + text.slice(1);              	          	  
            }

    </script>
    
     	<form action="solve.php" id="solve_form" method="POST">
    			<p><b><?php echo "$task_text"; ?></b></p>
        		<p>Anzahl der Gleichungen: <b><?php echo $number_of_equations; ?></b></p>
        		<p>
        			<b>Gib dir einen Nickname:</b><br/>
        			<input type="text" name="nickname" id="nickname" value="" readonly>
        		</p>
        		<p>
<?php 
    $counter=0;
    for($letter='A'; $letter!='AA';$letter++){
        $letterToLowerCase = strtolower($letter);
        echo "<input type='image' src='../images/let$letter.png' onclick='insertLetter("."\"$letterToLowerCase\""."); return false'/>\n";
        $counter++;
        if($counter%9 == 0){
            echo "<br/>";
        }    
    }
    echo "<input type='image' src='../images/del.png' onclick='deleteLastLetter(); return false'/>\n";
    
?>
        		</p>
        		<p>
        			<button class="button">Zur 1. Gleichung (von <?php echo $number_of_equations;?>)</button>    		
        		</p>
<!--         	<p>
    	    		<b>Speichere den folgenden Link, um deine Bearbeitung wieder aufrufen zu k&ouml;nnen:</b><br/>
        			<input type="text" readonly id="link_text" size=80 value="<?php echo($z_config_root_verzeichnis."student/task_display.php?token=".$token_task_work);?>">
        		</p>
 -->
        		<input type="hidden" name="token_task_work" value = "<?php echo $token_task_work; ?>">	
        		<input type="hidden" name="token_task" value = "<?php echo $token_task; ?>">	
        		<input type="hidden" name="equation_number" value = "1">	
    	</form>
        <script>
        	$(document).ready(function() {
                $("#link_text").focus(function() { 
                    $(this).select(); 
                }); 
        	});  
        	document.getElementById("solve_form").addEventListener("submit", function(event){
                let nickname =  document.getElementById("nickname").value;
                if(nickname.length < 3){
                    alert("Der Nickname muss mindestens 3 Buchstaben haben!");
    	      		event.preventDefault()
                }
    		});        	
    	</script>

<?php     
        } // end else
    } // end else
require_once('footer.php');
?>
