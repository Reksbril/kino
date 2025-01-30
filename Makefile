start-dev:	build-dev
	mkdir -p build/posters

	docker run \
	-p 80:80 \
	-v $(shell pwd)/build/posters:/var/www/html/posters \
	-it kino-app-dev


start-prod:	build-prod
	docker run \
	-p 80:80 \
	-it kino-app-prod


build-dev:
	docker build --build-arg BUILD_TYPE=development -t kino-app-dev .


build-prod:
	docker build --build-arg BUILD_TYPE=production -t kino-app-prod .
