
<!--
Copyright (C) 2021  Andreas Kaibel

This program is free software; you can redistribute it and/or modify it 
under the terms of the GNU General Public License as published by the Free Software Foundation; 
either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

For a copy of the GNU General Public License see <http://www.gnu.org/licenses/>. 

Get source-code at GitHub: https://github.com/akaibel/equation-solving
-->

<?php

    $term = $_POST['term'];
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Rechenausdruck vereinfachen</title>
  
  <link rel="stylesheet" href="style/main.css">

  <script src="https://unpkg.com/mathjs@8.1.0/lib/browser/math.js"></script>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
  <script src="scripts/solve.js"></script> 
  <script src="scripts/display.js"></script> 
</head>

<body>
<div  style="text-align: center;">
    <h1>
      Vereinfache den Rechenausdruck Schritt f&uuml;r Schritt!
    </h1>
</div>
<table id="mainTable">
  <tr id="rowSeparatingTaskAndCalculation">
  	<td colspan=3></td>
  </tr>
  <tr id="rowSeparatingCalculationAndNextStep">
  	<td colspan=3></td>
  </tr>
  <tr id="nextStepTableRow">
    <th>n&auml;chster<br/>Schritt:</th>
    <td><div id="fieldFormula1"></div></td>
    <td id="checkIsEqualResultSymbol"></td>
  </tr>
  <tr>
    <th>Eingabe:</th>
    <td><input type="text" id="fieldExpression1"/></td>
    <td></td>
  </tr>
  <tr>
    <th>Buttons:</th>
    <td>
		<input type="image" src="images/plus.png" onclick="buttonInsertPlus()"/>
		<input type="image" src="images/minus.png" onclick="buttonInsertMinus()"/>
		<input type="image" src="images/mult.png" onclick="buttonInsertMult()"/>
		<input type="image" src="images/div.png" onclick="buttonInsertDiv()"/>
		<input type="image" src="images/fract.png" onclick="buttonInsertFract()"/><br/>
		<input type="image" src="images/pow.png" onclick="buttonInsertPow()"/>
		<input type="image" src="images/sqrt.png" onclick="buttonInsertSqrt()"/>
		<input type="image" src="images/root.png" onclick="buttonInsertNthRoot()"/>
		<input type="image" src="images/logxy.png" onclick="buttonInsertLog()"/>
		<input type="image" src="images/bracketOpen.png" onclick="buttonBracketOpen()"/>
		<input type="image" src="images/bracketClose.png" onclick="buttonBracketClose()"/>
    </td>
    <td></td>
  </tr>
  <tr>
  	<th>richtig?</th>
  	<td align="center" >
		<button id="buttonCheckIsEqual" onclick="checkIsEqual()">pr&uuml;fen</button>
  		<div id="checkIsEqualResult"></div>
  	</td>  	
    <td></td>
  </tr>
</table>

<br/><br/>
<form action="term.html">
	<div style="text-align: center;">
		<button type="submit">neuen Rechenausdruck eingeben</button>
	</div>
</form>

<script>
  // technical variables for MathJax
  // possible values: 'keep', 'auto', 'all'
  const parenthesis = 'keep';	
  // possible values: 'hide', 'show'
  const implicit = 'hide';

  const smileyCorrect = "&#x1F60A;";
  const smileyFalse = "&#x1F615;";
  
  const term = "<?php echo($term);?>";

  var stepOfCalculation = 0;
  
  
  // the fieldExpression, that currently has the focus
  var fieldExpressionWithFocus = null;

  const mj = function (tex) {
	    return MathJax.tex2svg(tex, {em: 16, ex: 6, display: false});
  }

  const fieldExpression1 = document.getElementById('fieldExpression1');
  fieldExpression1.oninput = function () {
	    fieldExpression1.value = fieldExpression1.value.toLowerCase();
		updateFormulaAndResult();  
  }
  fieldExpression1.onfocus = function(){
		fieldExpressionWithFocus = fieldExpression1;  
  }
  fieldExpression1.addEventListener("keyup", function(event) {
	    if (event.key === "Enter") {
	        checkIsEqual();
	    }
	});

  fieldExpression1.focus();
  
  const rowSeparatingTaskAndCalculation = document.getElementById('rowSeparatingTaskAndCalculation');
  const rowSeparatingCalculationAndNextStep = document.getElementById('rowSeparatingCalculationAndNextStep');
  const nextStepTableRow = document.getElementById('nextStepTableRow');
  
  
  const fieldFormula1 = document.getElementById('fieldFormula1');
  
  const fieldCheckIsEqualResult = document.getElementById('checkIsEqualResult');
  const tdCheckIsEqualResultSymbol = document.getElementById('checkIsEqualResultSymbol');
    
  
  // display task:
  // task is displayed as one (or more) TR before rowSeperatingTaskAndCalculation
  displayTerm(term);

  
  function displayTerm(currentTerm){
	  let newTableRow = document.createElement("TR");
	  rowSeparatingTaskAndCalculation.parentNode.insertBefore(newTableRow, rowSeparatingTaskAndCalculation);
	  
	  let newTableHeader = document.createElement("TH");
	  newTableHeader.classList.add('task');
	  newTableHeader.innerHTML = "Aufgabe:";
	  
	  let leftSideTD = getFormulaAsTD(currentTerm);
	  leftSideTD.classList.add('task');
	  
	  let emptyTD = document.createElement("TD");
	  emptyTD.classList.add('task');
	  
	  newTableRow.appendChild(newTableHeader);
	  newTableRow.appendChild(leftSideTD);
	  newTableRow.appendChild(emptyTD);	  
  }
    
  function appendCurrentExpressionToSolution(){
	  let newTableRow = document.createElement("TR");
	  rowSeparatingCalculationAndNextStep.parentNode.insertBefore(newTableRow, rowSeparatingCalculationAndNextStep);
	  let newTableHeader = document.createElement("TH");
	  stepOfCalculation += 1;
	  newTableHeader.innerHTML = ""+stepOfCalculation+". Schritt:";
	  let smileyTD = document.createElement("TD");
	  smileyTD.innerHTML = smileyCorrect;
	  
	  newTableRow.appendChild(newTableHeader);
	  newTableRow.appendChild(getFormulaAsTD(convertToMathExpression(fieldExpression1.value)));
	  newTableRow.appendChild(smileyTD);
	  
	  fieldExpression1.value = "";
	  
	  fieldFormula1.innerHTML = "";
	  
	  fieldExpression1.focus();
  }


  function getTermFromFieldExpressions(){
  	return convertToMathExpression(fieldExpression1.value);
  }
  
	
  // method checks, whether the result of the term is correct
  // this check is only APPROXIMATE!
  // if the difference is smaller than 0.001, then the result is considered equal
  function checkIsEqual(){
	let term = getTermFromFieldExpressions();
	let resultTerm = math.evaluate(term);
	let resultReal = math.evaluate("<?php echo $term; ?>");
	let isEqual = (Math.abs(resultTerm - resultReal) <0.001);
      if(isEqual){
    	  fieldCheckIsEqualResult.innerHTML = "richtig! "+smileyCorrect;
    	  appendCurrentExpressionToSolution();
      }
      else{
    	  fieldCheckIsEqualResult.innerHTML = "falsch! "+smileyFalse;	
    	  tdCheckIsEqualResultSymbol.innerHTML = smileyFalse;
      }
      //focus on Left side
	  fieldExpression1.focus();
  }


  
  // returns the formula-display for an expression as a <td>-Tag
  function getFormulaAsTD(expression){
	    let node = null;

	    try {
	      // parse the expression
	      node = math.parse(expression)
	    }
	    catch (err) {
	      alert("getFormulaAsTD("+expression+"): Expression cannot be converted.");
	      return "Error";
	    }
	    
	    let formulaNode = document.createElement("TD");
	    try {
	      // export the expression to LaTeX
	      const latex = node ? node.toTex({parenthesis: parenthesis, implicit: implicit}) : ''
	      //console.log('LaTeX expression:', latex)

	      // display and re-render the expression
	      MathJax.typesetClear();
	      formulaNode.appendChild(mj(latex));
	    }
	    catch (err) {
		      alert("getFormulaAsTD("+expression+"): Expression cannot be converted.");
		      formulaNode.innerHTML = "Error";	    	
	    }
	    return formulaNode;
	  
  }
  
  function updateFormulaAndResult(){
	    let results = [];
	    let fieldFormula;
	    let fieldExpression;
	    let number = -1;
	    
	    if(fieldExpressionWithFocus == fieldExpression1){
		    fieldFormula = fieldFormula1;
		    fieldExpression = fieldExpression1;	 
		    number = 1;
	    }
	    else{
	    	alert("no expression field has focus");
	    	return;
	    }

	    var mathExpression = convertToMathExpression(fieldExpression.value);

	    fieldCheckIsEqualResult.innerHTML = "ungepr&uuml;ft";
	    tdCheckIsEqualResultSymbol.innerHTML = "";
	    
	    let node = null;

	    try {
	      // export the expression to LaTeX
	      node = math.parse(mathExpression);
	      const latex = node ? node.toTex({parenthesis: parenthesis, implicit: implicit}) : ''
	      //console.log('LaTeX expression:', latex)

	      // display and re-render the expression
	      MathJax.typesetClear();
	      fieldFormula.innerHTML = '';
	      fieldFormula.appendChild(mj(latex));
	    }
	    catch (err) {}
	  
	}
  
</script>


</body>
</html>
