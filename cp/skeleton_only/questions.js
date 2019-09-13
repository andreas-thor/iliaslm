const maxNumberOfTries = 3;

var questions = new Array();
var numberOfTries = new Array();

function initAllQuestions() {

	var questionsDiv = $('#questions');

	questions.forEach(function(questionJson, questionId) {

		numberOfTries[questionId] = 0;
		questionsDiv.append('<div id="question' + questionId + '"></div>');

		if (questionJson.type == 'mc') {
			initQuestion_MC(questionJson, questionId);
		}
		if (questionJson.type == 'gap') {
			initQuestion_GAP(questionJson, questionId);
		}

	});

}

function initQuestion_GAP(questionJson, questionId) {

	var containerDiv = $('#question' + questionId);
	
	containerDiv.append('<p>');
	questionJson.blocks.forEach (function(block, blockId) { 
		
		if (block.type == 'text') {
			containerDiv.append (block.text);
		}
		if (block.type == 'gap') {
			
			var id = 'gapBlock_' + questionId + '_' + blockId;
			containerDiv.append('<select id="' + id + '"></select>');
			var select = containerDiv.find('#' + id);
			select.append('<option value="" selected disabled hidden>Bitte auswählen ...</option>');
			block.choices.forEach (function (choice) { 
				select.append ('<option value="' + choice.value + '">' + choice.text + '</option>'); 
			})
		}
		
	});
	containerDiv.append('</p>');
	
	// add feedback and "check answer" button
	containerDiv.append('<pre name="feedback" style="display:none" class="code-example"><code class="code-example-body"></code></pre>');
	containerDiv.append('<button class="button-primary" onclick="checkAnswer_GAP(' + questionId + ')">Antwort abgeben</button>');
}


function updateFeedback (questionId, points, maxPoints) {
	
	// compute feedback
	var feedBackLine1 = "";
	var feedBackLine2 = "";

	if (points == maxPoints) {
		feedBackLine1 = "Korrekt!";
		feedBackLine2 = "";
	} else {
		if (numberOfTries[questionId] >= maxNumberOfTries) {
			feedBackLine1 = "Maximale Anzahl an Versuchen erreicht.";
			feedBackLine2 = "Siehe oben für die richtige Lösung.";
		} else {
			feedBackLine1 = "Ihr Ergebnis: " + points + " von " + maxPoints + " Punkten.";
			feedBackLine2 = "Sie haben noch " + (maxNumberOfTries - numberOfTries[questionId]) + " Versuch(e).";
		}
	}
	
	// update UI: show feedback; disable/reset checkboxes
	var question = $("#question" + questionId);
	question.find("pre[name='feedback']").find("code").text(feedBackLine1 + "\n" + feedBackLine2);
	question.find("pre[name='feedback']").show();

}


function checkAnswer_GAP(questionId) {

	numberOfTries[questionId]++;

	// compute points
	var question = $("#question" + questionId);
	var listSelects = question.find("select");
	var maxPoints = 0;
	var points = 0;
	listSelects.each(function(idx, sel) {
		maxPoints++;
		if ($(sel).find("option:selected").val() == 1) { 
			points++;
		}
	});

	updateFeedback (questionId, points, maxPoints);
	
	if ((points == maxPoints) || (numberOfTries[questionId] >= maxNumberOfTries)) {
		question.find("button").hide();
		listSelects.each(function(idx, sel) {
			$(sel).prop("disabled", true);
			$(sel).prop("value", 1);
		});
	}
}







function initQuestion_MC(questionJson, questionId) {

	// add question text
	var containerDiv = $('#question' + questionId);
	containerDiv.append('<p>' + questionJson.text + '</p>');

	// add answer options as multiple choice buttons
	containerDiv.append('<fieldset></fieldset>');
	var fieldSet = containerDiv.find('fieldset');
	questionJson.answers.forEach(function(answer) {
		fieldSet.append('<label><input type="checkbox" value="' + answer.value + '"><span class="label-body">' + answer.text + '</span></label>');
	})

	// add feedback and "check answer" button
	containerDiv.append('<pre name="feedback" style="display:none" class="code-example"><code class="code-example-body"></code></pre>');
	containerDiv.append('<button class="button-primary" onclick="checkAnswer_MC(' + questionId + ')">Antwort abgeben</button>');
}

function checkAnswer_MC(questionId) {

	numberOfTries[questionId]++;

	// compute points
	var question = $("#question" + questionId);
	var listInputs = question.find("input[type='checkbox']");
	var maxPoints = 0;
	var points = 0;
	listInputs.each(function(idx, li) {
		maxPoints++;
		if ((li.checked ? 1 : 0) == li.value)
			points++;
	});

	
	updateFeedback (questionId, points, maxPoints);

	if ((points == maxPoints) || (numberOfTries[questionId] >= maxNumberOfTries)) {
		question.find("button").hide();
		listInputs.each(function(idx, li) {
			$(li).prop("disabled", true);
			$(li).prop("checked", $(li).prop("value") == 1);
		});
	}
}