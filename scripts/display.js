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

  // inserts text into fieldExpressionWithFocus
  // selects numSelectedLetters starting at selectionStartPosition
  function insertIntoFieldExpressionWithFocus(text,selectionStartPosition,numSelectedLetters){
	  if(fieldExpressionWithFocus == null){return;}
	  let cursorPosition = getCursorPosition(fieldExpressionWithFocus);
	  fieldExpressionWithFocus.value = insertAtIndex(fieldExpressionWithFocus.value, cursorPosition, text);
	  updateFormulaAndResult();
	  setCursorPosition(fieldExpressionWithFocus, cursorPosition+selectionStartPosition);
	  fieldExpressionWithFocus.setSelectionRange(cursorPosition+selectionStartPosition,cursorPosition+selectionStartPosition+numSelectedLetters);	  
  }

  
  // insert sqrt(0) into fieldExpressionWithFocus at the cursorposition. 
  // set the cursor on the 0. 
  function buttonInsertSqrt(){
	  insertIntoFieldExpressionWithFocus("wurzel(2)",7,1);
  }
  
  function buttonInsertPow(){
	  insertIntoFieldExpressionWithFocus("^2",1,1);
  }
  
  function buttonInsertPlus(){
	  insertIntoFieldExpressionWithFocus("+2",1,1);
  }

  function buttonInsertMinus(){
	  insertIntoFieldExpressionWithFocus("-2",1,1);
  }

  function buttonInsertMult(){
	  insertIntoFieldExpressionWithFocus("*2",1,1);
  }

  function buttonInsertDiv(){
	  insertIntoFieldExpressionWithFocus(":2",1,1);
  }

  function buttonInsertLog(){
	  insertIntoFieldExpressionWithFocus("log(2;7)",4,3);
  }
  
  function buttonInsertFract(){
	  insertIntoFieldExpressionWithFocus("/2",1,1);
  }
  
  function buttonInsertNthRoot(){
	  insertIntoFieldExpressionWithFocus("ntewurzel(3;5)",10,3);
  }
  
  function buttonBracketOpen(){
	  insertIntoFieldExpressionWithFocus("(2+1)",1,3);
  }
  
  function buttonBracketClose(){
	  insertIntoFieldExpressionWithFocus(")",1,0);
  }
  
  function insertAtIndex(text, index, inserted_text)
  {
   return text.substring(0, index) + inserted_text + text.substring(index);
  }
  
  function setCursorPosition(oField, pos) {
	  // Modern browsers
	  if (oField.setSelectionRange) {
	    oField.focus();
	    oField.setSelectionRange(pos, pos);
	  
	  // IE8 and below
	  } else if (oField.createTextRange) {
	    let range = oField.createTextRange();
	    range.collapse(true);
	    range.moveEnd('character', pos);
	    range.moveStart('character', pos);
	    range.select();
	  }
	}

  
  function getCursorPosition (oField) {

	  // Initialize
	  let iCaretPos = 0;

	  // IE Support
	  if (document.selection) {

	    // Set focus on the element
	    oField.focus();

	    // To get cursor position, get empty selection range
	    let oSel = document.selection.createRange();

	    // Move selection start to 0 position
	    oSel.moveStart('character', -oField.value.length);

	    // The caret position is selection length
	    iCaretPos = oSel.text.length;
	  }

	  // Firefox support
	  else if (oField.selectionStart || oField.selectionStart == '0')
	    iCaretPos = oField.selectionDirection=='backward' ? oField.selectionStart : oField.selectionEnd;

	  // Return position
	  return iCaretPos;
	}
  
  