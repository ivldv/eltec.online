var form_data="";
$(document).ready(function(){
    $("#form").submit(function() { //устанавливаем событие отправки для формы с id=form
		form_data = $(this).serialize(); //собераем все данные из формы
		$.ajax({
			type: "POST", //Метод отправки
			url: "mail.php", //путь до php фаила отправителя
			data: form_data,
			dataType: 'text',
			success: function(data) {
					//код в этом блоке выполняется при успешной отправке сообщения
					console.log(data);
					$("#exampleModalCenter").modal('toggle');
					$('.modal-backdrop').remove(); 
									},
									})
		return false;
	}); 
	$('#email').blur(function() {
		if($(this).val() != '') {
			var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
			if(pattern.test($(this).val())){
				$(this).css({'border' : '1px solid #569b44'});
				$('#valid').text('Верно');
			} else {
				$(this).css({'border' : '1px solid #ff0000'});
				$('#valid').text('Не верно');
			}
		} else {
			$(this).css({'border' : '1px solid #ff0000'});
			$('#valid').text('Поле email не должно быть пустым');
		}
	});
});  