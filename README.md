#Краткий обзор фреймворка Kitty
Целью создания Kitty FW создать удобный и быстрый инструмент, разработки из коробки. 

Фреймворк специализируется в основном на работе с базой через модели
Работа с базой данных через `PDO` 
Модель поддерживает базы `mysql`,`sqlite3` в планах pgsql
Работа с `MongoDB` + `Document Model`
Большое количество генераторов которые облегчают рутиную работу.



Структура фреймворка
====================
<pre>
app
 -- backend  Админка
   -- controllers
    -- (файлы контролера)
   -- layouts
    -- (файл имя слоя)
   -- models
    -- (файлы модели)
   -- modules
	-- (Имя модуля)
	   -- controllers
	   -- layouts
        -- (файл имя слоя)
	   -- models
	   -- modules
	     -- ... итд модули могут быть вложенными 
	   -- views
   -- views
    -- (папка или файл вьюшки)
 -- frontend Сайт
assets
 -- backend
 -- frontend
framework
  -- classes  классы фреймворка
  -- models   базовые модели готовый функционал
  -- vendors  сторонние библиотеки
storage  папка хранения файлов, которые загрузили через админку
</pre>
