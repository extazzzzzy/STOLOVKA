# STOLOVKA
Веб-сервис доставки еды "STOLOVKA". 
## Описание
Сервис включает себя ролевую политику: менеджер, курьер, кухня, покупатель. У всех пользователей имеется личный профиль, а также страница заказов. У покупателя есть страница с корзиной.
1) Менеджер. Менеджер может редактировать меню: добавлять новые позиции, редактировать и удалять существующие. Возможность установления любого статуса для заказа.  Менеджер видит абсолютно все заказы.
2) Курьер. Курьер может принимать и видеть заказы только со статусом "Ожидает курьера" и "На кухне". Видна следующая информация о заказе: статус, время к которому нужно доставить или "Как можно скорее", имя клиента, адрес, номер телефона клиента.
3) Кухня. Кухня может принимать и видеть заказы только со статусом "Принят". После подтверждения заказа кухней, его статус меняется на "На кухне". Кухня видит все позиции заказа и время доставки или "Как можно быстрее".
***КОГДА КУХНЯ СДЕЛАЛА ЗАКАЗ И ПОМЕНЯЛА СТАТУС НА "ОЖИДАЕТ КУРЬЕРА", КУРЬЕР МОЖЕТ ПРИНЯТЬ ЗАКАЗ, ПОСЛЕ ЧЕГО КУХНЯ ДОЛЖНА ПОДТВЕРДИТЬ ПРИНЯТИЕ И СТАТУС ЗАКАЗА ИЗМЕНЯЕТСЯ НА "У КУРЬЕРА".
4) Покупатель. Покупатель может добавить блюда из меню в корзину. Некоторые блюда можно будет модифицировать, добавлять или убирать ингридиенты. В корзине будет кнопка оформления заказа, после чего будет необходимо ввести адрес доставки, время доставки или "Как можно быстрее". После оформления покупатель сможет отслеживать статус своего заказа.
## Установка
1) Укажите куда бы вы хотели клонировать проект: cd C:/projects/name_project
2) Клонируйте репозиторий: git clone https://github.com/extazzzzzy/STOLOVKA.git
3) Скачайте готовую базу данных MySQL: *тут будет ссылка* и поместите её в папку с вашими другими БД(обычно это db/mysql). Если у вас OpenServer - поставьте пароль "root" для БД. Если у вас MAMP - можете сразу начать пользоваться.
!!! ЗНАЧЕНИЯ В СТОЛБЦЕ role в таблице users ДОЛЖНЫ БЫТЬ: user, manager, cook ИЛИ deliveryman !!!
