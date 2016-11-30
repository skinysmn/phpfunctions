namespace Bolt\Extensions\levin\filesize;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
