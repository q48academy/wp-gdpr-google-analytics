# defines variables
include .env.local

.PHONY: test clean release


install: composer.json composer.lock
	composer self-update
	composer validate
	composer install
	cp -pr vendor/q48academy/gdpr-privacy-by-design/src/Gdpr.php src/

test:
	./bin/install-wp-tests.sh wordpress_test root2 'root2' localhost latest true
	vendor/bin/phpunit tests --debug

release: clean
	mkdir release
	cp -pr src release/
	cp -pr var release/
	cp -pr etc release/
	cp -pr wp-gdpr-google-analytics.php ./release/

	# add htaccess to root directory
	cp -p .htaccess release/
	# add inxed.html to each directory
	cp index.html release/etc/
	cp index.html release/etc/.keys/
	cp index.html release/var/
	cp index.html release/var/log/
	cp index.html release/src/

	mv release wp-gdpr-google-analytics
	zip -r wp-gdpr-google-analytics.zip wp-gdpr-google-analytics


clean:
	rm -rf ./etc/.keys/*.key
	rm -rf ./var/log/*
	cp ./index.html ./var/log/
	rm -rf ./release
