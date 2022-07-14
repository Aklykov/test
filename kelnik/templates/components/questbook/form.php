<form id="questBookForm">
	<div class="mb-3">
		<label for="exampleFormControlInput1" class="form-label">Имя<span class="text-danger">*</span></label>
		<input type="text" id="QB_NAME" name="QB_NAME" class="form-control" id="exampleFormControlInput1" placeholder="Имя" required>
	</div>
	<div class="mb-3">
		<label for="exampleFormControlInput1" class="form-label">Email<span class="text-danger">*</span></label>
		<input type="email" id="QB_EMAIL" name="QB_EMAIL" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com" required>
	</div>
	<div class="mb-3">
		<label for="QB_BODY" class="form-label">Сообщение<span class="text-danger">*</span></label>
		<textarea class="form-control" id="QB_BODY" name="QB_BODY" rows="3" placeholder="Сообщение" required></textarea>
	</div>
	<button class="btn btn-primary" type="submit">
		<span role="status" aria-hidden="true"></span>
		Отправить
	</button>
</form>