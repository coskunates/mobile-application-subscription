## Mobile Application Subscription Management API
### Kurulum

```shell
docker-compose -f docker/docker-compose.yml down -v
docker-compose -f docker/docker-compose.yml up -d --build
docker exec -ti sa_php sh -c "cd api && composer update && cp .env.example .env && php artisan migrate:refresh --seed"
docker exec -ti sa_php sh -c "cd mock && composer update && cp .env.example .env"
docker exec -ti sa_php sh -c "supervisord -c /etc/supervisord.conf"
docker exec -ti sa_php sh -c "crond -l 2 -d 8"
```
veya
```shell
chmod +x install.sh
./install.sh
```
Subscription API için Nginx http://localhost:8081 veya http://subscriptionapi.net:8081
 üzerinden hizmet verecek şekilde konfigüre edilmiştir. Repo içerisindeki postman environment collection ını kullanabilmek için aşağıdaki satırları etc/hosts dosyanıza eklemeniz gerekmektedir.
```shell
127.0.0.1 subscriptionapi.net
127.0.0.1 mockapi.net
```

### Açıklamalar
- API, Worker, Callback modülleri "api" projesi içerisinde yapılmıştır. Mock modülü ise "mock" projesi içerisindedir.
- Worker tarafında bir CheckSubscriptions cronu .env dosyasında belirtilen sayıda ayağa kalkarak kendi grubundaki abonelikleri kontrol etmektedir. Her bir abonelik oluşturulurken ilgili worker_group ataması da device id sine göre yapılmaktadır. Örneğin device id si 8 ise ve env dosyasında WORKER_COUNT 10 olarak verilmişse worker_group 8 olarak atanmakta ve ayağa kalkan cronlardan mod u 8 olan process bu kaydı işlemektedir.
- CheckSubscriptions cron u işleri RabbitMQ üzerine biriktirmekte ve ilgili consumerlar tarafından eritilmektedir. Burada consumer sayısı 10 olarak belirlenmiştir. Ancak konfiürasyon dosyası üzerinden artırılabilir.
- CheckSubscription cron u dakika da 1 kez çalışacak şekilde düzenlenmiştir.
- Başarısız olan işler failed_jobs a yazılmakta, 5 dakikada çalışan bir cron ile tekrar rabbit ilgili işleri eklemektedir.
- Callback mekanizması yine RabbitMQ üzerinde events queue su üzerinden işlemektedir.
- Raporlama işlemleride Callback mekanizmasında kullanılan event leri dinleyerek RabbitMQ üzerinden işlemektedir.

#### RabbitMQ UI Bağlantı Bilgileri
URL: http://127.0.0.1:15672 <br />
Kullanıcı Adı: rabbitmq <br />
Şifre: rabbitmq

#### Redis Client Bağlantı Bilgileri
Host: 127.0.0.1 <br />
Port: 6372 <br />

#### MySQL Client Bağlantı Bilgileri
Host: 127.0.0.1 <br />
Port: 4306 <br />
Kullanıcı Adı: root <br />
Şifre: root <br />
Database Adı: subscription <br />

