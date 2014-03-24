1. Требования
- php 5.3+
- php PDO + mysql support

2. Настройка и запуск
- Создаем базу, данные можно использовать из дампа data/data.sql
- Настраиваем параметры соединения с БД в файле playdb.php:
		Playdb::createApplication(array(
			'database' => array(
				// 'host' => '127.0.0.1' // default is localhost
				'user' => 'databaseuser',
				'database' => 'databasename',
				'password' => 'databaseaccesspassword',
			)
		));
- Запускаем с консоли php playdb.php для просмотров возможных вариантов комманд