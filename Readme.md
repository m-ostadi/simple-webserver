### How to run a single process webserver:

~~~
php -S localhost:8000 -t public
~~~



### How to run load-balancer:

step 1 :

~~~
php start_wokers.php
~~~

step 2 : 
~~~
php start_loadbalancer.php
~~~


for stopping workers: 
~~~
php stop_workers.php
~~~
