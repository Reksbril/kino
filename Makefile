start-dev: build-dev
	docker run \
	-p 80:80 \
	-v $(shell pwd)/src:/var/www/html \
	kino-app-dev 

start-prod:	build-prod
	docker run \
	-p 80:80 \
	-it kino-app-prod

build-dev:
	docker build --build-arg BUILD_TYPE=development -t kino-app-dev .

	mkdir -p $(shell pwd)/src/posters

	# Set up proper owner for /var/www/html/posters
	docker run \
	-v $(shell pwd)/src/posters:/var/www/html/posters \
	kino-app-dev \
	chown -R www-data:www-data /var/www/html/posters

build-prod:
	docker build --build-arg BUILD_TYPE=production -t kino-app-prod .
