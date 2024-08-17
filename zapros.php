API записано в переменную  Token и равно 1642420:bdfff6ffdf916c94d65e270ac6509742


Запрсы с платформы manychat.com:

Проверяем есть ли в базе партнер или нет 

POST: https://sql.systemtop.pp.ua/mlm_boster/server.php

Тело:
{
"action":"is_sponsor",
"userid":"{{user_id}}",
"priglasitelid":"{{ref}}"
}
Сопоставление ответа:
$.is_sponsor — is_sponsor

Если  is_sponsor равно 1 делаем второй запрос и делаем запись в таблицу:
POST: https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело запроса:
{
"action":"new_user",
"userid":"{{user_id}}",
"priglasitelid":"{{ref}}",
"tgid":"{{tg_user_id}}",
"username":"{{tg_username}}",
"fullName":"{{full_name}}",
"firstName":"{{first_name}}",
"lastName":"{{last_name}}"
}
Сопоставление ответа:
$.in_base — in_base
$.sponsor_link — sponsor_link

Услови записи в таблицу:

Если пригласитель активный.
В столбце записано status active и у него есть 2 или менее с таким же статусом в первой линии партнеров — значит новый кандидат будет в первой линии у пригласителя. Если у пригласителя 3 или более активных партнеров в первой линиии значит пригласителю записывается только каждый 10 а 9 будут идти на структуру ниже стоящим активным.
Это называется перелив. Не активным переливы не идут.

Условия получения переливов.
Партнер должен быть активный и иметь в первой линии 1 или менее активного партера

Если в структуре нет партнеров, которым можно сделать перелив - находим любого партнера активного в таблице и рандомно ему даем партнера в первую линию

Если пригласитель не активный.
В столбце status будет записано passive. 
К нему в первую линию не записывается партнер. Делаем перелив соблюдая условия
Условия получения переливов.
Партнер должен быть активный и иметь в первой линии 1 или менее активного партера

Если в структуре нет партнеров, которым можно сделать перелив - находим любого партнера активного в таблице и рандомно ему даем партнера в первую линию

Пригласителю будет отправлено уведомление когда к нему в первую линию попадает новый кандидат: 
 В первой лини у вас новый кандидат: \n🧑‍💻 Имя: $fio \n😀 Ник: $username0"

Если в первую линию от перелива:
 В первой лини у вас новый кандидат: \n🧑‍💻 Имя: $fio \n😀 Ник: $username0 \n(Перелив)

Запрос который делает запись в таблицу 
POST: https://sql.systemtop.pp.ua/mlm_boster/server.php
В тело: {
"action":"update_user_all",
"userid":"{{user_id}}",
"username0":"{{username0}}",
"fio":"{{fio}}",
"phone":"{{phone}}",
"ref_link":"{{link_ref}}"
}
В сопоставление ответов
$.in_base — in_base
$.sponsor_link — sponsor_link


Запрос которые записывает в столбец  status значение  active
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"change_status",
"userid":"{{user_id}}",
"status":"active"
}

Запрос которые записывает в столбец  status значение  passive
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:

{
"action":"change_status",
"userid":"{{user_id}}",
"status":"passive"
}


Запрос который выводит статистику
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"statistic_user",
"userid":"{{user_id}}"
}
Приходит сообщение в телеграм на платформе manychat.com
▪️ Переходы по ссылке:  0
▪️ Переливы партнерам: 0 
▪️ Переливы от вышестоящих партнеров: 0 
▪️ Кандидатов в 1 линии: 0 
▪️ Активных партнеров 1 линии: 0 
▪️ Кандидатов в структуре: 0
▪️ Активных партнеров в структуре: 0

/cabinet - личный кабинет



Запрос который отправляет сообщение в телеграм на платформе manychat.com статистику структуры
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"base_user",
"userid":"{{user_id}}"
}

Ответ в сообщении:
Активные партнеры

Кандидаты
▪️  ()
▪️  ()
▪️  ()
▪️  ()


/cabinet - личный кабинет

Запрос который отправляет сообщение в телеграм на платформе manychat.com данные спонсора у кого в первой линии
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"sponsor_user",
"userid":"{{user_id}}"
}

Ответ в телеграм сообщением:
Ваш спонсор
▪️ Имя: 
▪️ Ник: 
▪️ Телефон: 
▪️ userid: 

/cabinet - личный кабинет

Запрос чтобы изменить свое имя в таблице
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"update_user_fio",
"userid":"{{user_id}}",
"fio":"{{fio}}"
}
Ответ в телеграм:
Ваше имя обновлено

/cabinet - личный кабинет

Запрос чтобы изменить username в телеграм
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:

{
"action":"update_user_username0",
"userid":"{{user_id}}",
"username0":"{{username0}}"
}

Ответ в телеграм:
Ваш ник обновлен

/cabinet - личный кабинет

Запрос чтобы изменить номер телефона
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"update_user_phone",
"userid":"{{user_id}}",
"phone":"{{phone}}"
}
Ответ в телеграм:
Ваш номер изменен

/cabinet - личный кабинет

Запрос чтобы изменить свою ссылку
POST https://sql.systemtop.pp.ua/mlm_boster/server.php
Тело:
{
"action":"update_user_ref_link",
"userid":"{{user_id}}",
"ref_link":"{{link_ref}}"
}

Ответ в телеграм:
 Ваша ссылка изменена.

/cabinet - личный кабинет


Запрос который добавляет в таблицу значения
POST https://sql.systemtop.pp.ua/mlm_boster/server.php

{
"action":"update_user_all",
"userid":"{{user_id}}",
"username0":"{{username0}}",
"fio":"{{fio}}",
"phone":"{{phone}}",
"ref_link":"{{link_ref}}"
}