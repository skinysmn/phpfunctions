namespace Bolt\Extensions\levin\phpfunctions;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
