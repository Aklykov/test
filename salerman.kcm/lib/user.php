<?php

namespace Salerman\Kcm;

class User {

	const GROUP_ADMIN = 1; // группа "Администраторы"
	const GROUP_ALERT_ERROR_IMPORT = 7; // группа "Оповещения об ошибках импорта"
	const NOIMAGE = '/static/img/noimage.jpg';

	public $id;
	public $login;
	public $email;
	public $name;
	public $secondName;
	public $lastName;
	public $fullName;
	public $avatar;
	public $avatarSrc;
	public $phone;
	public $printPhone;
	public $town;

	/**
	 * Получить пережатый аватар
	 */
	public function getAvatar() {
		$imgW = 60;
		$imgH = 60;
		if( !empty($this->avatar) ) {
			$arFileTmp = \CFile::ResizeImageGet(
				$this->avatar,
				array('width' => $imgW, 'height' => $imgH),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				//BX_RESIZE_IMAGE_EXACT,
				true
			);
			$this->avatarSrc = $arFileTmp['src'];
		} else {
			$this->avatarSrc = self::NOIMAGE;
		}
	}

	/**
	 * Получить список пользователей
	 *
	 * @param array $arFilter
	 * @return User[]
	 */
	static public function getList($arFilter=array()) {
		$arObj = array();
		$rsUsers = \CUser::GetList(($by='last_name'), ($order='desc'), $arFilter,  array('SELECT'=>array('UF_*')));
		while( $arUser = $rsUsers->Fetch() ) {
			$obj = new self();
			$obj->id = $arUser['ID'];
			$obj->login = $arUser['LOGIN'];
			$obj->email = $arUser['EMAIL'];
			$obj->name = $arUser['NAME'];
			$obj->secondName = $arUser['SECOND_NAME'];
			$obj->lastName = $arUser['LAST_NAME'];
			$obj->fullName = $arUser['NAME'] . ' ' . $arUser['LAST_NAME'];
			$obj->phone = $arUser['PERSONAL_PHONE'];
			$obj->town = $arUser['PERSONAL_CITY'];
			$obj->avatar = $arUser['PERSONAL_PHOTO'];
			$obj->printPhone = self::GetPrintPhone( $obj->phone );
			$obj->GetAvatar();
			$arObj[] = $obj;
		}
		return $arObj;
	}

	/**
	 * Получить пользователя по ИД
	 *
	 * @param int $id
	 * @return User
	 */
	static public function getByID($id) {
		$arFilter = array('ID' => $id);
		$arObj = self::GetList($arFilter);
		return current($arObj);
	}

	/**
	 * Получить пользователя по Email
	 *
	 * @param string $email
	 * @return User
	 */
	static public function getByEmail($email) {
		$arFilter = array('=EMAIL' => $email);
		$arObj = self::GetList($arFilter);
		return current($arObj);
	}

	/**
	 * Получить пользователя по Логин
	 *
	 * @param string $login
	 * @return User
	 */
	static public function getByLogin($login) {
		$arFilter = array('LOGIN_EQUAL_EXACT' => $login);
		$arObj = self::GetList($arFilter);
		return current($arObj);
	}

	/**
	 * Преобразуем телефон в +7(923)12-34-567
	 *
	 * @param string $phone
	 * @return string
	 */
	static public function getPrintPhone($phone) {
		if( !empty($phone) ) {
			$p1 = substr($phone, 0, 3);
			$p2 = substr($phone, 3, 2);
			$p3 = substr($phone, 5, 2);
			$p4 = substr($phone, 7, 3);
			return '+7($p1)$p2-$p3-$p4';
		}
	}

	/**
	 * Проверить пароль пользователя
	 * @param int $userId
	 * @param string $password
	 * @return bool
	 */
	static public function isUserPassword($userId, $password) {
		$userData = \CUser::GetByID($userId)->Fetch();
		$salt = substr($userData['PASSWORD'], 0, (strlen($userData['PASSWORD']) - 32));
		$realPassword = substr($userData['PASSWORD'], -32);
		$password = md5($salt.$password);
		return ($password == $realPassword);
	}

	/**
	 * Получить массив Email-ов Администраторов
	 *
	 * @return array
	 */
	static public function getArrayEmailsAdmin() {
		$arEmails = array();
		$arFilter = array( 'GROUPS_ID' => array(self::GROUP_ADMIN) );
		$rsUsers = \CUser::GetList( ($by='last_name'), ($order='desc'), $arFilter );
		while( $arUser = $rsUsers->Fetch() ) {
			$arEmails[] = $arUser['EMAIL'];
		}
		return $arEmails;
	}

	/**
	 * Получить массив Email-ов группы "Оповещения об ошибках импорта"
	 *
	 * @return array
	 */
	static public function getArrayEmailsAlertErrorImport() {
		$arEmails = array();
		$arFilter = array( 'GROUPS_ID' => array(self::GROUP_ALERT_ERROR_IMPORT) );
		$rsUsers = \CUser::GetList( ($by='last_name'), ($order='desc'), $arFilter );
		while( $arUser = $rsUsers->Fetch() ) {
			$arEmails[] = $arUser['EMAIL'];
		}
		return $arEmails;
	}

}