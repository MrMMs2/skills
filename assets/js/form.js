/*JS*/
$(document).ready(
	function(){
		// Mostrar e esconder elementos (Sim ou não).
		showHideElements('id_methodology', 'id_annualplanning_yes', 'id_annualplanning_no');
		showHideElements('id_trainingactionsociety', 'id_trainingactionsociety_yes', 'id_trainingactionsociety_no');
		showHideElements('id_typeevaluation', 'id_evaluation_yes', 'id_evaluation_no');
		showHideElements('id_programskills', 'id_programskills_yes', 'id_programskills_no');
		showHideElements('fitem_id_fileplannextyear', 'id_planactiontngnextyear_yes', 'id_planactiontngnextyear_no');
		
		// Mostrar e esconder os elementos (checkbox)
		showHideElementsCheckbox('#ckstruct6', '.fieldsplataforms');
		showHideElementsCheckbox('#psk1', '#tblphase_psk1');
		showHideElementsCheckbox('#psk2', '#tblphase_psk2');
		showHideElementsCheckbox('#psk3', '#tblphase_psk3');
		showHideElementsCheckbox('#psk4', '#tblphase_psk4');
		
		// Mostrar e esconder elemento (input)
		showHideElementsInputbox('#id_qtdeservers', '.qtdeservers_intructions');
		
		// Esconde treinamentos quando carrega pagina
		$(".training").hide();
				
		// Iniciando accordion
		$("#accordion").hide();
		$(".intructions").hide();
		
		$("#courses360h").change(function(){
			if($('#courses360h').is(':checked')){
				 $("#accordion").show('blind');
				 $(".intructions").show('blind');
			 }else{
				 $("#accordion").hide('blind');
				 $(".intructions").hide('blind');
			 }
		});// fim function
		
		
		// Adicionando e removendo linhas da tabela de treinamentos
		RemoveTableRow = function(handler) {
		    var tr = $(handler).closest('tr');

		    tr.fadeOut(400, function(){ 
		      tr.remove(); 
		    }); 

		    return false;
		  };// fim RemoveTableRow
		  
		  var trainingid;
		  AddTableRow = function(trainingid) {
		      
		      var newRow = $("<tr class='rowtraining"+trainingid+"'>");
		      var iterator = $("#training"+trainingid+" tbody tr").length;
		      var cols = "";
		      
		      cols += "<td class='area'>" +
		      			"<select class='span12' onchange='validateTrainingArea(this)' name='trainings[training_"+trainingid+"][row"+iterator+"][tema]' data-training='"+trainingid+"'>"+
		      				"<option value=''>SELECIONE</option>"+
			      			"<option value='TECNOLOGIA DA INFORMAÇÃO'>TECNOLOGIA DA INFORMAÇÃO</option>"+
			      			"<option value='JUDICIÁRIA'>JUDICIÁRIA</option>"+
			      			"<option value='ADMINISTRATIVA/GESTÃO'>ADMINISTRATIVA/GESTÃO</option>"+
			      			"<option value='LÍNGUAS'>LÍNGUAS</option>"+
			      			"<option value='RESPONSABILIDADE SOCIAL/SAÚDE E QUALIDADE DE VIDA'>RESPONSABILIDADE SOCIAL/SAÚDE E QUALIDADE DE VIDA</option>"+
			      			"<option value='EDUCAÇÃO'>EDUCAÇÃO</option>"+
		      			"</select>"+
		      		"</td>";
		      cols += "<td class='cell'><input type='text' onkeypress='return onlyNumbers(event)' name='trainings[training_"+trainingid+"][row"+iterator+"][participantes]'> </td>";
		      cols += "<td class='cell'><input type='text' onkeypress='return onlyNumbers(event)' name='trainings[training_"+trainingid+"][row"+iterator+"][ntrainings]'> </td>";
		      cols += '<td class="actions">';
		      cols += '<button class="btn btn-large btn-danger" onclick="RemoveTableRow(this)" type="button"><i class="fa fa-trash"></i></button>';
		      cols += '</td>';
		      iterator ++;
		      newRow.append(cols);
		      
		      $("#training"+trainingid).append(newRow);
		      
		      return false;
		  }; // fim AddTableRow
	}
); // fim ready

function training_verify(training){
	  if($("#tgn_"+training).is(':checked')){
		 $("#training"+training).show('blind');
	 }else{
		 $("#training"+training).hide('blind');
	 }
}

// Função que esconde e apresenta elementos
function showHideElements(id_showHide, checkedYes, checkedNo){
	
	$("#"+id_showHide).hide();
	
	$("#"+checkedYes).change(function(){
		  if($('#'+checkedYes).is(':checked')){
			 $("#"+id_showHide).show('blind');
		  }
	});// fim function
	
	$("#"+checkedNo).change(function(){
		 if($('#'+checkedNo).is(':checked')){
			 $("#"+id_showHide).hide("blind");
		 }
	}); // fim function
}

// funcao que esconde e apresenta elementos checkbox
function showHideElementsCheckbox(check, element){
	$(element).hide();
	
	$(check).change(function(){
		  if($(check).is(':checked')){
			 $(element).show('blind');
		  }else{
			  $(element).hide("blind");
		  }
	});// fim function
}

function showHideElementsInputbox(finput, element){
	$(finput).focus(function(){
        $(element).show('blind');
    });
	$(finput).focusout(function(){
        $(element).hide("blind");
    });
}

//funcao responsavel por manipular os processadores.
function processHandler(e){
	processEvasion(e);
}

//funcao responsavel por calcular percentual de evasao
function processEvasion(e){
	
	var areaId 		= $(e).attr('data-area');
	var modalidade 	= $(e).attr('data-mod');
	
	var ncapacitados = $('#area'+areaId+'_nqual_'+modalidade).val();
	var ninscritos = $('#area'+areaId+'_nenroll_'+modalidade).val();
	var nreprovados = $('#area'+areaId+'_ndisap_'+modalidade).val();
	
	var nevasao = (ninscritos - ncapacitados) - nreprovados;
	var percentEvasao = ((nevasao * 100) / ninscritos);
	
	if(percentEvasao < 0) percentEvasao = 0;
	if(nevasao < 0) nevasao = 0;
	
	if(!isNaN(percentEvasao) && ncapacitados > 0 && ninscritos > 0 && nreprovados >= 0){
		// console.log(nevasao, percentEvasao);
		$('#area'+areaId+'_percent_'+modalidade).val(percentEvasao.toFixed(0)+"%");
		$('#area'+areaId+'_ndesist_'+modalidade).val(nevasao);
	}
}

// funcao responsavel por validar areas de treinamentos
function validateTrainingArea(element){
	var id = $(element).attr('data-training');
	var areaAtual = $(element).val();
	var $elementAtual = $(element).parent('td').parent('tr');
	var $trainings = $('.rowtraining'+id);
	
	// percorre treinamentos
	$($trainings).each(function(pos, training){
		var $training = $(training);
		if(!$elementAtual.is($training)){
			var otherArea = $training.find('.area select').val();
			if(areaAtual === otherArea){
				//console.log('A Area ' + areaAtual+ ' ja foi escolhida! Escolha outra.');
				alert('A Área ' + areaAtual + ' Já foi escolhida! Por favor, Escolha outra área.');
				$(element).val('').css('border', '1px solid #d43f3a');
			}else
				$(element).css('border', '1px solid #ccc');
		}
	});
}