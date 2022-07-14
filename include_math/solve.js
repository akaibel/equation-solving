/*
Copyright (C) 2021  Andreas Kaibel

This program is free software; you can redistribute it and/or modify it 
under the terms of the GNU General Public License as published by the Free Software Foundation; 
either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

For a copy of the GNU General Public License see <http://www.gnu.org/licenses/>. 

Get source-code at GitHub: https://github.com/akaibel/equation-solving
*/

	// the number of operations is counted; if operations is too great
	// then the script is stopped to avoid a permanent lag of the page
	// e.g. for functions as sin(x)/x
	var operations;
	var maxOperations = 1000000;

    /*
    	finds solutions for an equation of variable in the interval [start,end]
        returns an array with the solutions
        if there is an error, then null is returned.
    */
  	function findSolutionsInInterval(equation,variable,start,end){
	  		operations = 0;
			let allSolutions = [];
	  		let sidesOfEquation = equation.split("=");
	  		let equationEqualsZero = sidesOfEquation[0]+"-("+sidesOfEquation[1]+")";
	  		let node;
	  		try{
	  			node = math.compile(equationEqualsZero);
	  		}catch(err){
	  		    if(err.name.startsWith("SyntaxError")){
	  		    	alert("fehlerhafte Gleichung");
	  		    }
	  			return null;
	  		}
	  		let result = null;
			let resultNext = null;
	  		let scope = {};
	  		if(isNaN(start) || isNaN(end)){
	  			alert("Der Bereich muss durch Zahlen angegeben werden!");
	  			return null;
	  		}
	  		if(start == end){
	  			alert("Für den Bereich können nicht zwei gleiche Zahlen angegeben werden.");
	  			return null;	  			
	  		}
	  		if(start > end){
	  			let startNeu = end;
	  			end = start;
	  			start = startNeu;
	  		}
	  		// step for checking the area
	  		// 4000 steps from start to end
	  		let step = (end-start)/4000;
	  		let x;
	  		for(x=start; x<end;x+=step){
				operations += 1;
				if(operations >= maxOperations){
					alert ("Bereich zu gross gewaehlt!\nEs koennen nicht alle Loesungen gefunden werden.");
					return allSolutions;
				}
		  		try{
		  			scope[variable] = x;
		  			result = node.evaluate(scope);
		  			scope[variable] = x+step;
					resultNext = node.evaluate(scope);
		  			if(result === Infinity || result === -Infinity|| resultNext === Infinity || resultNext === -Infinity) {
						continue;
					} 
		  			if(result*resultNext<=0){
		  				//alert(x+"; "+xMinusStep);
		  				let approxSolution = sliceInterval(node,variable,x,x+step);
		  				if(approxSolution != null){
							allSolutions.push(approxSolution);
						}
		  			}
					else{
						let resultAbs = Math.abs(result);
						let resultNextAbs = Math.abs(resultNext);
						if(resultNextAbs<step*20 && resultNextAbs<resultAbs){
							// jetzt ist man schon nah dran und es geht noch bergab...
							let rueckgabe = approxSolution(node,variable,x-step,step,end);
							if(rueckgabe[0] != null){
								allSolutions.push(rueckgabe[0]);								
							}
							x = rueckgabe[1];
						}
					}
		 		}catch(err){
					if(err.message === "Root must be odd when a is negative."){
						continue;
					}
					alert(err.message);
		  			//alert("Unbekannte Variable!");
		  			return null;
		  		}
	  		}
	  		return allSolutions;
   	}

	function approxSolution(node,variable,xStart,step,end){
		// diese Methode wird aufgerufen, wenn xStart schon "nah" an einer Nullstelle ist.
		// man geht dann so lange weiter, bis die Distanz wieder groesser wird.
		// der Bereich der drei Punkte (vor dem Minimum, minimum, nach dem Minimum) wird mit 1/10 der Distanz 
		// weiter untersucht.
		// am Ende wird der Mittlere Punkt zurueckgegeben und der rechteste Punkt, den man besichtigt hat.
		// denn erst da muss man weitersuchen.
		let stepCurrent = step;
		let result,resultNext;
  		let scope = {};
		let xMax = xStart +stepCurrent;
		let x;
		for(iteration=0; iteration<10;iteration++){
  			x = xStart;
			do{
				scope[variable] = x;
  				result = node.evaluate(scope);
  				scope[variable] = x+stepCurrent;
  				resultNext = node.evaluate(scope);
				x += stepCurrent; 						
				operations += 1;
				if(operations >= maxOperations){
					alert ("Bereich zu gross gewaehlt!\nEs koennen nicht alle Loesungen gefunden werden.");
					return [null,xMax];
				}
			}
			while(result * resultNext > 0 && Math.abs(resultNext) <= Math.abs(result) && x<end);
			if(iteration == 0){
				xMax = x;
			}
			if(resultNext == 0){
				return [x+stepCurrent, xMax];
			}
			if(result * resultNext < 0){
				let nullstelle = sliceInterval(node,variable,x-stepCurrent,x);
				return [nullstelle,xMax];
			}
			if(x >= end){
				return [null,xMax];
			}
			// jetzt ist man bei i am naechsten an der Nulllinie!
			// d.h. man startet bei i-1 und geht in kleineren Schritten vor.
			xStart = x-2*stepCurrent;
			stepCurrent = stepCurrent/10;
		}
		return [x,xMax-step];
	}
  	
  	function sliceInterval(node, variable, left, right){
  		let middle;
  		let resultLeft, resultMiddle;
  		let scope = {};
  		let i;
  		for(i=0; i<30; i++){

			operations += 1;
			if(operations >= maxOperations){
				alert ("Bereich zu gross gewaehlt!\nEs koennen nicht alle Loesungen gefunden werden.");
				return null;
			}

  			middle = (left+right)/2;
  			scope[variable] = left;
  			resultLeft = node.evaluate(scope);
  			scope[variable]=middle;
  			resultMiddle = node.evaluate(scope);
			if(resultMiddle  === Infinity) {
				return null;
			}

  			if(resultLeft * resultMiddle <=0){
  				right = middle;
  			}
  			else{
  				left = middle;
  			}
  		}
		if(Math.abs(resultMiddle) > 10 )
		{
			// da liegt eine Gleichung wie 1/x = 0 vor!
			return null;
		}
  		return middle;
  	}

	var keysWithParameterSwitch = ["ntewurzel(","log("];

	// switches the order of the two parameters for the functions mentioned above.
	// parameters are separated by ;	
	function switchParameters(text){
		for(key of keysWithParameterSwitch){
			var result = "";
			var lastIndex = 0;
			while(true){
				var index = text.indexOf(key,lastIndex);
				if(index === -1){
					if(result.length > 0){
						text = result;						
					}
					break;
				}
				// add key to result
				result+=text.substring(lastIndex,index);
				result+=key;
				index+=key.length;
				var numberOfOpenBrackets = 1;
				var params = ["",""];
				var numberOfParam = 0;
				while(numberOfOpenBrackets > 0 && index < text.length){
					var c = text.charAt(index);
					if(c===')' && numberOfOpenBrackets === 1){
							break;
					}
					if(c === ";" && numberOfOpenBrackets === 1){
						numberOfParam++;
					}
					else{
						params[numberOfParam] += c;
						if(c==='('){
							numberOfOpenBrackets += 1;
						}
						else if(c===')'){
							numberOfOpenBrackets-= 1;
						}
					}
					index++;
				}
				// put everything together
				if(numberOfParam > 0){
					result += params[1];
					result += ";";				
				}
				result += params[0];
				result += text.substring(index,text.length);
				lastIndex = index;
				text = result;
			}  // end while(true)
		}  // end for 
		return text;
	}


  // converts the Expression from the input
  // to a math-readable expression
  function convertToMathExpression(text){
	  var result = text;
	  result = result.replace(/:/g, "./");
	  result = result.replace(/ /g, "");
	  result = switchParameters(result);
	  result = result.toLowerCase();
	  result = result.replace(/ntewurzel/g, "nthRoot");
	  result = result.replace(/wurzel/g, "sqrt");
	  result = result.replace(/,/g,".");
	  result = result.replace(/;/g,",");
	  return result;
  }
