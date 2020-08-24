# Voucher Module
<h2>Technical Stack </h2>
<ul>
<li>Symfony 5.1</li>
<li>Kafka</li>
<li>Docker</li>
<li>MySql 5</li>
<li>Php 7.4</li>
<li>Nginx</li>
<li>Phpmyadmin</li>
</ul>


<h2>Implementation</h2>
In order to migrate from a <b>monolithic application</b> to a <b>distributed system</b> and separate orders from voucher creation, while keep applying the business rule: <br><br> <i>“If an order that costs more than 100 euro gets marked as sent, the customer will receive a voucher worth 5 euro.“</i>
<br><br>
We proceeded to create two different <b>Microservices</b>: <b>Order Module</b> and <b>Voucher Module</b>, each one with its own Database: <b>orderdb</b> and <b>voucherdb</b> and will use Pub/Sub mechanism.
<br><br>
<b>Order Module:</b> Will be used to manage orders, and when the order is marked and the business rule is applied, it will push a message to a Kafka topic
<br><br>
<b>Voucher Module:</b> It will consume the upcoming events, and if the event is “GENERATE_VOUCHER_COMMAND” , the Microservice will trigger a command to generate and save the voucher for the correspondent order in its own database.


<h2>Installation</h2>

<h4>1- Install Docker and Docker-compose in your machine</h4>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;https://www.docker.com/products/docker-desktop <br>
<h4>2- Create a custom docker network (pub_sub_network)</h4>
- In order to communicate the two microservices
```
docker network create pub_sub_network
```

Order Module 
---
<h4>1- Clone order module repo</h4>
- ***Order module link:*** https://github.com/Salimify/order-module
```
git clone https://github.com/Salimify/order-module.git
```

<h4>2- Run order module container</h4>
```
docker-compose up -d
```
<h4>3- Login to order module container</h4>
```
docker-compose exec kafka_producer_php sh
```
<h4>4- Install composer packages</h4>
```
composer install
```
<h4>5- Run DB migrations</h4>
```
php bin/console doctrine:migrations:migrate
```
<h4>6- Populate DB with orders mock data using custom command</h4>
```
php bin/console app:populate-orders
```
<h4>7- Browse http://localhost:8787 to verify that Order Module is up and running.</h4>
<img src="https://i.ibb.co/3zVsv4w/1.jpg" alt="1" border="0"></a><br />

Voucher Module 
---
<h4>1- Clone Voucher module repo</h4>
- ***Voucher module link:*** https://github.com/Salimify/voucher-module
```
git clone https://github.com/Salimify/voucher-module.git
```

<h4>2- Run Voucher module container</h4>
```
docker-compose up -d
```
<h4>3- Login to Voucher module container</h4>
```
docker-compose exec kafka_consumer_php sh
```
<h4>4- Install composer packages</h4>
```
composer install
```
<h4>5- Run DB migrations</h4>
```
php bin/console doctrine:migrations:migrate
```
<h4>6- Browse http://localhost:8788 to verify that Voucher Module is up and running.</h4>
<img src="https://i.ibb.co/fMDBHs6/2.jpg" alt="2">

<h4>7- After confirming that the service is running, run the following command to start the Kafka consumer</h4>
***do not kill the app:consumer:voucher***
```
php bin/console app:consumer:voucher
```

Demo
---
<h4>1- Order Module: After populating cache, check if the database is populated properly</h4>
* Order Module endpoint to List orders: http://localhost:8787/orders <br><br>
<img src="https://i.ibb.co/VYvhHQW/oders-not-sent.jpg" alt="oders-not-sent" border="0" />
<br><br>
* Order Module Phpmyadmin url: http://localhost:8082/ (user: symfony , password: symfony ) <br><br>
<img src="https://i.ibb.co/MSSPb87/orderdb.jpg" alt="orderdb" border="0" />
<h4>2- Call this endpoint with the order id to mark it as sent</h4>
```
http://localhost:8787/order/{orderId}?sent=true
```
<img src="https://i.ibb.co/kSkTWPz/sent.jpg" alt="sent" border="0" />

<b>Applying business rule</b>
When the order mark as sent transaction is processed, we verify If the order total_price is more than 100€, a message will be published 
from Orders Module to Kafka topic <i>"voucher_queue"</i> and ingested with Voucher Module (It will trigger the internal custom command <i>"app:create-voucher"</i>) to create a voucher of 5€ 
<h4>3- Verify if vouchers are created for the marked as sent orders with total_price > 100 in Voucher module</h4>
* List created vouchers: http://localhost:8788/vouchers <br>

* Voucher Module Phpmyadmin url: http://localhost:8083/ (user: symfony , password: symfony )

<img src="https://i.ibb.co/TkffDJ6/terminal.jpg" alt="terminal"  />
<br>
<br><img src="https://i.ibb.co/8PXVs05/after-sending.jpg" alt="after-sending"  />
<br><br><img src="https://i.ibb.co/xCk1xGt/vouchers-list.jpg" alt="vouchers-list" />