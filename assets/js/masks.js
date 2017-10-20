/* Projeto Gestao por competencias
 * Script para mascaras
 * */

$(document).ready(function() {
	// Mascara para money
	$(".money").maskMoney({showSymbol:true, symbol:"R$", decimal:",", thousands:"."});
	
	// Mascara dos campos numericos (#quantidade de servidores, #id_qtdemagistrates)
	onlyNumbersWithMsg("qtdeservers");
	onlyNumbersWithMsg("qtdemagistrates");
	
	// Mascara para percentual - deprecated
	$(".percent").mask("9?99%");
	    
    $(".percent").on("blur", function() {
    	var value = (parseInt($(this).val()) > 100) ? $(this).val('100%') : $(this).val();
        
    	if( $(this).val().length == 1 || $(this).val().length == 2){
    		
    		value = $(this).val() + '%';
    		
    		 $(this).val( value );
    	}
    });
	
});


//Aceitar apenas numeros com mensagem
function onlyNumbersWithMsg(id) {
	$("#id_"+id).keypress(function(e) {
		// if the letter is not digit then display error and don't type anything
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			// display error message
			$("#id_error_"+id).html("Apenas Dígitos").show();
			return false;
		}
	});
}

// Aceitar apenas numeros generico
function onlyNumbers(e) {
	var tecla = (window.event) ? event.keyCode : e.which;
	if ((tecla > 47 && tecla < 58))
		return true;
	else {
		if (tecla == 8 || tecla == 0)
			return true;
		else
			return false;
	}
}

function setMaskMoney(id){
    $(".money"+id).maskMoney({showSymbol:true, symbol:"R$", decimal:",", thousands:"."});
};