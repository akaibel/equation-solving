<?php

    $equation = $_POST['equation'];
    $variable = $_POST['variable'];
    // the interval, where the solution is searched for
    $intervalLeftSide = $_POST['leftSide'];
    $intervalRightSide = $_POST['rightSide'];
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Gleichung l&ouml;sen</title>
  
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
      L&ouml;se die Gleichung Schritt f&uuml;r Schritt!
    </h1>
    <p><i>Die Gleichung hat <b><span  id="numberOfSolutions">2</span></b> L&ouml;sung(en).</i></p>
</div>
<table id="mainTable">
  <tr id="rowSeparatingTaskAndCalculation">
  	<td colspan=5></td>
  </tr>
  <tr id="rowSeparatingCalculationAndNextStep">
  	<td colspan=5></td>
  </tr>
  <tr id="nextStepTableRow">
    <th>n&auml;chster<br/>Schritt:</th>
    <td><div id="fieldFormula1"></div></td>
    <td class="equalitySign">=</td>
    <td><div id="fieldFormula2"></div></td>
    <td id="checkIsEqualResultSymbol"></td>
  </tr>
  <tr>
    <th>Eingabe:</th>
    <td><input type="text" id="fieldExpression1"/></td>
    <td class="equalitySign">=</td>
    <td><input type="text" id="fieldExpression2"/></td>
    <td></td>
  </tr>
  <tr>
    <th>Buttons:</th>
    <td colspan=3>
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
  	<td align="center" colspan = 3>
		<button id="buttonCheckIsEqual" onclick="checkIsEqual()">pr&uuml;fen</button>
  		<div id="checkIsEqualResult"></div>
  	</td>  	
    <td></td>
  </tr>
</table>

<br/><br/>
<form action="index.html">
	<div style="text-align: center;">
		<button type="submit">neue Gleichung eingeben</button>
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
  
  const equation = "<?php echo($equation);?>";

  var stepOfCalculation = 0;
  
  // results (array of float) of the left side of the current equation
  var results1;
  // results (array of float) of the right side of the current equation 
  var results2;
  
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
	        fieldExpression2.focus();
	    }
	});

  fieldExpression1.focus();
  
  const fieldExpression2 = document.getElementById('fieldExpression2');  
  fieldExpression2.oninput = function () {
	    fieldExpression2.value = fieldExpression2.value.toLowerCase();
		updateFormulaAndResult();  
  }
  fieldExpression2.onfocus = function(){
		fieldExpressionWithFocus = fieldExpression2;  
  }
  fieldExpression2.addEventListener("keyup", function(event) {
	    if (event.key === "Enter") {
	        checkIsEqual();
	    }
	});

  const rowSeparatingTaskAndCalculation = document.getElementById('rowSeparatingTaskAndCalculation');
  const rowSeparatingCalculationAndNextStep = document.getElementById('rowSeparatingCalculationAndNextStep');
  const nextStepTableRow = document.getElementById('nextStepTableRow');
  
  
  const fieldFormula1 = document.getElementById('fieldFormula1');
  const fieldFormula2 = document.getElementById('fieldFormula2');
  
  const fieldCheckIsEqualResult = document.getElementById('checkIsEqualResult');
  const tdCheckIsEqualResultSymbol = document.getElementById('checkIsEqualResultSymbol');
    
  
  // display task:
  // task is displayed as one (or more) TR before rowSeperatingTaskAndCalculation
  displayEquation(equation);
  
  const solutions = findSolutions();

  const spanNumberOfSolutions = document.getElementById('numberOfSolutions');
  if(solutions === null){
	  	spanNumberOfSolutions.innerHTML = "keine!!! ";	  
  }
  else{
  	spanNumberOfSolutions.innerHTML = ""+solutions.length;
  }
  const variablesValues = [];
  var i;
  if(solutions !== null){
      for(i=0; i<solutions.length; i++){
    	  variablesValues.push({<?php echo(strtolower($variable)); ?>: solutions[i]});
      }
  }
  
  function displayEquation(currentEquation){
	  let newTableRow = document.createElement("TR");
	  rowSeparatingTaskAndCalculation.parentNode.insertBefore(newTableRow, rowSeparatingTaskAndCalculation);
	  
	  let newTableHeader = document.createElement("TH");
	  newTableHeader.classList.add('task');
	  newTableHeader.innerHTML = "Aufgabe:";
	  
	  let equalitySignTD = document.createElement("TD");
	  equalitySignTD.classList.add('task');
	  equalitySignTD.innerHTML = "=";
	  
	  let leftSideTD = getFormulaAsTD(getSideOfEquation(currentEquation,1));
	  leftSideTD.classList.add('task');
	  let rightSideTD = getFormulaAsTD(getSideOfEquation(currentEquation,2));
	  rightSideTD.classList.add('task');
	  
	  let emptyTD = document.createElement("TD");
	  emptyTD.classList.add('task');
	  
	  newTableRow.appendChild(newTableHeader);
	  newTableRow.appendChild(leftSideTD);
	  newTableRow.appendChild(equalitySignTD);
	  newTableRow.appendChild(rightSideTD);
	  newTableRow.appendChild(emptyTD);	  
  }
    
  function appendCurrentExpressionToSolution(){
	  let newTableRow = document.createElement("TR");
	  rowSeparatingCalculationAndNextStep.parentNode.insertBefore(newTableRow, rowSeparatingCalculationAndNextStep);
	  let newTableHeader = document.createElement("TH");
	  stepOfCalculation += 1;
	  newTableHeader.innerHTML = ""+stepOfCalculation+". Schritt:";
	  let equalitySignTD = document.createElement("TD");
	  equalitySignTD.innerHTML = "=";
	  let smileyTD = document.createElement("TD");
	  smileyTD.innerHTML = smileyCorrect;
	  
	  newTableRow.appendChild(newTableHeader);
	  newTableRow.appendChild(getFormulaAsTD(convertToMathExpression(fieldExpression1.value)));
	  newTableRow.appendChild(equalitySignTD);
	  newTableRow.appendChild(getFormulaAsTD(convertToMathExpression(fieldExpression2.value)));
	  newTableRow.appendChild(smileyTD);
	  
	  fieldExpression1.value = "";
	  fieldExpression2.value = "";
	  
	  fieldFormula1.innerHTML = "";
	  fieldFormula2.innerHTML = "";
	  
	  results1 = [];
	  results2 = [];
	  
	  fieldExpression1.focus();
  }
	
  function getSideOfEquation(equation,side){
	  try{
		  return equation.split("=")[side-1];		  		  
	  }
	  catch(err){
		  alert("error in getSideOfEquation("+equation+","+side+")");
		  return "";
	  }
	  
  }
  
  // method checks, whether the results of both sides are equal
  // this check is only APPROXIMATE!
  // if the difference is smaller than 0.001, then the result is considered equal
  function checkIsEqual(){
	  let isEqual = false;
	  var i;
	  for(i=0; i<results1.length; i++){
		  if(Math.abs(results1[i]-results2[i]) < 0.001){
			  isEqual = true;
		  }
	  }
	  if(isEqual){
		  fieldCheckIsEqualResult.innerHTML = "richtig! "+smileyCorrect;
		  appendCurrentExpressionToSolution();
	  }
	  else{
		  fieldCheckIsEqualResult.innerHTML = "falsch! "+smileyFalse;	
		  tdCheckIsEqualResultSymbol.innerHTML = smileyFalse;
	  }
	  //focus on Left side
	  fieldExpression2.blur();
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
	    else if(fieldExpressionWithFocus == fieldExpression2){
		    fieldFormula = fieldFormula2;
		    fieldExpression = fieldExpression2;
		    number = 2;
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
	      // parse the expression
	      // replace all ':' by '/'
	      node = math.parse(mathExpression);

	      // evaluate the results of the expression
	      var i;
	      for(i=0; i<variablesValues.length; i++){
		      results.push(math.format(node.compile().evaluate(variablesValues[i])));
	      }
    	    if(number == 1){
    		    results1 = results;	    	
    	    }
    	    else if(number == 2){
    		    results2 = results;	    	
    	    }
	    }
	    catch (err) {
	      results = [];
	    }
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
  
  	function findSolutions(){
  		let variable = "<?php echo $variable; ?>";
  		// area where a solution is searched
  		
  		let start = parseFloat("<?php echo $intervalLeftSide; ?>");
  		let end = parseFloat("<?php echo $intervalRightSide; ?>");

    	let solutions = findSolutionsInInterval(equation, variable, start, end);
    	if(solutions == null){
    		// there was an error 
    		// the error was displayed before
    		return null;
    	}
    	if(solutions.length == 0){
    		alert("Keine Lösung im Bereich "+start+" bis "+end);
    		return solutions;
    	}
//    	if(solutions.length == 1){
//   		alert("Eine Lösung im Bereich "+start+" bis "+end);
//    		return solutions;
//    	}
//    	alert(""+solutions.length+" Lösungen");
      	return solutions;
  	}

</script>


</body>
</html>
