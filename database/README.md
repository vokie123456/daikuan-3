# 数据库


> 用于存储数据表结构及部份数据填充

+ 新建表  
`php artisan make:migration [create_users_table]`

+ 修改表  
`php artisan make:migration add_column_to_users_table --table=users`

+ 迁入数据库  
`php artisan migrate`

+ 回滚  
`php artisan migrate:rollback --step=N`

+ 运行默认填充器(DatabaseSeeder)  
`php artisan db:seed`  

+ 运行指定填充器  
`php artisan db:seed --class=UsersTableSeeder`