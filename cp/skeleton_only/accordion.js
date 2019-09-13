function accordionClick(button) {

	// hide all content; make all buttons active
	$("button.accordion").next().hide();
	$("button.accordion").removeClass("accordionOpen");
	$("button.accordion").removeClass("button");
	$("button.accordion").addClass("accordionClosed");
	$("button.accordion").addClass("button-primary");

	// show current content; make this button inactive
	$(button).next().show();
	$(button).removeClass("accordionClosed");
	$(button).removeClass("button-primary");
	$(button).addClass("accordionOpen");
	$(button).addClass("button");

}