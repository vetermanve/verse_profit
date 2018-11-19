# Goals and Means
Сайт для учета расходов и доходов. 

Написан на Verse Framework (php)

# Фичи
- [x] Аваторизация
- [x] Заведение аккаунта друга
- [ ] Счета
- [ ] Списки хотелок
- [ ] Грппы
- [ ] Бюджет
- [ ] Долги
- [ ] Трекинг почасовой работы
- [ ] Примерка хотелок на бюджет
- [ ] Telegram bot

# Объекты
## Пользователь / User
Состояние: Готово.

```
id 
name
email
is_registered // todo
```

## Бюджет / Budget

```
id 
name
descripition
```


## Счет / Balance

```
id
name
name_official 
description
type - Наличные, Карта, Счет, Между Людьми 
balance_type - Текущий, Частный
budget_id - Принадлежность к бюджету 
```

## Цель / Goal

```
id
name - имя цели
description - описание цели
amount - сумма цели
budget_id - принадлежность к бюджету
created_at - когда создана
creator_id - кто cоздал
for_users - массив пользователей, для кого
status - завершена ли цель.
priority - срочность
time_accurancy - важность срока 
severity - обязательность
```

## Транзакция / Transaction

```
id
description - описание транзакции
goal_id - к какой цели транзакция
type - План, Факт
balance_type - Текущий, Частный
balance_from - Балнс с которого
balance_to - Баланс на который
budget_id - Принадлежность к бюджету 
```

## Отношение / Relation
Состояние: Готово.

```
id
owner_user_id - кто создал отношение
related_user_id - к кому отношение 
description - описание
// buget_id - Принадлежность к бюджету // todo 
```

