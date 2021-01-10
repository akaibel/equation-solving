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


    /*
    	finds solutions for an equation of variable in the interval [start,end]
        returns an array with the solutions
        if there is an error, then null is returned.
    */
  	function findSolutionsInInterval(equation,variable,start,end){
	  		let allSolutions = [];
	  		let sidesOfEquation = equation.split("=");
	  		let equationEqualsZero = sidesOfEquation[0]+"-("+sidesOfEquation[1]+")";
	  		let node = math.compile(equationEqualsZero);
	  		let result1 = null;
			let result2;
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
	  		let i, iMinusStep;
	  		for(i=start; i<=end;i+=step){
		  		try{
		  			scope[variable] = i;
		  			result2 = node.evaluate(scope);
		  			if(Math.abs(result2) > 1000*step || result2 === Infinity || result2 == -Infinity) {
						result1 = null;
						continue;
					} 
		  			if(result1 !== null && result1*result2<=0){
		  				iMinusStep = i-step;
		  				//alert(i+"; "+iMinusStep);
		  				let approxSolution = sliceInterval(node,variable,iMinusStep,i);
		  				allSolutions.push(approxSolution);
		  			}
	  				result1 = result2;
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
  	
  	function sliceInterval(node, variable, left, right){
  		let middle;
  		let resultLeft, resultMiddle;
  		let scope = {};
  		let i;
  		for(i=0; i<30; i++){
  			middle = (left+right)/2;
  			scope[variable] = left;
  			resultLeft = node.evaluate(scope);
  			scope[variable]=middle;
  			resultMiddle = node.evaluate(scope);
  			if(resultLeft * resultMiddle <=0){
  				right = middle;
  			}
  			else{
  				left = middle;
  			}
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
  
  	
