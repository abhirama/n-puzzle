<?php
	require_once "n-puzzle_defines.php";
?>
<!DOCTYPE html PUBLIC "-//W3C/DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

		<style type="text/css">
			table { 
				margin-left: auto;
				margin-right: auto;
				text-align: center;
			}

			table tr td {
				border: 0;
				width: 50px;		
				height: 50px;
			}

			.cell {
				background-color: #CFC996;
				cursor: pointer;
			}

			.actionLinkContainer {
				position: relative;
			}

			.fRight {
				position: absolute;
				right: 0;
			}

			.fLeft {
				position: absolute;
				left: 0;
			}

			/*http://forum.developers.facebook.net/viewtopic.php?pid=15444*/
			.fbButton {
        background-color: #3b5998;
        border-color: #d8dfea rgb(14, 31, 91) rgb(14, 31, 91) rgb(216, 223, 234);
        border-style: solid;
        border-width: 1px;
        color: #fff;
        font-family: "lucida grande", tahoma, verdana, arial, sans-serif;
        font-size: 11px;
        margin: 0 2px;
        padding: 2px 18px;
				cursor: pointer;
			}
			
			.otherStuff {
				text-align: center;
			}

			.moveCountContainer {
				background-color: #F9C396;
				padding: 5px;
				margin: 2px;
				font-family: "Trebuchet MS", tahoma, verdana, arial, sans-serif;
			}

			.introduction {
				background-color: #F6F9F9;
				padding: 5px;
				margin: 2px;
				font-weight: bold;
			}

			.messageContainer {
				height: 30px;
			}

			.message {
				padding: 5px;
				margin-top: 10px;
				font-weight: bold;
				text-align: center;
			}
		</style>

		<script type="text/JavaScript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script> 
		<script type="text/javascript">
			$(document).ready(function(){
				//global variable declarations - start
				var _boardRows = 3;
				var _boardColumns = 3;

				//variable used to figure whether a function has been called for the first time
				var _firstPass = true;

				//global variable to hold the current empty cell row and column
				var _emptyCellRow = _boardRows - 1;
				var _emptyCellColumn = _boardColumns - 1;

				//to hold the no of moves the user takes to complete the puzzle
				var _moveCounter = 0;
				//global variable declarations - end

				//function definitions - start
				function createTable(rows, columns) {
					//using dom appending to create the table as it is more readable than string munging. 
					//Not the most efficient way but for a small app like this it does not matter.

					//takes care of the cases where the player resets the board
					$('#boardContainer table').remove();

					$('#boardContainer').append('<table border="10" bordercolor="#966F33" cell-padding="1" cell-spacing="1" id="board"></table>');
					for (var row = 0; row < rows; ++row) {
						$('#board').append('<tr></tr>');
						for (var column = 0; column < columns; ++column) {
							var id = getCellId(row, column);
							$('#board tr:last').append('<td id=' + id + '></td>');
							$('#' + id).data("row", row);
							$('#' + id).data("column", column);
						}
					}

					//the last values of rows and columns are the empty cell row and column values
					_emptyCellRow = row - 1;
					_emptyCellColumn = column - 1;

					//resizes the iframe so that scroll bars are not shown. 
					//http://abhirama.wordpress.com/2010/09/21/preventing-scroll-bar-in-facebook-canvas-iframe-applications/
					//we have to check this as FB is not loaded the first time this function runs
					if (!_firstPass) {
						FB.Canvas.setSize();
					} else {
						_firstPass = false;
					}

				}

				function getCellId(row, column) {
					return 'cell' + row + '' + column;
				}

				function getNumberArray(rows, columns) {
					//generate an array that holds all the number applicable for the board
					var totalCount = rows * columns;
					var numbers = [];
					for (var no = 1; no < totalCount; ++no) {
						numbers.push(no);		
					}
					return numbers;
				}

				//using Fisher Yates algorithm
				function shuffleNumberArray(numbers) {
					var totalCount = numbers.length;
					for (var index = 0; index < totalCount; ++index) {
						var shuffleIndex = Math.floor(Math.random() * (totalCount - 1));
						swap(index, shuffleIndex, numbers);
					}
				}

				function swap(firstIndex, secondIndex, arrayy) {
					var temp = arrayy[firstIndex];
					arrayy[firstIndex] = arrayy[secondIndex];
					arrayy[secondIndex] = temp;
				}

				function assignNumbersToBoard(numbers) {
					var index = 0;
					$('#board td').each(function(){
						if (index == numbers.length) {
							return;
						}

						$(this).text(numbers[index]);
						$(this).addClass('cell');
						index = index + 1;
					});
				}

				function isEmptyCell(row, column) {
					return row == _emptyCellRow && column == _emptyCellColumn;
				}

				function isValidClick(cell) {
					var row = cell.data('row');
					var column = cell.data('column');

					//blindly check the four cells bounding this cell for empty space. If found, valid click. Else invalid

					//check to left
					var leftColumn = column - 1;
					if (isEmptyCell(row, leftColumn)) {
						return true;
					}

					//check right
					var rightColumn = column + 1;
					if (isEmptyCell(row, rightColumn)) {
						return true;
					}

					//check top
					var topRow = row - 1;
					if (isEmptyCell(topRow, column)) {
						return true;
					}

					//check below 
					var belowRow = row + 1;
					if (isEmptyCell(belowRow, column)) {
						return true;
					}

					return false;
				}

				//http://stackoverflow.com/questions/698301/is-there-a-native-jquery-function-to-switch-elements
				function swapNodes(a, b) {
					swapIds(a, b);
					swapDataElements(a, b);
					var aParent= a.parentNode;
					var aSibling= a.nextSibling === b ? a : a.nextSibling;
					b.parentNode.insertBefore(a, b);
					aParent.insertBefore(b, aSibling);
				}

				function swapIds(nodeA, nodeB) {
					var aId = nodeA.id;
					var bId = nodeB.id;

					nodeA.id = bId;
					nodeB.id = aId;
				}

				function swapDataElements(nodeA, nodeB) {
					var jQueryNodeA = $(nodeA);
					var jQueryNodeB = $(nodeB);

					var aDataRow = jQueryNodeA.data('row');
					var aDataColumn = jQueryNodeA.data('column');

					var bDataRow = jQueryNodeB.data('row');
					var bDataColumn = jQueryNodeB.data('column');

					jQueryNodeA.data('row', bDataRow);
					jQueryNodeA.data('column', bDataColumn);

					jQueryNodeB.data('row', aDataRow);
					jQueryNodeB.data('column', aDataColumn);
				}

				function isPuzzleSolved() {
					var cells = (_boardRows * _boardColumns) - 1;
					var solved = true;
					$('td').each(function(index){
						if (index < cells && $(this).text() != (index + 1)) {
							solved = false;
							return false;
						}
						solved = true;
					});
					return solved;
				}

				function validateInputBoardValues(rows, columns) {
					if (rows == 'undefined' || rows == '') {
						displayMessage({
							"text": "Please provide valid value for row",
							"type": "warning"
						});
						return false;
					}

					if (columns == 'undefined' || columns == '') {
						displayMessage({
							"text": "Please provide valid value for column",
							"type": "warning"
						});
						return false;
					}

					if (rows < 2 || columns < 2) {
						displayMessage({
							"text": "Come on, even a baby can solve this. Provide a row value greater than 2",
							"type": "warning"
						});
						return false;
					}

					if (columns < 2) {
						displayMessage({
							"text": "Come on, even a baby can solve this. Provide a column value greater than 2",
							"type": "warning"
						});
						return false;
					}

					if (rows > 8) {
						displayMessage({
							"text": "Come on, be realistic. The row value is way too big",
							"type": "warning"
						});
						return false;
					}

					if (columns > 8) {
						displayMessage({
							"text": "Come on, be realistic. The column value is way too big",
							"type": "warning"
						});
						return false;
					}

					return true;
				}

				function getInversionCount(numbers) {
					var len = numbers.length;

					var inversionCount = 0;
					for (var outerIndex = 0; outerIndex < len; ++outerIndex) {
						for (var innerIndex = outerIndex + 1; innerIndex < len; ++innerIndex) {
							if (numbers[outerIndex] > numbers[innerIndex]) {
								inversionCount = inversionCount + 1;
							}
						}
					}

					return inversionCount;
				}

				//http://www.cs.bham.ac.uk/~mdr/teaching/modules04/java2/TilesSolvability.html
				function isPuzzleSolvable(numbers) {
					var inversionCount = getInversionCount(numbers);
					var inversionCountEvent = (inversionCount % 2 == 0 ? true : false);
					var gridWidthOdd = (_boardColumns % 2 == 1 ? true : false);
					var blankCellOnOddRowFromBottom = true; //because in our case the last cell is always the blank one

					if (gridWidthOdd) {
						if (inversionCountEvent) {
							return true;
						} else {
							return false;
						}
					} else {
						if (blankCellOnOddRowFromBottom) {
							return inversionCountEvent;
						} else {
							return !inversionCountEvent;
						}
					}

					return false;
				}

				function createBoard(rows, columns) {
					createTable(_boardRows, _boardColumns);

					var numbers = getNumberArray(_boardRows, _boardColumns);
					shuffleNumberArray(numbers);

					//brute force until we get a solvable puzzle
					while (!isPuzzleSolvable(numbers)) {
						numbers = getNumberArray(_boardRows, _boardColumns);
						shuffleNumberArray(numbers);
					}

					assignNumbersToBoard(numbers);
					assignClickHandler();
				}

				function displayMessage(messageObject) {
					var messageString = '<div class="message">__MESSAGE__</div>';
					var messageString = messageString.replace('__MESSAGE__', messageObject.text);
					$('.messageContainer').html(messageString);
					if (messageObject.type == 'warning') {
						$('.message').css({
							"background-color": "#FFCC00"
						});
					} else {
						$('.message').css({
							"background-color": "#C9C909"
						});
					}
				}

				function clearMessage() {
					$('.messageContainer').empty();
				}

				function displayMoves() {
					$('#moveCount').text(_moveCounter);
				}

				function assignClickHandler() {
					$('.cell').bind('click', function(){
						if (isValidClick($(this))) {
							var emptyCellId = getCellId(_emptyCellRow, _emptyCellColumn);
							var emptyCell = document.getElementById(emptyCellId);

							//assign empty cell row and column to the currently clicked cell
							//do not move this piece of code as this has to be done before the nodes are swapped
							_emptyCellRow = $(this).data('row');
							_emptyCellColumn = $(this).data('column');

							swapNodes(emptyCell, this);

							_moveCounter = _moveCounter + 1;

							displayMoves();

							if (isPuzzleSolved()) {
								displayMessage({
									"text": "Awesome, you solved the puzzle in " + _moveCounter + " moves. <input type='button' value='Publish to Wall' class='share fbButton'/>",
									"type": "success"
								});
								$('.cell').unbind('click');
							}
						} else {
							displayMessage({
								"text": "Please click on a number next to empty cell",
								"type": "warning"
							});
						}
					});
				}

				function wallPublish() {	
					FB.ui(
						 {
							 method: 'stream.publish',
							 message: 'I just Solved ' + _boardColumns + '-puzzle',
							 attachment: {
								 name: 'n-puzzle',
								 description: (
									 'I solved ' + _boardColumns + '-puzzle in ' + _moveCounter + ' moves.'
								 )
							 },
							 action_links: [
								{ text: 'n-puzzle', href: '<?php echo APP_URL; ?>' }
							 ]
						 },
						 function(response) {
							 if (response && response.post_id) {
								 document.location.href = "invite.php";
							 } else {
								 document.location.href = "invite.php";
							 }
						 }
					 );
				}
				
				//function definitions - end

				//inline - start 
				$('#boardRows').val(_boardRows);
				$('#boardColumns').val(_boardColumns);

				createBoard(_boardRows, _boardColumns);

				//event bindings - start
				$('#newGameButton').bind('click', function(){
					var inputBoardRows = $('#boardRows').val();
					var inputBoardColumns = $('#boardColumns').val();
					if (validateInputBoardValues(inputBoardRows, inputBoardColumns)) {
						_boardRows = parseInt(inputBoardRows);
						_boardColumns = parseInt(inputBoardColumns);

						createBoard(_boardRows, _boardColumns);
						//clear any previously assigned message
						clearMessage();
						//clear the previous score count
						_moveCounter = 0;
						displayMoves();
					}
				});

				$('#boardRows').bind('change', function(){
					$('#boardColumns').val($(this).val());
				});

				//wall publish 
				$('.share').live('click', function(){
					wallPublish();
				});

				//invite friends
				$('#inviteFriends').bind('click', function(){
					document.location.href = "invite.php";	
				});
				//event bindings - end
				//inline - end 
			});


		</script>
	</head>
	<body>
		<?php require_once "load_facebook_js.php"; ?>

		<div class="actionLinkContainer">
			<div class="fRight">
				<fb:bookmark/>
			</div>
			<div class="fLeft">
				<input class="fbButton" type="button" id="inviteFriends" value="Invite your friends to n-puzzle"/>
			</div>
		</div>

		<br/>	
		
		<div class="messageContainer">
		</div>

		<div class="otherStuff">
			<p><span class="moveCountContainer">Moves:<span id="moveCount">0</span></span></p>
		</div>

		<div id="boardContainer">
		</div>

		<div class="otherStuff">
			<p>
				<input type="text" id="boardRows" size="1"/>X<input type="text" id="boardColumns" size="1" disabled="disabled"/>
				<input type="button" value="New Game" id="newGameButton"/>
			</p>
		</div>

		<div class="introduction">
			Objective of n-puzzle is to arrange the numbers on the board in order. Begin by clicking on any number next to the empty cell. Read more about n-puzzle <a href="http://en.wikipedia.org/wiki/Fifteen_puzzle" target="_new">here</a>.
		</div>

	</body>
</html>
