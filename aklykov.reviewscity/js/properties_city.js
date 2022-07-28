$(document).ready(function(){
	PropertiesCity.init();
});

let PropertiesCity = {

	struction: [],
	$selectIblocks: '',
	$selectSections: '',

	init: function(){
		this.$selectIblocks = $('#ReviewsCityListIblock');
		this.$selectSections = $('#ReviewsCityListSection');

		BX.ajax.runAction("aklykov:reviewscity.api.city.getListIblockAndSections").then(function (response) {
			PropertiesCity.struction = response['data'];

			let selectedIblockId = 0;
			let selectedSectionId = PropertiesCity.$selectSections.data('value');
			if (selectedSectionId > 0) {
				for (let i in PropertiesCity.struction) {
					let iblock = PropertiesCity.struction[i];
					for (let j in iblock['SECTIONS']) {
						let section = iblock['SECTIONS'][j];
						if (section['ID'] == selectedSectionId)
							selectedIblockId = iblock['ID'];
					}
				}

				PropertiesCity.updateIblocks(selectedIblockId);
				PropertiesCity.updateSections(selectedIblockId, selectedSectionId);
			} else {
				PropertiesCity.updateIblocks(0);
			}
		}, function (response) {
			alert('Произошла ошибка! Попробуйте еще раз!');
		});

		this.$selectIblocks.change(function(){
			let selectedIblockId = $(this).val();
			PropertiesCity.updateSections(selectedIblockId);
		});
	},

	updateIblocks: function(selectedIblockId) {
		let html = '';
		html += '<option value="">Выберите ИБ</option>';
		for (let i in this.struction) {
			let iblock = this.struction[i];
			if (iblock['ID'] == selectedIblockId)
				html += '<option value="'+iblock['ID']+'" selected>'+iblock['NAME']+'</option>';
			else
				html += '<option value="'+iblock['ID']+'">'+iblock['NAME']+'</option>';
		}
		this.$selectIblocks.html(html);
	},

	updateSections: function(selectedIblockId, selectedSectionId) {
		let html = '';
		html += '<option value="">Выберите Раздел</option>';
		for (let i in this.struction) {
			let iblock = this.struction[i];
			if (iblock['ID'] == selectedIblockId) {
				for (let j in iblock['SECTIONS']) {
					let section = iblock['SECTIONS'][j];
					if (section['ID'] == selectedSectionId)
						html += '<option value="'+section['ID']+'" selected>'+section['NAME']+'</option>';
					else
						html += '<option value="'+section['ID']+'">'+section['NAME']+'</option>';
				}
			}
		}
		this.$selectSections.html(html);
	}
};