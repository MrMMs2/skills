/*JS*/
$(document).ready(
	function(){
		showOrHideElementsOnloadPage('#id_annualplanning_yes', '#id_methodology');
		showOrHideElementsOnloadPage('#id_trainingactionsociety_yes', '#id_trainingactionsociety');
		showOrHideElementsOnloadPage('#ckstruct6', '.fieldsplataforms');
		showOrHideElementsOnloadPage('#id_planactiontngnextyear_yes', '#fitem_id_fileplannextyear');
		showOrHideElementsOnloadPage('#id_programskills_yes', '#id_programskills');
		showOrHideElementsOnloadPage('#id_evaluation_yes', '#id_typeevaluation');
		
		showOrHideElementsOnloadPage('#psk1', '#tblphase_psk1');
		showOrHideElementsOnloadPage('#psk2', '#tblphase_psk2');
		showOrHideElementsOnloadPage('#psk3', '#tblphase_psk3');
		showOrHideElementsOnloadPage('#psk4', '#tblphase_psk4');
		checkPhaseStageProgramSkills();
		
		checkcourses360hOnloadPage();
		checkTrainingOnloadPage();
	}
);

function showOrHideElementsOnloadPage(checked, itemAction){
	if($(checked).is(':checked')){
		 $(itemAction).show('blind');
	 }else{
		 $(itemAction).hide('blind');
	 }
}

function checkTrainingOnloadPage(){
	var $elements = $('.ckformativeaction');
	$($elements).each(function(pos, element){
		var id = $(element).attr('data-id');
		if($(element).is(':checked')){
			 $("#training"+id).show('blind');
		 }
	});
}

function checkcourses360hOnloadPage(){
	if($('#courses360h').is(':checked')){
		 $("#accordion").show('blind');
		 $(".intructions").show('blind');
	 }
}

function checkPhaseStageProgramSkills(){
	var jphases = $("input[name='phasestageprogramskills']").val();
	var ophases = JSON.parse(jphases);
	for (var stage in ophases){
		for (var phase in ophases[stage]){
			var value = ophases[stage][phase];
			var idfild = '#id_'+stage+'-'+phase+'_'+value;
			$(idfild).prop( "checked", true );
		}
	}
}