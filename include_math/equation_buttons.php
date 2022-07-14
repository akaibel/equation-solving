<?php 
    function equation_buttons($variable){
        $variableToUpperCase = strtoupper($variable);
        
?>
		<div style="min-width:560px">
			<input type="image" src="../images/num0.png" onclick="buttonNum(0);return false"/>
			<input type="image" src="../images/num1.png" onclick="buttonNum(1);return false"/>
			<input type="image" src="../images/num2.png" onclick="buttonNum(2);return false"/>
			<input type="image" src="../images/num3.png" onclick="buttonNum(3);return false"/>
			<input type="image" src="../images/num4.png" onclick="buttonNum(4);return false"/>
			<input type="image" src="../images/num5.png" onclick="buttonNum(5);return false"/>
			<input type="image" src="../images/num6.png" onclick="buttonNum(6);return false"/>
			<input type="image" src="../images/num7.png" onclick="buttonNum(7);return false"/>
			<input type="image" src="../images/num8.png" onclick="buttonNum(8);return false"/>
			<input type="image" src="../images/num9.png" onclick="buttonNum(9);return false"/>
			<input type="image" src="../images/numcomma.png" onclick="buttonNumComma();return false"/>
			<br/>
			<span id="buttonVariableDiv"  onclick="buttonNumVariable();return false">
				<input type="image" src="../images/let<?php echo $variableToUpperCase;?>.png"/>
			</span>
			&nbsp;&nbsp;
			<input type="image" src="../images/plus.png" onclick="buttonInsertPlus();return false"/>
			<input type="image" src="../images/minus.png" onclick="buttonInsertMinus();return false"/>
			<input type="image" src="../images/mult.png" onclick="buttonInsertMult();return false"/>
			<input type="image" src="../images/div.png" onclick="buttonInsertDiv();return false"/>
			&nbsp;&nbsp;
			<input type="image" src="../images/bracketOpen.png" onclick="buttonBracketOpen();return false"/>
			<input type="image" src="../images/bracketClose.png" onclick="buttonBracketClose();return false"/>
			&nbsp;&nbsp;
			<input type="image" src="../images/arrowback.png" onclick="buttonBack();return false"/>
			<input type="image" src="../images/del.png" onclick="buttonDelete();return false"/>
			<input type="image" src="../images/arrowforward.png" onclick="buttonForward();return false"/>
			<input type="image" src="../images/semicolon.png" onclick="buttonSemicolon();return false"/>
			<br/>
			<input type="image" src="../images/fract.png" onclick="buttonInsertFract();return false"/>
			<input type="image" src="../images/pow.png" onclick="buttonInsertPow();return false"/>
			<input type="image" src="../images/sqrt.png" onclick="buttonInsertSqrt();return false"/>
			<input type="image" src="../images/root.png" onclick="buttonInsertNthRoot();return false"/>
			<input type="image" src="../images/logxy.png" onclick="buttonInsertLog();return false"/>
			&nbsp;&nbsp;
			<input type="image" src="../images/sin.png" onclick="buttonInsertSin();return false"/>
			<input type="image" src="../images/cos.png" onclick="buttonInsertCos();return false"/>
			<input type="image" src="../images/tan.png" onclick="buttonInsertTan();return false"/>
			&nbsp;&nbsp;
			<input type="image" src="../images/numPI.png" onclick="buttonNumPI();return false"/>
			<input type="image" src="../images/numE.png" onclick="buttonNumE();return false"/>
		</div>	
<?php 
    }
?>
