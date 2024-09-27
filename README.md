# Project 3 เกลอ

## .env file:

- Support duplicate multiple email account in difference organize
- Can enable/disable register or forgot password in belib back-end in client manage data
> APP_FORGOT_PASSWORD -> work with settin from belib
>- **enable**: can access forgot password form
>- **disable**: can't access forgot password form
> APP_FORGOT_SENDMAIL -> work with settin from belib
>- **enable**: send url reset password to user email (support only real email)
>- **disable**: redirect to reset password page **(DANGER.!!** anyone can reset other account if know user email) 

## Installation Steps:

1. สร้าง database ชื่อว่า **belib_bdpath,belib_crpy,belib_readdi,belib_seed** ในเครื่อง
2. CD เข้าไปยัง folder ที่จะวาง source code เช่น htdocs หรือ www (ถ้ายังไม่มีให้สร้าง folder ชื่อว่า belib_bdpath แล้ว CD เข้าไป)
3. git clone -b develop https://gitlab.com/bookdose/tomahawk/auth-bdpath .
4. Copy ไฟล์ .env.example แล้วสร้างเป็นไฟล์ใหม่ที่เครื่องตัวเองชื่อว่า .env
5. เปิดไฟล์ .env ขึ้นมาแก้ไขค่าเหล่านี้
> - APP_URL 
> - DB_DATABASE, DB_USERNAME และ DB_PASSWORD
6. composer install
7. php artisan migrate:fresh --seed
8. php artisan storage:link
9. php artisan key:generate
10. chmod -Rf 777 storage/
11. php artisan optimize:clear && composer dump-autoload

