var questBookList;
$(document).ready(function(){

	// добавление нового отзыва
	$('#questBookForm').submit(function(event){
		event.preventDefault();

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				action: 'QuestBook:addRecords',
				name: $('#QB_NAME').val(),
				email: $('#QB_EMAIL').val(),
				body: $('#QB_BODY').val(),
			},
			success: function(data)
			{
				if (data['result']) {
					questBookList.updateLastRecords();
				} else {
					alert(data['message']);
				}
			},
			dataType: 'json'
		});
	});

	// обновление списка последних отзывов
	questBookList = new Vue({
		el: '#questBookList',
		data: {
			items: []
		},
		methods: {
			updateLastRecords: function() {
				$.ajax({
					url: 'ajax.php',
					type: 'GET',
					data: {
						action: 'QuestBook:getLastRecords',
						limit: 5
					},
					success: function(data)
					{
						if (data['result']) {
							questBookList.items = data['data']['items'];
						} else {
							alert(data['message']);
						}
					},
					dataType: 'json'
				});
			}
		}
	});
	questBookList.updateLastRecords();
});


