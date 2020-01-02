# wp-gdpr-google-analytics

A **Wordpress Plugin** with **Privacy by Design** for **GDPR** and **Google Analytics**

## Is that even possible?


# In Your Wordpress

## Activate Plugin

## Add Action to Your Template

    <?php
        $wpGdprGoogleAnalytics = new Q48academy\Gdpr\GdprGoogleAnalytics();
        $foo = $wpGdprGoogleAnalytics->sendPageView();
    ?>
    
# Testing

Testing requires PHPUnit and wp-cli and follows the concept described here:

https://www.smashingmagazine.com/2017/12/automated-testing-wordpress-plugins-phpunit/

## Install PhpUnit via Composer

We are using PHPUnit form https://phpunit.de/

    composer global require phpunit/phpunit:^6.5

## Install WP CLI

We are using wp-cli form https://wp-cli.org/
    
    brew install wp-cli


## Setup Test instance

    cd wp-content/plugins/wp-gdpr-google-analytics
    bin/install-wp-tests.sh db_name db_user 'db_password' db_host latest

## Run Unit Test

    phpunit tests
